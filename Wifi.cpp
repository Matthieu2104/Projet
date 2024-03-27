#include "Wifi.h"

void Wifi() {
  const char* ssid = "E5576_93F9";
  const char* password = "inagd6TbhmB";
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("\nConnecting");
  while(WiFi.status()!= WL_CONNECTED){
    Serial.print(".");
    delay(1); 
  }
  Serial.println("\nConnected to the wifi network");
  Serial.println("local ip: ");
  Serial.println(WiFi.localIP());
}
