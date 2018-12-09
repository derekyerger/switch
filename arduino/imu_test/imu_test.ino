#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BNO055.h>
#include <utility/imumaths.h>
#include <Keyboard.h>

Adafruit_BNO055 bno = Adafruit_BNO055(0x28);
  int w = 0;
  int a = 0;
  int s = 0;
  int d = 0;
  int t = 5;

void setup(void) 
{
  Serial.begin(9600);
  Serial.println("Orientation Sensor Test"); Serial.println("");
  
  /* Initialise the sensor */
  if(!bno.begin())
  {
    /* There was a problem detecting the BNO055 ... check your connections */
    Serial.print("Ooops, no BNO055 detected ... Check your wiring or I2C ADDR!");
    while(1);
  }
  
  delay(1000);
    
  bno.setExtCrystalUse(true);
}

void loop(void) 
{
  /* Get a new sensor event */ 
  sensors_event_t event; 
  bno.getEvent(&event);


  if (event.orientation.z > t) {
    if (!w) {
      w = 1;
      Keyboard.press('w');
      Serial.println("press w");
    }
  } else {
    if (w) {
      w = 0;
      Keyboard.release('w');
      Serial.println("press w");
    }
  }
  if (event.orientation.z < -t) {
    if (!s) {
      s = 1;
      Keyboard.press('s');
    }
  } else {
    if (s) {
      s = 0;
      Keyboard.release('s');
    }
  }
   if (event.orientation.y > t) {
    if (!a) {
      a = 1;
      Keyboard.press('a');
    }
  } else {
    if (a) {
      a = 0;
      Keyboard.release('a');
    }
  }
  if (event.orientation.y < -t) {
    if (!d) {
      d = 1;
      Keyboard.press('d');
    }
  } else {
    if (d) {
      d = 0;
      Keyboard.release('d');
    }
  }
  /* Display the floating point data */
  Serial.print("X: ");
  Serial.print(event.orientation.x, 4);
  Serial.print("\tY: ");
  Serial.print(event.orientation.y, 4);
  Serial.print("\tZ: ");
  Serial.print(event.orientation.z, 4);
  Serial.println("");
  
  delay(100);
}
