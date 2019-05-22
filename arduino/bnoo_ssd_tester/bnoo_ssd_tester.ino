#include <SPI.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BNO055.h>
#include <utility/imumaths.h>

#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 64 // OLED display height, in pixels

#define OLED_RESET     4 // Reset pin # (or -1 if sharing Arduino reset pin)
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

#define DISPVCC        13          // OLED power

/* Set the delay between fresh samples */
uint16_t BNO055_SAMPLERATE_DELAY_MS = 100;

Adafruit_BNO055 bno = Adafruit_BNO055(55);

byte lastMode = 1;


void displayPowerOn() {
  if (lastMode == 1) {
    digitalWrite(DISPVCC,LOW);
    display.begin(SSD1306_SWITCHCAPVCC, 0x3C);
    display.clearDisplay();
    display.setTextSize(1);
    display.setTextColor(WHITE);
    display.setCursor(0,0);
    display.display();
  }
  lastMode = 0;
}

void displayPowerOff() {
  digitalWrite(DISPVCC,HIGH);
  display.clearDisplay();
  display.display();
  lastMode = 0;
}


void setup(void) {
  Serial.begin(115200);
  Serial.println("Orientation Sensor Test"); Serial.println("");

  /* Initialise the sensor */
  if (!bno.begin())
  {
    /* There was a problem detecting the BNO055 ... check your connections */
    Serial.print("Ooops, no BNO055 detected ... Check your wiring or I2C ADDR!");
    while (1);
  }
  
  displayPowerOn();
  delay(1000);

}

#define thresh1 2
#define thresh2 4
#define thresh3 50
#define thresh4 100
#define thresh5 25

unsigned long lastMovement;
unsigned long minStableTime = 500;

void loop(void) {
  sensors_event_t orientationData , angVelocityData , linearAccelData;
  bno.getEvent(&orientationData, Adafruit_BNO055::VECTOR_EULER);
  bno.getEvent(&angVelocityData, Adafruit_BNO055::VECTOR_GYROSCOPE);
  bno.getEvent(&linearAccelData, Adafruit_BNO055::VECTOR_LINEARACCEL);

  char axis = ' ';
  byte impulse = 0;

  /* First priority: angular accel
   * Don't allow a judgment to be made if we're moving about */

  if (abs(angVelocityData.gyro.x) > thresh3) axis = 'A' + (angVelocityData.gyro.x < 0) * 32;
  if (abs(angVelocityData.gyro.x) > thresh4) impulse = 1;

  if (abs(angVelocityData.gyro.y) > thresh3) axis = 'B' + (angVelocityData.gyro.y < 0) * 32;
  if (abs(angVelocityData.gyro.y) > thresh4) impulse = 1;

  if (abs(angVelocityData.gyro.z) > thresh3) axis = 'C' + (angVelocityData.gyro.z < 0) * 32;
  if (abs(angVelocityData.gyro.z) > thresh4) impulse = 1;

  if (axis == ' ') { /* Second priority: linear accel */
    if (abs(linearAccelData.acceleration.x) > thresh1) axis = 'X' + (linearAccelData.acceleration.x < 0) * 32;
    if (abs(linearAccelData.acceleration.x) > thresh2) impulse = 1;
  
    if (abs(linearAccelData.acceleration.y) > thresh1) axis = 'Y' + (linearAccelData.acceleration.y < 0) * 32;
    if (abs(linearAccelData.acceleration.y) > thresh2) impulse = 1;
  
    if (abs(linearAccelData.acceleration.z) > thresh1) axis = 'Z' + (linearAccelData.acceleration.z < 0) * 32;
    if (abs(linearAccelData.acceleration.z) > thresh2) impulse = 1;
  }

  if (axis != ' ' && (millis() - lastMovement) > minStableTime) {
    Serial.print("---");
    Serial.println(millis() - lastMovement);
    display.clearDisplay();
    display.setCursor(0,0);
    display.print("Event: ");
    display.print(axis);
    display.println(impulse);
    display.println();
    printEvent(&orientationData, display);
    printEvent(&angVelocityData, display);
    printEvent(&linearAccelData, display);
    display.display();
  } else {
    for (int y = 8; y < 24; y++)
      display.drawFastHLine(0, y, 128, 0);
    display.setCursor(0,8);
    if (millis() - lastMovement > minStableTime) {
      display.print("stable for ");
      display.print(millis() - lastMovement);
      display.println("ms");
    } else display.setCursor(0,16);
    printEvent(&orientationData, display);
    display.display();  
  }

  if ((abs(angVelocityData.gyro.x)
    + abs(angVelocityData.gyro.y)
    + abs(angVelocityData.gyro.z)) > thresh5) {
    lastMovement = millis();
    Serial.println(lastMovement);
  }
  
  delay(BNO055_SAMPLERATE_DELAY_MS);
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


