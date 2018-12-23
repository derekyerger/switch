/* Copyright (c) 2016-2018 by Derek Yerger. All Rights Reserved. Patent pending. */

#include <EEPROM.h>
#include <Keyboard.h>
#include <Arduino.h>
#include <SPI.h>
#include <LowPower.h>

#if SOFTWARE_SERIAL_AVAILABLE
#include <SoftwareSerial.h>
#endif

void (*resetFunc) (void) = 0;

const int MAGIC = 12; /* To detect if flash has been initialized */
const int STRBUF = 512;  /* Buffer size for programming string */
const int SAMP = 50;    /* For array allocation */
const int MAXSENS = 2;   /* For uarray allocation */

typedef struct {
  char *name;
  int min;
  int max;
  char *desc;
} TINFO;

const TINFO TDESC[] = {
  { "Sensor count", 1, 2, "The number of sensors the device will read from" },
  { "Hard press", 0, 255, "The pressure required to trigger a hard press" },
  { "Soft press", 0, 255, "The pressure required to trigger a soft press" },
  { "Long press", 1, 10000, "The contact time (in milliseconds) required to trigger a long press" },
  { "Sample interval", 1, 9, "The time (in milliseconds) between each sample" },
  { "Settle time", 1, 255, "The number of samples to continue reading before making a verdict" },
  { "Debounce time", 0, 10000, "How long to wait (in milliseconds) after performing an action, before resuming sensing" },
  { "Sample averaging window", 1, SAMP, "The number of past samples to include in the averaging function" },
  { "Pressure bias", 10, 100, "During auto-calibration, keep soft and hard presses separated by this delta" },
  { "Minimum group", 5, 50, "Minimum number of hard or soft presses required to auto-calibrate" },
  { "Enable adjust", 0, 1, "Enable auto-calibration" },
  { "Bluetooth", 0, 1, "Enable or disable bluetooth" },
  { "Turn off WiFi at battery level", 0, 100, "What battery level is required for WiFi management" }
};


int tunables[] = { 2, 145, 125, 400, 2, 50, 100, 50, 20, 15, 0, 0, 100 };

/* Array mapped to legible pointer names */
int *numSensors = &tunables[0];
int *hardP = &tunables[1];
int *softP = &tunables[2];
int *longP = &tunables[3];
int *sampleDelay = &tunables[4];
int *settleTime = &tunables[5];
int *debounce = &tunables[6];
int *avgWin = &tunables[7];
int *biasP = &tunables[8];
int *minGrp = &tunables[9];
int *enAdj = &tunables[10];
int *toBLE = &tunables[11];
int *rpiCutoff = &tunables[12];

byte debugging = 0;
char db[20];

char pString[STRBUF] = ""; /* Memory buffer for programming string */
char nOp[3] = ";;";  /* No operation. Returned from fetchImpulse() if
                        no string is found for the desired trigger */
int val;
byte lastIf;
int batlvl;
long lastBat = -1;
byte powerSaving = 0;
long powerSavingTime = 0;

int adx = 0;
boolean debug;
int impulse;
byte sensorMask;
byte readImpulse;
boolean captureS1 = false;

/* Linked list averaging mechanism */
struct calibrLL {
  calibrLL* next;
  byte val;
};

const int CLLMAX = 100;
calibrLL cLL[CLLMAX];
calibrLL* cLLfirst = &cLL[0];
byte cLLptr;

void insertLL(byte val); /* Insert sorted replacing oldest */
void updateCalibration(); /* Update soft/hard values based on entries */

struct clist {
  byte count;
  byte median;
  calibrLL *first;
};

/* Array buffers for averaging and time tracking */
unsigned long lastImpulse[6];
byte impulseBuffer[MAXSENS][SAMP];
byte impulseP[MAXSENS];

/* Function prototypes */
void printMenu();
void purge();
int prompt();
char* fetchImpulse(byte sensor, byte impulse);
void parseCmd(char* hidSequence);
void clearS(byte sensor);
byte avgS(byte sensor);

/* ===== Main setup, loop, supporting functions ===== */

void saveValues() { /* Write all tunables to EEPROM */
  int adx = 2;
  int sp;
  for (sp = 0; sp < (sizeof(tunables) / sizeof(int)); sp++) {
    EEPROM.update(adx, tunables[sp] >> 8);
    EEPROM.update(adx + 1, tunables[sp]); adx += 2;
  }
  sp = 0;
  while (pString[sp] != 0) EEPROM.update(adx++, pString[sp++]);
  EEPROM.update(adx++, 0);
}

void setupIface() {
  setupIface(0);
}

void setupIface(byte init) {
  if (!init && lastIf == *toBLE) return;
  lastIf = *toBLE;
  if (!init && !lastIf) Keyboard.end();
  if (*toBLE) {
    digitalWrite(6, LOW);
    digitalWrite(12, LOW);
    delay(150);
    digitalWrite(12, HIGH);
    delay(150);
    digitalWrite(12, LOW);
    delay(150);
    digitalWrite(12, HIGH);
    delay(550);
    digitalWrite(6, HIGH);
  } else {
    digitalWrite(5, LOW);
    Keyboard.begin();
    digitalWrite(12, LOW);
    delay(150);
    digitalWrite(12, HIGH);
    delay(850);
    digitalWrite(5, HIGH);
  }
}

void setupLowPower() {
  if (!lastIf) Keyboard.end();
}

void setup() {
  pinMode(5, OUTPUT);
  pinMode(6, OUTPUT);
  pinMode(12, OUTPUT);
  pinMode(22, OUTPUT);
  digitalWrite(5, HIGH);
  digitalWrite(6, HIGH);
  digitalWrite(12, HIGH);
  digitalWrite(22, LOW); /* Any RPi will stay off */

  Serial.begin(9600); /* Side channel control */
  adx = 0;
  val = (EEPROM.read(adx) << 8) + EEPROM.read(adx + 1);
  byte resetCt = 0;
  while (analogRead(0) > 400) {
    if (resetCt++ > 6) break;
    digitalWrite(12, LOW);
    delay(150);
    digitalWrite(12, HIGH);
    delay(350);
  }
  if (val != MAGIC || resetCt > 6) {
    EEPROM.update(adx, MAGIC >> 8); EEPROM.update(adx + 1, MAGIC);
    saveValues();
  } else {
    int sp;
    adx += 2;
    for (sp = 0; sp < (sizeof(tunables) / sizeof(int)); sp++) {
      tunables[sp] = (EEPROM.read(adx) << 8) + EEPROM.read(adx + 1);
      adx += 2;
    }

    sp = 0;
    while ((pString[sp++] = EEPROM.read(adx++)) != 0);
  }

  Serial.setTimeout(60000);
  for (byte p = 0; p < CLLMAX - 1; p++) cLL[p].next = &cLL[p + 1];
  lastIf = !*toBLE;
  setupIface(1);
}

void loop() {
  if (readImpulse > 1) readImpulse--;
  if (readImpulse == 1) { /* This takes action after release */
    readImpulse = 0;
    if (sensorMask) {
      char* p = fetchImpulse(sensorMask, impulse);
      Serial.print('>');
      Serial.print(sensorMask);
      Serial.print(impulse);
      char* q = p;
      do {
        if (*q == '\\') q++;
        Serial.print(*q);
      } while (*(++q) != ';');
      Serial.print("\n");
      for (int s = 0; s < *numSensors; s++) clearS(s);

      if (captureS1) {
        captureS1 = false;
        goto skip;
      }
      if (*toBLE) parseBtCmd(p);
      else parseCmd(p);
    }

    delay(*debounce);
  }
  for (byte s = 0; s < *numSensors; s++) { /* Main sensing */
    byte v = analogRead(s) >> 2;
    if ((lastImpulse[s] != 0) && (v < *softP)) { /* Ending */
      sensorMask |= (1 << s);
      byte avg = avgS(s);

      if (millis() - lastImpulse[s] >= *longP) {
        impulse |= 2;
      } else if (avg >= *hardP) impulse |= 1;

      readImpulse = *settleTime;
      insertLL(avg);
      updateCalibration();
      clearS(s);
      powerSavingTime = millis();
    } else if ((lastImpulse[s] == 0) && (v >= *softP)) { /* Starting */
      impulse = 0;
      sensorMask = 0;
      lastImpulse[s] = millis();
      impulseBuffer[s][impulseP[s]] = v;
      impulseP[s] = (impulseP[s] + 1) % *avgWin;
      if (powerSaving) {
        /* Make sure we wake the interface */
        setupIface(1);
        captureS1 = true; /* If waking up, ignore next */
        powerSaving = false;
        powerSavingTime = millis();
      }
    } else if (lastImpulse[s] != 0) { /* During event, sample */
      impulseBuffer[s][impulseP[s]] = v;
      impulseP[s] = (impulseP[s] + 1) % *avgWin;
    } else if (false) {
      if (millis() - powerSavingTime > 600000 ) {
        if (!powerSaving) setupLowPower();
        powerSaving = true;
        LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF); /* Save power */
      }
    } else if (powerSaving) {
      setupIface(1);
      captureS1 = true; /* If waking up, ignore next */
      powerSaving = false;
    }
  }
  delay(*sampleDelay);
skip:
  if (Serial.available() > 0) { /* Side channel command */
    val = Serial.read();
    switch (val) {
      case 1: /* Read state */
        delay(100);
        int sp;
        for (sp = 0; sp < (sizeof(tunables) / sizeof(int)); sp++) {
          Serial.print(tunables[sp]);
          Serial.print(",");
        }
        Serial.print(pString);
        Serial.print("\n");
        break;

      case 3: /* Temporary program */
        Serial.readStringUntil('\n').toCharArray(pString, STRBUF);
        break;

      case 4: /* Save to memory */
        saveValues();
        break;

      case 5: /* Capture next */
        captureS1 = true;
        break;

      case 6: /* Cancel capture */
        captureS1 = false;
        break;

      case 7: /* Set tunable */
        sp = Serial.readStringUntil(',').toInt();
        tunables[sp] = Serial.readStringUntil('\n').toInt();
        if (&tunables[sp] == toBLE) setupIface();
        Serial.print("\n");
        break;

      case 8: /* Get battery */
        Serial.print(batlvl);
        Serial.print("%");
        if (analogRead(A5) > 650) Serial.print(", charging");
        Serial.print("\n");
        break;

      case 9: /* Get power source */
        Serial.print(analogRead(A5));
        Serial.print("\n");
        break;

      case 11: /* Set debugging */
        debugging = (byte) Serial.readStringUntil('\n').toInt();
        break;

      case 12: /* Reset */
        resetFunc();
        break;

      case 14: /* Get sensors */
        Serial.print(analogRead(A0));
        Serial.print(",");
        Serial.print(analogRead(A1));
        Serial.print("\n");
        break;
      
      case 15: /* Delete bonding info */
        //ble.sendCommandCheckOK("AT+GAPDELBONDS");
        break;

      case 16: /* Grab device info table */
        sp = sizeof(TDESC) / sizeof(TINFO);
        for (int i = 0; i < sp; i++) {
            TINFO j = TDESC[i];
            Serial.print(j.name);
            Serial.print(",");
            Serial.print(tunables[i]);
            Serial.print(",");
            Serial.print(j.min);
            Serial.print(",");
            Serial.print(j.max);
            Serial.print(",");
            Serial.print(j.desc);
            Serial.print(";");
        }
        Serial.print("\n");
        break;
    }
  }

  if (lastBat != millis() >> 16) {
    lastBat = millis() >> 16;
    batlvl = analogRead(A9) - 496;
    if (batlvl < 0) batlvl = 0;
    if (batlvl > 100) batlvl = 100;
    if (batlvl < 20) {
      digitalWrite(5, LOW);
      if ((millis() >> 16) % 16 == 0) {
        for (val = 0; val < 3; val++) {
          digitalWrite(12, LOW);
          delay(100);
          digitalWrite(12, HIGH);
          delay(200);
        }
      } else delay(900);
      digitalWrite(5, HIGH);
    }
  }

}

/* ===== Averaging, calibration ===== */

void clearS(byte sensor) { /* Clear out averaging buffer */
  lastImpulse[sensor] = 0;
  impulseP[sensor] = 0;
  for (byte c = 0; c < SAMP; c++) impulseBuffer[sensor][c] = 0;
}

byte avgS(byte sensor) { /* Compute average for gathered samples */
  long ttl = 0;
  byte t = 0;
  for (byte c = 0; c < SAMP; c++)
    if (impulseBuffer[sensor][c] != 0) {
      ttl += impulseBuffer[sensor][c];
      t++;
    }
  return ttl / t;
}

/* Insert sorted replacing oldest */
void insertLL(byte val) {
  byte fl = 32;
  /* Delete old node */
  if (debugging) {
    sprintf(db, "c%d,%d,%d,", cLL[cLLptr].val, val, cLLptr);
    Serial.print(db);
  }
  if (&cLL[cLLptr] == cLLfirst) {
    /* Item to remove is first item */
    cLLfirst = cLL[cLLptr].next;
  } else if (&cLL[cLLptr].next == NULL) {
    /* Item to remove is last item */
    fl |= 1;
    for (calibrLL* p = cLLfirst; p != NULL; p = p->next)
      if (p->next == &cLL[cLLptr]) {
        p->next == NULL;
        break;
      }
  } else {
    /* Item to remove is in between */
    fl |= 2;
    for (calibrLL* p = cLLfirst; p != NULL; p = p->next)
      if (p->next == &cLL[cLLptr]) {
        p->next = cLL[cLLptr].next;
        break;
      }
  }

  /* Find place to insert */
  if (val < cLLfirst->val) {
    /* Insert at front */
    fl |= 4;
    cLL[cLLptr].val = val;
    cLL[cLLptr].next = cLLfirst;
    cLLfirst = &cLL[cLLptr];
  } else {
    calibrLL *p, *lp;
    for (p = cLLfirst; p != NULL; p = p->next) {
      if (p->val <= val && (p->next)->val > val) {
        fl |= 8;
        cLL[cLLptr].val = val;
        cLL[cLLptr].next = p->next;
        p->next = &cLL[cLLptr];
        break;
      }
      lp = p;
    }

    if (p == NULL) {
      /* Append to end */
      lp->next = &cLL[cLLptr];
      cLL[cLLptr].val = val;
      cLL[cLLptr].next = NULL;
      fl |= 16;
    }
  }
  cLLptr = (cLLptr + 1) % CLLMAX;
  if (debugging) {
    Serial.print(fl);
    Serial.print(";");
  }
}

void updateCalibration() {
  /* Count members of each group */
  clist gCt[2];
  gCt[1].count = 0;
  gCt[0].count = 0;
  byte v, x = 0, *inc = &v;
  byte divisor;

  if (*softP <= cLLfirst->val) {
    if (debugging) Serial.print("ss");
    gCt[0].first = cLLfirst;
    inc = &gCt[0].count;
  }
  for (calibrLL* p = cLLfirst; p != NULL; p = p->next) {
    if (debugging && p->val > 0) {
      if (p->val < 16) Serial.print("0");
      Serial.print(p->val, HEX);
    }
    if (p->next != NULL) {
      if (*softP > p->val && *softP <= p->next->val) {
        if (debugging) Serial.print("ss");
        gCt[0].first = p;
        inc = &gCt[0].count;
      }
      if (*hardP > p->val && *hardP <= p->next->val) {
        if (debugging) Serial.print("hh");
        gCt[1].first = p;
        inc = &gCt[1].count;
      }
    } else if (debugging && *hardP > p->val) if (debugging) Serial.print("hh");
    (*inc)++;
  }

  /* Medians */
  if (debugging) Serial.print(";");
  for (v = 0; v < 2; v++) {
    if (debugging) {
      Serial.print(gCt[v].count);
      Serial.print(",");
    }
    calibrLL* p = cLLfirst;
    if (gCt[v].count > *minGrp) {
      p = gCt[v].first;
      for (x = 0; x < (gCt[v].count / 2); x++) p = p->next;
      gCt[v].median = p->val;
      if (debugging) {
        Serial.print(gCt[v].median);
        Serial.print(",");
      }
    } else if (debugging) Serial.print(",");
  }
  if (debugging) Serial.print(";");

  if (*enAdj && gCt[0].count > *minGrp && gCt[1].count > *minGrp) {
    byte dta = (gCt[1].median - gCt[0].median) / 2;
    *hardP = gCt[1].median - dta;
    *softP = gCt[0].median - dta;
    if (*hardP - *softP < *biasP)
      *softP = *hardP - *biasP;
    if (debugging) {
      sprintf(db, "%d,%d", *softP, *hardP);
      Serial.print(db);
    }
  }
  if (debugging) Serial.print("\n");
}

/* ===== User Programming and Translation ===== */

char* fetchImpulse(byte sensorMask, byte impulse) { /* Get substring */
  byte mo = 0;
  char* cc = pString;
  do {
    switch (mo) {
      case 0:
        mo++;
        if (*cc - 48 != sensorMask) mo = 9;
        break;

      case 1:
        mo++;
        if (*cc - 48 != impulse) mo = 9;
        break;

      case 2:
        return cc;
        break;

      default:
        if (*cc == ';') mo = 0;
        break;
    }
  } while (*(++cc) != 0);
  return nOp;
}

void parseCmd(char* hidSequence) { /* Translate to keystrokes */
  do {
    switch (*hidSequence) {
      /* Modifiers */
      case '^':
        Keyboard.press(KEY_LEFT_CTRL);
        break;
      case '+':
        Keyboard.press(KEY_LEFT_SHIFT);
        break;
      case '%':
        Keyboard.press(KEY_LEFT_ALT);
        break;
      case '&':
        Keyboard.press(KEY_LEFT_GUI);
        break;
      case '|':
        Keyboard.press(KEY_RETURN);
        Keyboard.releaseAll();
        break;
      case '~':
        Keyboard.press(KEY_ESC);
        Keyboard.releaseAll();
        break;
      case '_':
        Keyboard.press(KEY_BACKSPACE);
        Keyboard.releaseAll();
        break;
      case '!':
        Keyboard.press(KEY_TAB);
        Keyboard.releaseAll();
        break;

      /* Special functions: delay */
      case '`':
        delay(250);
        break;
      case ';':
        break;

      /* Escaped chars and regular chars */
      case '\\':
        Keyboard.press(*(++hidSequence));
        Keyboard.releaseAll();
        break;
      default:
        int modify = 0;
        if ((*hidSequence >= 65) && (*hidSequence <= 90)) modify = 128;
        Keyboard.press(*hidSequence + modify);
        Keyboard.releaseAll();
        break;
    }
    delay(5);
  } while (*(++hidSequence) != ';');
  Keyboard.releaseAll();
}

void sendBt(byte* buf) {
  return;
}

void parseBtCmd(char* hidSequence) { /* Translate to keystrokes */
  byte buf[8] = {0, 0, 0, 0, 0, 0, 0, 0};
  byte bufPtr = 2;
  byte mod = 0, lastmod = 0, key = 0, skip = 0;
  do {
    key = 0;
    if (!skip) {
      switch (*hidSequence) {
        /* Modifiers */
        case '^': mod |= 1; continue;
        case '+': mod |= 2; continue;
        case '%': mod |= 4; continue;
        case '&': mod |= 8; continue;
        case '|': key = 0x28; break;
        case '~': key = 0x29; break;
        case '_': key = 0x2a; break;
        case '!': key = 0x2b; break;
        case '`': sendBt(buf); delay(250); continue;
      }
    }
    skip = 0;
    if (!key)
      switch (*hidSequence) {

        case '0': key = 0x27; break;
        case ' ': key = 0x2c; break;
        case '-': key = 0x2d; break;
        case '=': key = 0x2e; break;
        case '[': key = 0x2f; break;
        case ']': key = 0x30; break;
        case '\'': key = 0x34; break;
        case '`': key = 0x35; break;
        case ',': key = 0x36; break;
        case '.': key = 0x37; break;
        case '/': key = 0x38; break;

        case '!': key = 0x1e; mod |= 1; break;
        case '@': key = 0x1f; mod |= 1; break;
        case '#': key = 0x20; mod |= 1; break;
        case '$': key = 0x21; mod |= 1; break;
        case '%': key = 0x22; mod |= 1; break;
        case '^': key = 0x23; mod |= 1; break;
        case '&': key = 0x24; mod |= 1; break;
        case '*': key = 0x25; mod |= 1; break;
        case '(': key = 0x26; mod |= 1; break;
        case ')': key = 0x27; mod |= 1; break;

        case '_': key = 0x2d; mod |= 1; break;
        case '+': key = 0x2e; mod |= 1; break;
        case '{': key = 0x2f; mod |= 1; break;
        case '}': key = 0x30; mod |= 1; break;
        case '|': key = 0x31; mod |= 1; break;
        case ':': key = 0x33; mod |= 1; break;
        case '"': key = 0x34; mod |= 1; break;
        case '~': key = 0x35; mod |= 1; break;
        case '<': key = 0x36; mod |= 1; break;
        case '>': key = 0x37; mod |= 1; break;
        case '?': key = 0x38; mod |= 1; break;

        case ';': break;

        case '\\': skip = 1; continue;

        default:
          int modify = 0;
          if ((*hidSequence >= 65) && (*hidSequence <= 90)) modify = 85;
          else if ((*hidSequence >= 49) && (*hidSequence <= 57)) modify = 74;
          key = (*hidSequence - 93 + modify);
          break;
      }

    byte dupe = 0;
    byte j;
    if (key) for (j = 2; j < 8; j++) if (buf[j] == key) dupe = 1;

    if ((bufPtr > 2 && lastmod != mod) || bufPtr == 8 || dupe) {
      sendBt(buf);
      for (j = 0; j < 8; j++) buf[j] = 0;
      bufPtr = 2;
    }
    buf[0] = lastmod = mod;
    buf[bufPtr++] = key;
  } while (*(++hidSequence) != ';');
  if (bufPtr > 2) sendBt(buf);
}
/*
  ^ 128 KEY_LEFT_CTRL
  + 129 KEY_LEFT_SHIFT
  % 130 KEY_LEFT_ALT
  & 131 KEY_LEFT_GUI
  | 176 KEY_RETURN
  ~ 177 KEY_ESC
  _ 178 KEY_BACKSPACE
  ! 179 KEY_TAB
  A 193 KEY_CAPS_LOCK   -- -128 for this and below
  B 194 KEY_F1
  C 195 KEY_F2
  D 196 KEY_F3
  E 197 KEY_F4
  F 198 KEY_F5
  G 199 KEY_F6
  H 200 KEY_F7
  I 201 KEY_F8
  J 202 KEY_F9
  K 203 KEY_F10
  L 204 KEY_F11
  M 205 KEY_F12
  Q 209 KEY_INSERT
  R 210 KEY_HOME
  S 211 KEY_PAGE_UP
  T 212 KEY_DELETE
  U 213 KEY_END
  V 214 KEY_PAGE_DOWN
  W 215 KEY_RIGHT_ARROW
  X 216 KEY_LEFT_ARROW
  Y 217 KEY_DOWN_ARROW
  Z 218 KEY_UP_ARROW
*/
