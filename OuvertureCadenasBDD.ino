#include <Arduino.h>
#include <ESP32Servo.h>
#include <MFRC522.h>
#include <SPI.h>
#include <HTTPClient.h>
#include <WiFi.h>
#include "time.h"

#define SS_PIN 21
#define RST_PIN 22
MFRC522 mfrc522(SS_PIN, RST_PIN);

Servo servo;

const int pin_num1 = 1;
const int pin_num2 = 3;

const char* ssid = "E5576_93F9";
const char* password = "inagd6TbhmB";

const char* ntpServer = "pool.ntp.org";
const long  gmtOffset_sec = 3600 * 1;
const int   daylightOffset_sec = 3600 * 0;

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n");
  WiFi.mode(WIFI_STA);
  WiFi.persistent(false);
  WiFi.begin(ssid, password);
  Serial.println("\nConnecting");
  while(WiFi.status()!= WL_CONNECTED){
    Serial.print(".");
    delay(100); 
  }
  Serial.println("\nConnected to the wifi network");
  Serial.println("local ip: ");
  Serial.println(WiFi.localIP());
  SPI.begin();
  mfrc522.PCD_Init();
  pinMode(pin_num1, OUTPUT);
  pinMode(pin_num2, OUTPUT);
  digitalWrite(pin_num1, LOW);
  digitalWrite(pin_num2, LOW);
  servo.attach(32);
  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);
}


void loop() {
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Carte détectée.");

    String cardID = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      cardID += String(mfrc522.uid.uidByte[i], HEX);
    }

    Serial.println("ID de la carte: " + cardID);

    HTTPClient http;
    http.begin("https://57d1313e-8972-40f3-82bb-3749f6d90a81.mock.pstmn.io/Carte?envoie=ok");
    http.addHeader("Content-Type", "application/json");
    
    int httpEnvoie = http.POST("{\"card_id\": " + cardID + "}");
    int httpResponseCode = http.get();
    
    if (httpResponseCode == "autorisée") {
      String response = http.getString();
      Serial.println(response);
      struct tm timeinfo;
      Serial.println(&timeinfo, "%A, %B %d %Y %H:%M:%S");
      digitalWrite(pin_num2, HIGH);
      servo.attach(32);
      delay(500);
      servo.write(35);
      delay(2000);
      servo.write(0);
      digitalWrite(pin_num2, LOW);
      delay(1000);
      servo.detach();
    } else {
        Serial.println("Invalid card ID");
        digitalWrite(pin_num1, HIGH);
        delay(1000);
        digitalWrite(pin_num1, LOW);
    }

    http.end();
  }
}
