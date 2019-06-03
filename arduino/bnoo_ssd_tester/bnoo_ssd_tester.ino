#include <SPI.h>
#include <Wire.h>

#define USE_DISPLAY

#ifdef USE_DISPLAY
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include "altdevslogo.h"
#endif

#include <Adafruit_Sensor.h>
#include <Adafruit_BNO055.h>
#include <utility/imumaths.h>
#include <EEPROM.h>
#include <Keyboard.h>

const int MAGIC = 25396; /* To detect if flash has been initialized */
const int STRBUF = 48;  /* Buffer size for programming string */
const int MENUFIXED = 4; /* Where to start counting tunable entries */

const char *tunablesDesc[] = { "Linear soft motion",
                               "Linear hard motion",
                               "Angular soft motion",
                               "Angular hard motion",
                               "Linear stability threshold percent",
                               "Angular stability threshold",
                               "Sample interval (ms)",
                               "Stable time (ms)" };
int tunables[] = { 2, 4, 50, 100, 25, 25, 100, 1000 };

int *linSoft = &tunables[0];
int *linHard = &tunables[1];
int *angSoft = &tunables[2];
int *angHard = &tunables[3];
int *stable = &tunables[4];
int *stable2 = &tunables[5];
int *samp = &tunables[6];
int *stableTime = &tunables[7];


#ifdef USE_DISPLAY
#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 64 // OLED display height, in pixels
#define OLED_RESET     4 // Reset pin # (or -1 if sharing Arduino reset pin)
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);
#define DISPVCC        13          // OLED power
#endif

Adafruit_BNO055 bno = Adafruit_BNO055(55);

byte lastMode = 1;


void displayPowerOn() {
#ifdef USE_DISPLAY
  if (lastMode == 1) {
    digitalWrite(DISPVCC,LOW);
    display.begin(SSD1306_SWITCHCAPVCC, 0x3C);
    display.clearDisplay();
    display.drawBitmap(
      (display.width()  - LOGO_WIDTH ) / 2,
      16,
      logo_bmp, LOGO_WIDTH, LOGO_HEIGHT, 1);
    display.setTextSize(1);
    display.setTextColor(WHITE);
    display.setCursor(14,3);
    display.print(F("Alternate Devices"));
    display.display();
  }
#endif
  lastMode = 0;
}

void displayPowerOff() {
#ifdef USE_DISPLAY
  digitalWrite(DISPVCC,HIGH);
  display.clearDisplay();
  display.display();
#endif
  lastMode = 0;
}

char pString[STRBUF] = ""; /* Memory buffer for programming string */
char nOp[3] = ";;";  /* No operation. Returned from fetchImpulse() if
                        no string is found for the desired trigger */

int val;
int adx = 0;
int programming;
boolean debug;

/* Function prototypes */
void printMenu();
void purge();
int prompt();
char* fetchImpulse(int sensor, int impulse);
void parseCmd(char* hidSequence);

void saveValues() { /* Write all tunables to EEPROM */
  int adx = 2;
  int sp;
  for (sp = 0; sp < (sizeof(tunables)/sizeof(int)); sp++) {
    EEPROM.update(adx, tunables[sp] >> 8);
    EEPROM.update(adx+1, tunables[sp]); adx += 2;
  }
  sp = 0;
  while (pString[sp] != 0) EEPROM.update(adx++, pString[sp++]);
  EEPROM.update(adx++, 0);
}

void setup(void) {
  programming = 99;
  Serial.begin(115200);
  displayPowerOn();
  
  while (!Serial) { ; }
  adx = 0;
  val = (EEPROM.read(adx) << 8) + EEPROM.read(adx+1);
  if (val != MAGIC) {
    Serial.println(F("First run on this chip. Values will be initialized."));
    EEPROM.update(adx, MAGIC >> 8); EEPROM.update(adx+1, MAGIC);
    saveValues();
  } else {
    int sp;
    adx += 2;
    for (sp = 0; sp < (sizeof(tunables)/sizeof(int)); sp++) {
      tunables[sp] = (EEPROM.read(adx) << 8) + EEPROM.read(adx+1);
      adx += 2;
    }

    sp = 0;
    while ((pString[sp++] = EEPROM.read(adx++)) != 0) { ; }
    Serial.println(F("Stored values have been loaded."));
    Serial.println(sp);
  }
  Serial.setTimeout(60000);

  /* Initialise the sensor */
  if (!bno.begin())
  {
    /* There was a problem detecting the BNO055 ... check your connections */
    Serial.print(F("Ooops, no BNO055 detected ... Check your wiring or I2C ADDR!"));
    while (1);
  }
  
  delay(1000);

}

unsigned long lastMovement;

void printEvent(sensors_event_t* event, Print& pobj);

void compIMU(char* axis, byte* impulse, int* soft, int* hard, char eval, float val) {
  static float m;
  if (val == -1) {
    m = 0;
    return;
  }

  if (abs(val) > *soft && abs(val) > m) {
    *axis = eval + (val < 0) * 32;
    m = abs(val);
  }

  if (abs(val) > *hard) *impulse = 1;

}

void getIMUevent(char* axis, byte* impulse, sensors_event_t* linear, sensors_event_t* angular) {
    
  /* First priority: angular accel. Prioritize the greatest magnitude */
  compIMU(NULL, NULL, NULL, NULL, NULL, -1);
  compIMU(axis, impulse, angSoft, angHard, 'A', angular->gyro.x);
  compIMU(axis, impulse, angSoft, angHard, 'B', angular->gyro.y);
  compIMU(axis, impulse, angSoft, angHard, 'C', angular->gyro.z);

  if (*axis == ' ') { /* Second priority: linear accel */
    compIMU(NULL, NULL, NULL, NULL, NULL, -1);
    compIMU(axis, impulse, linSoft, linHard, 'X', linear->acceleration.x);
    compIMU(axis, impulse, linSoft, linHard, 'Y', linear->acceleration.y);
    compIMU(axis, impulse, linSoft, linHard, 'Z', linear->acceleration.z);
  }

}

void loop(void) {
  sensors_event_t orientationData , angVelocityData , linearAccelData;
  bno.getEvent(&orientationData, Adafruit_BNO055::VECTOR_EULER);
  bno.getEvent(&angVelocityData, Adafruit_BNO055::VECTOR_GYROSCOPE);
  bno.getEvent(&linearAccelData, Adafruit_BNO055::VECTOR_LINEARACCEL);
  if (programming >= 0) { /* Menu mode */
    switch (programming) {
      case 99:
        purge();
        printMenu();
        while (Serial.available() == 0) { 
          Serial.print(orientationData.orientation.x);
          Serial.print(' ');
          Serial.print(orientationData.orientation.y);
          Serial.print(' ');
          Serial.print(orientationData.orientation.z);
          Serial.print(' ');
          Serial.print("   \r ");
          delay(*samp);
          bno.getEvent(&orientationData, Adafruit_BNO055::VECTOR_EULER);
          bno.getEvent(&angVelocityData, Adafruit_BNO055::VECTOR_GYROSCOPE);
          bno.getEvent(&linearAccelData, Adafruit_BNO055::VECTOR_LINEARACCEL);
        }
        val = Serial.read() - 48;
        if ((val < 1) || (val > (MENUFIXED+sizeof(tunables)/sizeof(int)))) {
          Serial.println(F("\r\n Invalid choice."));
          delay(1000);
          printMenu();
        } else programming = val;
        break;
        
      case 1:
        purge();
        Serial.print(F("\r\n The current string is: "));
        Serial.println(pString);
        Serial.print(F("\r\n Enter a new string: "));
        Serial.readStringUntil('\r').toCharArray(pString, STRBUF);
        programming = 99;
        break;
        
      case 2:
        saveValues();
        Serial.print(F("\033[2J\033[0;0H"));
        Keyboard.begin();
        programming = -1;
        debug = false;
        break;
        
      case 3:
        Serial.println(F("\r\n Press any key to return to the menu."));
        programming = -1;
        debug = true;
        break;

      default:
        purge();
        Serial.print(F("\r\n Enter new value for '"));
        Serial.print(tunablesDesc[programming-MENUFIXED]);
        Serial.print(F("': "));
        tunables[programming-MENUFIXED] = prompt();
        programming = 99;
        break;      
    }
  } else { /* Runtime/sensing mode */

    char axis = ' ';
    byte impulse = 0;

    getIMUevent(&axis, &impulse, &linearAccelData, &angVelocityData);

    /* Don't allow a judgment to be made if we're moving about */
    if (axis != ' ' && (millis() - lastMovement) > *stableTime) {
      Serial.println();
      Serial.print(F("Event: "));
      Serial.print(axis);
      Serial.println(impulse);
      printEvent(&orientationData, Serial);
      printEvent(&angVelocityData, Serial);
      printEvent(&linearAccelData, Serial);
#ifdef USE_DISPLAY
      display.clearDisplay();
      display.setCursor(0,0);
      display.print(F("Event: "));
      display.print(axis);
      display.println(impulse);
      display.println();
      printEvent(&orientationData, display);
      printEvent(&angVelocityData, display);
      printEvent(&linearAccelData, display);
      display.display();
#endif
    } else {
#ifdef USE_DISPLAY
      for (int y = 8; y < 24; y++)
        display.drawFastHLine(0, y, 128, 0);
      display.setCursor(0,8);
      if (millis() - lastMovement > *stableTime) {
        display.print("stable for ");
        display.print(millis() - lastMovement);
        display.println("ms");
      } else display.setCursor(0,16);
      printEvent(&orientationData, display);
      display.display();  
#endif
    }

    if ((abs(linearAccelData.acceleration.x)
      + abs(linearAccelData.acceleration.y)
      + abs(linearAccelData.acceleration.z))*100 > *stable) {
      lastMovement = millis();
    }
    
    if ((abs(angVelocityData.gyro.x)
      + abs(angVelocityData.gyro.y)
      + abs(angVelocityData.gyro.z)) > *stable2) {
      lastMovement = millis();
    }
    
    delay(*samp);
    if (Serial.available() > 0) { /* Break to menu on input */
      Keyboard.releaseAll();
      Keyboard.end();
      programming = 99;
    }
  }
}

void printEvent(sensors_event_t* event, Print& pobj) {
  double x = -1000000, y = -1000000 , z = -1000000; //dumb values, easy to spot problem
  if (event->type == SENSOR_TYPE_ACCELEROMETER) {
    x = event->acceleration.x;
    y = event->acceleration.y;
    z = event->acceleration.z;
  }
  else if (event->type == SENSOR_TYPE_ORIENTATION) {
    x = event->orientation.x;
    y = event->orientation.y;
    z = event->orientation.z;
  }
  else if (event->type == SENSOR_TYPE_MAGNETIC_FIELD) {
    x = event->magnetic.x;
    y = event->magnetic.y;
    z = event->magnetic.z;
  }
  else if ((event->type == SENSOR_TYPE_GYROSCOPE) || (event->type == SENSOR_TYPE_ROTATION_VECTOR)) {
    x = event->gyro.x;
    y = event->gyro.y;
    z = event->gyro.z;
  }

  pobj.print(x);
  pobj.print(" ");
  pobj.print(y);
  pobj.print(" ");
  pobj.println(z);
}

void printMenu() {
  Serial.println(F("\033[2J\033[0;0H"));
  Serial.println(F(" \033[1m1.\033[0m Enter programming code"));
  Serial.println(F(" \033[1m2.\033[0m Save values and run"));
  Serial.println(F(" \033[1m3.\033[0m Enter debug mode\r\n")); 
  Serial.println(F(" \033[1mTunable items:\033[0m")); 
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
  Serial.print(F("\r\n Enter a new value: "));
  return Serial.parseInt();
}

char* fetchImpulse(int sensorMask, int impulse) { /* Get substring */
  int sp = 0;
  int mo = 0;
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
        break;
      case '~':
        Keyboard.press(KEY_ESC);
        break;
      case '_':
        Keyboard.press(KEY_BACKSPACE);
        break;
      case '!':
        Keyboard.press(KEY_TAB);
        break;
      
      /* Special functions: exit */
      case '`':
        Keyboard.releaseAll();
        Keyboard.end();
        setup();
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

