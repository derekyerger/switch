#include <time.h>
#include <ESP8266WiFi.h>
#include <DNSServer.h>
#include <ESP8266WebServer.h>
#include <WiFiManager.h>
#include <WiFiClientSecure.h>

const char* ssid = "ADI-Spoke-r6K2yw";
const char* password = "alternatedevs";

const char* host = "api.altdevs.net";
const int apiPort = 8943;
const char* nodeId   = "r6K2ywLeEBEf7bvi";

extern const unsigned char caCert[] PROGMEM;
extern const unsigned int caCertLen;

WiFiClientSecure client;

void setup() {
  Serial.begin(38400);
  delay(10);

  WiFiManager wifiManager;
  wifiManager.setConnectTimeout(20);
  wifiManager.autoConnect(ssid, password);

  configTime(8 * 3600, 0, "pool.ntp.org", "time.nist.gov");
  time_t now = time(nullptr);
  while (now < 8 * 3600 * 2) {
    delay(500);
    now = time(nullptr);
  }
  struct tm timeinfo;
  gmtime_r(&now, &timeinfo);

  bool res = client.setCACert_P(caCert, caCertLen);
  if (!res) while (true) yield();
}


void loop() {
  const int apiPort = 8943;
  if (!client.connect(host, apiPort)) {
    delay(60000);
    return;
  }

  if (!client.verifyCertChain(host)) return;
  
  client.print(String("v1.0-") + nodeId + "\n");
  
  while (client.connected()) {
    while (client.available()) Serial.write(client.read());
    while (size_t len = Serial.available()) {
      uint8_t sbuf[len];
      Serial.readBytes(sbuf, len);
      client.write(sbuf, len);
      delay(1);
    }
  }
}

