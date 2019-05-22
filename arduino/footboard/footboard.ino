/* Copyright (c) 2016-2018 by Derek Yerger. All Rights Reserved. Patent pending. */

#include <EEPROM.h>
#include <Keyboard.h>
#include <Arduino.h>
#include <SPI.h>
#include <LowPower.h>
#include <avr/pgmspace.h>
#include "Adafruit_BLE.h"
#include "Adafruit_BluefruitLE_SPI.h"
#include "Adafruit_BluefruitLE_UART.h"
#include "Adafruit_BLEBattery.h"

#include "BluefruitConfig.h"

#if SOFTWARE_SERIAL_AVAILABLE
#include <SoftwareSerial.h>
#endif

void (*resetFunc) (void) = 0;

const int MAGIC = 24; /* To detect if flash has been initialized */
const int STRBUF = 512;  /* Buffer size for programming string */
const int SAMP = 50;    /* For array allocation */
const int MAXSENS = 2;   /* For uarray allocation */
const int MONITORINTERVAL = 100;

#include "device.h"

int tunables[] = { 2, 90, 75, 300, 1, 30, 0, 50, 20, 15, 0, 0, 900, 900, 0, 0, 150 };

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
int *sleepDelay = &tunables[13];
int *ratio[2] = { &tunables[14], &tunables[15] };
int *loadPressure = &tunables[16];

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
unsigned long powerSavingTime = 0;
unsigned long wifiSavingTime = 0;

int adx = 0;
boolean debug;
byte monitor;
unsigned long monitorTime;
byte impulse;
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
void dumpCapabilities();

/* ===== Main setup, loop, supporting functions ===== */
Adafruit_BluefruitLE_SPI ble(BLUEFRUIT_SPI_CS, BLUEFRUIT_SPI_IRQ, BLUEFRUIT_SPI_RST);
Adafruit_BLEBattery battery(ble);

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
    ble.echo(false);
    ble.sendCommandCheckOK("AT+BleHIDEn=On");
    ble.sendCommandCheckOK("AT+BleKeyboardEn=On");
    ble.sendCommandCheckOK("AT+GAPCONNECTABLE=1");
    ble.sendCommandCheckOK("AT+GAPSTARTADV");
    ble.sendCommandCheckOK("AT+BLEKEYBOARDCODE=00-00");
    ble.reset();
    battery.begin(true);
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
    ble.sendCommandCheckOK("AT+GAPCONNECTABLE=0");
    ble.sendCommandCheckOK("AT+GAPDISCONNECT");
    ble.sendCommandCheckOK("AT+GAPSTOPADV");
    digitalWrite(12, LOW);
    delay(150);
    digitalWrite(12, HIGH);
    delay(850);
    digitalWrite(5, HIGH);
  }
}

void setupLowPower() {
  if (!lastIf) Keyboard.end();
  ble.sendCommandCheckOK("AT+GAPCONNECTABLE=0");
  ble.sendCommandCheckOK("AT+GAPDISCONNECT");
  ble.sendCommandCheckOK("AT+GAPSTOPADV");
}

void setup() {
  pinMode(5, OUTPUT);
  pinMode(6, OUTPUT);
  pinMode(12, OUTPUT);
  pinMode(22, OUTPUT);
  digitalWrite(5, HIGH);
  digitalWrite(6, HIGH);
  digitalWrite(12, HIGH);
  digitalWrite(22, HIGH);
  Serial1.begin(115200); /* Side channel control */
  adx = 0;
  val = (EEPROM.read(adx) << 8) + EEPROM.read(adx + 1);
  ble.begin(0);
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
    ble.factoryReset();
    ble.println("AT+GAPDEVNAME=Accessible Input Device");
    ble.sendCommandCheckOK("AT+HWMODELED=DISABLE");
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

  if (EEPROM.read(1006) != 121) { /* Unique ID */
    while (analogRead(0) < 100) delay(150);
    delay(500);
    randomSeed(analogRead(0) + analogRead(1));
    EEPROM.update(1006, 121);
    for (adx = 1007; adx < 1024; adx++) {
      int c = random(62);
      if (c > 50) c -= 3;
      else if (c > 25) c += 39;
      else c += 97;
      EEPROM.update(adx, c);
    }
  }

  Serial1.setTimeout(60000);
  for (byte p = 0; p < CLLMAX - 1; p++) cLL[p].next = &cLL[p + 1];
  lastIf = !*toBLE;
  setupIface(1);
}

void loop() {
  int sp;
  if (readImpulse > 1) readImpulse--;
  if (readImpulse == 1) { /* This takes action after release */
    readImpulse = 0;
    if (sensorMask) {
      char* p = fetchImpulse(sensorMask, impulse);
      Serial1.print('>');
      Serial1.print(sensorMask);
      Serial1.print(impulse);
      char* q = p;
      do {
        if (*q == '\\') q++;
        Serial1.print(*q);
      } while (*(++q) != ';');
      Serial1.print("\n");
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
      if (monitor) {
        if (s == 0) Serial1.write( '@' );
        if (s == 1) Serial1.write( '#' );
        if (avg < 16) Serial1.print('0');
        Serial1.print(avg, HEX);
        Serial1.print('\n');
      }

      if (millis() - lastImpulse[s] >= *longP) {
        impulse = 2;
      } else if (avg >= *hardP) impulse |= 1;

      readImpulse = *settleTime;
      insertLL(avg);
      updateCalibration();
      clearS(s);
      powerSavingTime = millis();
    } else if ((lastImpulse[s] == 0) && (v >= *softP)) { /* Starting */
      if (monitor) {
        if (s == 0) Serial1.write( '$' );
        if (s == 1) Serial1.write( '%' );
        Serial1.print('\n');
      }
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
    } else if (analogRead(A5) < 650) {
      if (millis() - powerSavingTime > *sleepDelay * 1000 ) {
        if (!powerSaving) setupLowPower();
        powerSaving = true;
        LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF); /* Save power */
      }
    } else if (powerSaving) {
      setupIface(1);
      captureS1 = true; /* If waking up, ignore next */
      powerSaving = false;
    }
    if (monitor && s == 0 && (millis() - monitorTime > MONITORINTERVAL)) monitor = 2;
    if (monitor == 2) {
      if (s == 0) Serial1.write( '!' );
      if (v < 16) Serial1.print('0');
      Serial1.print(v, HEX);
    }
  }
  if (monitor == 2) {
    Serial1.print('\n');
    monitorTime = millis();
    monitor = 1;
  }
  delay(*sampleDelay);
skip:
  if (Serial1.available() > 0) { /* Side channel command */
    val = Serial1.read();
    switch (val) {
      case 1: /* Read state */
        delay(100);
        for (sp = 0; sp < (sizeof(tunables) / sizeof(int)); sp++) {
          Serial1.print(tunables[sp]);
          Serial1.print(",");
        }
        Serial1.print(pString);
        Serial1.print("\n");
        break;

      case 3: /* Temporary program */
        Serial1.readStringUntil('\n').toCharArray(pString, STRBUF);
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
        sp = Serial1.readStringUntil(',').toInt();
        tunables[sp] = Serial1.readStringUntil('\n').toInt();
        if (&tunables[sp] == toBLE) setupIface();
        Serial1.print("\n");
        break;

      case 9: /* Get power source */
        Serial1.print(analogRead(A5));
        Serial1.print("\n");
        break;

      case 11: /* Set debugging */
        debugging = (byte) Serial1.readStringUntil('\n').toInt();
        break;

      case 12: /* Reset */
        resetFunc();
        break;

      case 14: /* Get sensors */
        Serial1.print(analogRead(A0));
        Serial1.print(",");
        Serial1.print(analogRead(A1));
        Serial1.print("\n");
        break;
      
      case 15: /* Delete bonding info */
        ble.sendCommandCheckOK("AT+GAPDELBONDS");
        break;

      case 16: /* Grab device info table */
        dumpCapabilities();
        break;

      case 17: /* Get device capability string */
      case 18: /* Or just the id */
        Serial1.print(F("v1.1-"));
        for (int adx = 1007; adx < 1024; adx++) Serial1.write(EEPROM.read(adx));
        if (val == 17) Serial1.print(F("-aid2-e9599daa"));
        Serial1.print("\n");
        break;
      
      case 19: /* Set monitor */
        monitor = (byte) Serial1.readStringUntil('\n').toInt();
        Serial1.print(*softP);
        Serial1.print(",");
        Serial1.print(*hardP);
        Serial1.print("\n");
        break;

      case 20: /* Collect baseline */
        for (sp = 0; sp < SAMP; sp++) {
          for (byte s = 0; s < *numSensors; s++) impulseBuffer[s][sp] = analogRead(s) >> 2;
          delay(10);
        }
        
        float nf = avgS(0);
        clearS(0);

        nf /= (float) avgS(1);
        clearS(1);

        nf *= 10000;
        *ratio[0] = (int) nf;
        while ((analogRead(0) >> 2) < *loadPressure) delay(10);
        for (sp = 0; sp < SAMP; sp++) {
          for (byte s = 0; s < *numSensors; s++) impulseBuffer[s][sp] = analogRead(s) >> 2;
          delay(10);
        }
        
        nf = avgS(0);
        clearS(0);

        nf /= (float) avgS(1);
        clearS(1);

        nf *= 10000;
        *ratio[1] = (int) nf;
        Serial1.print(*ratio[0]);
        Serial1.print(',');
        Serial1.print(*ratio[1]);
        Serial1.print('\n');
        break;
    }
  }

  if (lastBat != millis() >> 16) {
    lastBat = millis() >> 16;
    batlvl = analogRead(A9) - 496;
    if (batlvl < 0) batlvl = 0;
    if (batlvl > 100) batlvl = 100;
    battery.update(batlvl);
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

  if (analogRead(A5) >= 650) {
      wifiSavingTime = millis();
    digitalWrite(22, HIGH);
  } else {
    unsigned long cutoff = (unsigned long) *rpiCutoff;
      digitalWrite(22, (millis() - wifiSavingTime < cutoff * 1000));
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
    Serial1.print(db);
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
    Serial1.print(fl);
    Serial1.print(";");
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
    if (debugging) Serial1.print("ss");
    gCt[0].first = cLLfirst;
    inc = &gCt[0].count;
  }
  for (calibrLL* p = cLLfirst; p != NULL; p = p->next) {
    if (debugging && p->val > 0) {
      if (p->val < 16) Serial1.print("0");
      Serial1.print(p->val, HEX);
    }
    if (p->next != NULL) {
      if (*softP > p->val && *softP <= p->next->val) {
        if (debugging) Serial1.print("ss");
        gCt[0].first = p;
        inc = &gCt[0].count;
      }
      if (*hardP > p->val && *hardP <= p->next->val) {
        if (debugging) Serial1.print("hh");
        gCt[1].first = p;
        inc = &gCt[1].count;
      }
    } else if (debugging && *hardP > p->val) if (debugging) Serial1.print("hh");
    (*inc)++;
  }

  /* Medians */
  if (debugging) Serial1.print(";");
  for (v = 0; v < 2; v++) {
    if (debugging) {
      Serial1.print(gCt[v].count);
      Serial1.print(",");
    }
    calibrLL* p = cLLfirst;
    if (gCt[v].count > *minGrp) {
      p = gCt[v].first;
      for (x = 0; x < (gCt[v].count / 2); x++) p = p->next;
      gCt[v].median = p->val;
      if (debugging) {
        Serial1.print(gCt[v].median);
        Serial1.print(",");
      }
    } else if (debugging) Serial1.print(",");
  }
  if (debugging) Serial1.print(";");

  if (*enAdj && gCt[0].count > *minGrp && gCt[1].count > *minGrp) {
    byte dta = (gCt[1].median - gCt[0].median) / 2;
    *hardP = gCt[1].median - dta;
    *softP = gCt[0].median - dta;
    if (*hardP - *softP < *biasP)
      *softP = *hardP - *biasP;
    if (*softP < 1) *softP = 1;
    if (debugging) {
      sprintf(db, "%d,%d", *softP, *hardP);
      Serial1.print(db);
    }
    if (monitor) {
      Serial1.write( '^' );
      Serial1.print(*softP, HEX);
      Serial1.print(*hardP, HEX);
      Serial1.print('\n');
    }
  }
  if (debugging) Serial1.print("\n");
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
  char stbuf[46] = "AT+BLEKEYBOARDCODE=";
  char b[3];
  int c;
  for (c = 0; c < 8; c++) {
    sprintf(b, "%02x", buf[c]);
    strcat(stbuf, b);
    if (c < 7) strcat(stbuf, "-");
  }
  ble.println(stbuf);
  ble.waitForOK();
  ble.println("AT+BLEKEYBOARDCODE=00-00");
  ble.waitForOK();
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

void dumpCapabilities() {
  for (int i = 0; i < TCMAX; i++) {
      TINFO j;
      memcpy_P(&j, &TDESC[i], sizeof(j));
      char buffer[80];
      strncpy_P (buffer, j.name, 80);
      Serial1.print(buffer);
      Serial1.print(",");
      Serial1.print(tunables[i]);
      Serial1.print(",");
      Serial1.print(j.min);
      Serial1.print(",");
      Serial1.print(j.max);
      Serial1.print(",");
      strncpy_P (buffer, j.desc, 80);
      Serial1.print(buffer);
      Serial1.print(";");
  }
  Serial1.print("\n");
}
