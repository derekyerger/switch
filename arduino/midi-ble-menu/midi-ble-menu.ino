#include <Arduino.h>
#include <SPI.h>
#include "Adafruit_BLE.h"
#include "Adafruit_BluefruitLE_SPI.h"
#include "Adafruit_BluefruitLE_UART.h"
#include "Adafruit_BLEMIDI.h"
#ifndef SAMD_SERIES
  #include <EEPROM.h>
#endif
#include "MIDIUSB.h"

#if SOFTWARE_SERIAL_AVAILABLE
  #include <SoftwareSerial.h>
#endif

#include "BluefruitConfig.h"

Adafruit_BluefruitLE_SPI ble(BLUEFRUIT_SPI_CS, BLUEFRUIT_SPI_IRQ, BLUEFRUIT_SPI_RST);

Adafruit_BLEMIDI midi(ble);

float adj[2] = {0x64 / 380, 0x64 / 380};
int last[2];
int val;

const int MAGIC = 17; /* To detect if flash has been initialized */
const int MENUFIXED = 2; /* Where to start counting tunable entries */

#define FACTORYRESET_ENABLE         1

#define CEIL 0x64 /* noteVal and lastVal are translated to a scale of 0 to this value */

#define DIVIDE 4
#define BASETIME 3000
#define BASEWIN 0
#define N_CEIL 30

const char *tunablesDesc[] = { "Operating mode",
                               "Channel",
                               "Minimum activation",
                               "Minimum activation 2",
                               "Delay between samples (ms)",
                               "Scale range",
                               "Start note"};
int tunables[] = { 1, 0, 8, 99, 2, 8, 60 };

/* Array mapped to legible pointer names */
int *mode = &tunables[0];
int *chan = &tunables[1];
int *minAct = &tunables[2];
int *minAct2 = &tunables[3];
int *sampdelay = &tunables[4];
int *scale = &tunables[5];
int *startnote = &tunables[6];

int adx = 0;
int programming;
bool serstatus = 0;
byte note = 0;
byte noteVal[2], lastVal[2];
int maxAct = 0;
bool toggle = 0;

int lastV[2];
int base[2];
unsigned long timeV;

/* Function prototypes */
void printMenu();
void purge();
int prompt();

unsigned long lastDetect = 0;
bool usbPresent = 0;

void detectUSB() {
  if (millis() - lastDetect < 5000) return;
  lastDetect = millis();
  unsigned long d = millis();
  midiEventPacket_t noteOn = {0x08, 0x80, 0, 0};
  MidiUSB.sendMIDI(noteOn);
  MidiUSB.flush();
  if (programming == -1 && Serial) {
    int t = (millis() - d);
    Serial.print("usb response time: ");
    Serial.println(t);
  }
  usbPresent = (millis() - d < 100);
}


void noteOn(byte channel, byte pitch, byte velocity) {
  midi.send(0x90 | channel, pitch, velocity); /* Bluetooth */
  
  if (!usbPresent) return;
  /* USB */
  midiEventPacket_t noteOn = {0x09, 0x90 | channel, pitch, velocity};
  MidiUSB.sendMIDI(noteOn);
  MidiUSB.flush();
}

void noteOff(byte channel, byte pitch, byte velocity) {
  midi.send(0x80 | channel, pitch, velocity); /* Bluetooth */
  
  if (!usbPresent) return;
  /* USB */
  midiEventPacket_t noteOff = {0x08, 0x80 | channel, pitch, velocity};
  MidiUSB.sendMIDI(noteOff);
  MidiUSB.flush();
}

void controlChange(byte channel, byte control, byte value) {
  midi.send(0xB0 | channel, control, value); /* Bluetooth */
  
  if (!usbPresent) return;
  midiEventPacket_t event = {0x0B, 0xB0 | channel, control, value};
  MidiUSB.sendMIDI(event);
  MidiUSB.flush();
}

void saveValues() { /* Write all tunables to EEPROM */
  int sp;
#ifndef SAMD_SERIES
  int adx = 2;
  for (sp = 0; sp < (sizeof(tunables)/sizeof(int)); sp++) {
    EEPROM.update(adx, tunables[sp] >> 8);
    EEPROM.update(adx+1, tunables[sp]); adx += 2;
  }
#endif
  for (sp = 0; sp < 2; sp++)
    adj[sp] = (float) CEIL / (float) (N_CEIL - base[sp]);
}

void setup() {
  programming = -99;
  pinMode(10, OUTPUT);
  digitalWrite(10, LOW);
  pinMode(22, OUTPUT);
  digitalWrite(22, LOW);
  Serial.begin(115200);
  Serial.setTimeout(60000);
  ble.begin(0);
  ble.echo(false);
#ifndef SAMD_SERIES
  adx = 0;
  val = (EEPROM.read(adx) << 8) + EEPROM.read(adx+1);
  if (val != MAGIC) {
    Serial.println("First run on this chip. Values will be initialized.");
    EEPROM.update(adx, MAGIC >> 8); EEPROM.update(adx+1, MAGIC);
    saveValues();
    ble.factoryReset();
    ble.sendCommandCheckOK("AT+GAPDEVNAME=midipad 9");
    ble.sendCommandCheckOK("AT+HWMODELED=DISABLE");
  } else {
    int sp;
    adx += 2;
    for (sp = 0; sp < (sizeof(tunables)/sizeof(int)); sp++) {
      tunables[sp] = (EEPROM.read(adx) << 8) + EEPROM.read(adx+1);
      adx += 2;
    }
    for (sp = 0; sp < 2; sp++)
      adj[sp] = (float) CEIL / (float) (N_CEIL - base[sp]);

    Serial.println("Stored values have been loaded.");
    Serial.println(sp);
  }
#else
  //ble.factoryReset();
  //ble.sendCommandCheckOK("AT+GAPDEVNAME=Vectis-MIDI 5");
  //ble.sendCommandCheckOK("AT+HWMODELED=DISABLE");
  saveValues();
#endif
  ble.sendCommandCheckOK("AT+GAPCONNECTABLE=1");
  ble.sendCommandCheckOK("AT+GAPSTARTADV");
  //delay(1000);
  midi.begin(true);
  ble.setMode(BLUEFRUIT_MODE_DATA);
  base[0] = analogRead(0) >> DIVIDE + BASEWIN;
  base[1] = analogRead(1) >> DIVIDE + BASEWIN;
}

void loop() {
  if (Serial && !serstatus) {
    serstatus = 1;
    programming = 99;
  } else if (!Serial && serstatus) {
    serstatus = 0;
    programming = -99;
  }
  bool curNote = 0;
  for (int i = 0; i < 2; i++) {
    int w = analogRead(i) >> DIVIDE;

    if (w != lastV[i]) {
      timeV = millis();
      lastV[i] = w;
    } else if (millis() - timeV > BASETIME) {
      if (lastV[0] != base[0] - BASEWIN || lastV[1] != base[1] - BASEWIN) {
        base[0] = lastV[0] + BASEWIN;
        base[1] = lastV[1] + BASEWIN;
        timeV = millis();
        Serial.print("Base: ");
        Serial.print(base[0]);
        Serial.print(' ');
        Serial.println(base[1]);
      }
    }

    int a = w - base[i];
    if (a < 0) a = 0;
    
    if (last[i] != a) {
      if (programming == -1 && Serial) {
        Serial.print(i);
        Serial.print(',');
        Serial.print(w);
        Serial.print(',');
        Serial.print(a);
        Serial.print(',');
      }
      last[i] = a;
      float f = (float) a;
      f *= adj[i];
      // f = pow(f, 1.5);
      // if (f > (float) CEIL) f = (float) CEIL;
  
      noteVal[i] = (byte) f;
      if (programming == -1) Serial.println(noteVal[i]);
    }
    detectUSB();
    if (*mode < 2) {
      if (noteVal[0] > *minAct || noteVal[1] > *minAct) {
        float diff2 = (float) noteVal[1] / (float) (noteVal[0] + noteVal[1]);

        int diff = (int) (diff2 * (*scale == 8 ? 12 : *scale));
        if (*scale == 8) {
          if (diff % 12 == 1) diff--;
          if (diff % 12 == 3) diff--;
          if (diff % 12 == 6) diff--;
          if (diff % 12 == 8) diff--;
          if (diff % 12 == 10) diff--;
        }
        int pitch = *startnote + diff;
        if (!note || (*mode == 1 && pitch != note)) {
          if (note) {
            if (programming == -1 && Serial) Serial.print("off: ");
            if (programming == -1 && Serial) Serial.println(note);
            noteOff(*chan, note, 0x64);
          }
          if (programming == -1 && Serial) Serial.print("on: ");
          if (programming == -1 && Serial) Serial.println(pitch);
          note = pitch;
          noteOn(*chan, note, 0x64);
        }
        maxAct = -1;
      } else {
        if (maxAct >= 0) maxAct = max(maxAct, max(noteVal[0], noteVal[1]));
        if (noteVal[0] == 0 && noteVal[1] == 0) {
          if (maxAct > *minAct2 && maxAct < *minAct) {
            toggle ^= 1;
            if (toggle) noteOn(*chan, 48, 0x64);
            else noteOff(*chan, 48, 0x64);
          }
          maxAct = 0;
        }

        if (note) {
          if (programming == -1 && Serial) Serial.print("off: ");
          if (programming == -1 && Serial) Serial.println(note);
          noteOff(*chan, note, 0x64);
          note = 0;
        }
      }
    } else if (*mode == 2) {
      for (int j = 0; j < 2; j++) {
        if (noteVal[j] != lastVal[j]) {
          lastVal[j] = noteVal[j];
          if (programming == -1 && Serial) {
            Serial.print("update channel ");
            Serial.print(j);
            Serial.print(" to ");
            Serial.println(noteVal[j]);
          }
          controlChange(*chan, j, noteVal[j]);
        }
      }
    }
  }
  delay(*sampdelay);
  if (programming >= 0) { /* Menu mode */
    switch (programming) {
      case 99:
        purge();
        printMenu();
        programming = -99;
        break;
        
      case 1:
        saveValues();
        Serial.print("\033[2J\033[0;0H");
        Serial.println("sensor = 0 or 1\r\nrawval = ADC in 2^10\r\nadjustedval = Adjusted for floor\r\noutput = scale to 100");
        Serial.println("sensor,rawval,adjustedval,output");
        programming = -1;
        break;
        
      default:
        purge();
        Serial.print("\r\n Enter new value for '");
        Serial.print(tunablesDesc[programming-MENUFIXED]);
        Serial.print("': ");
        tunables[programming-MENUFIXED] = prompt();
        programming = 99;
        break;      
    }
  }
  if (programming == -99 && Serial.available() > 0) {
    val = Serial.read() - 48;
    if ((val < 1) || (val > (MENUFIXED+sizeof(tunables)/sizeof(int)))) {
      if (val >= 17 && val <= 23) {
        noteOff(*chan, val - 17 + 48, 0x64);
      } else if (val >= 49 && val <= 55) {
        noteOn(*chan, val - 49 + 48, 0x64);
      } else {
        Serial.println("\r\n Invalid choice.");
        delay(1000);
        printMenu();
      }
    } else programming = val;
  }
  if (programming == -1 && Serial.available() > 0) {
    programming = 99;
  }
  while (ble.available()) {
    int c = ble.read();
    switch (c - 48) {
      case 0:
        ble.println("Changed mode to single note, one octave, major.");
        *mode = 0;
        *scale = 8;
        break;

      case 1:
        ble.println("Changed mode to multi note, one octave, major.");
        *mode = 1;
        *scale = 8;
        break;

      case 2:
        ble.println("Changed mode to multi note, one octave.");
        *mode = 1;
        *scale = 12;
        break;

      case 3:
        ble.println("Changed mode to multi note, two octaves.");
        *mode = 1;
        *scale = 24;
        break;

      case 4:
        ble.println("Changed mode to multi note, three octaves.");
        *mode = 1;
        *scale = 36;
        break;
    }
  }
}

void printMenu() {
  Serial.println("\033[2J\033[0;0H");
  Serial.println(" \033[32;1mmidipad\033[34m\r\n Alternate Devices - https://altdevs.net\033[0m\r\n");
  Serial.println(" \033[1m1.\033[0m Save values and show sensors\r\n");
  Serial.println(" \033[1mTunable items:\033[0m"); 
  int ch = MENUFIXED;
  int sp;
  for (sp = 0; sp < (sizeof(tunables)/sizeof(int)); sp++) {
    Serial.print(" \033[1m");
    Serial.write((ch++) + 48);
    Serial.print(".\033[0m ");
    Serial.print(tunablesDesc[sp]);
    Serial.print(" = ");
    Serial.println(tunables[sp]);
  }
  Serial.print("\n Enter a choice: \r\n\r\n");
}

void purge() {
  while (Serial.available() > 0)
    int val = Serial.read();
}

int prompt() {
  purge();
  Serial.print("\r\n Enter a new value: ");
  return Serial.parseInt();
}
