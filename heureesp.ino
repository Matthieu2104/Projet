#include <Arduino.h>
#include <ESP32Servo.h>
#include <MFRC522.h>
#include <SPI.h>
#include "time.h"
#include <WiFi.h>

#define SS_PIN 21
#define RST_PIN 22
MFRC522 mfrc522(SS_PIN, RST_PIN);

Servo servo;

const int pin_num1 = 1; 
const int pin_num2 = 3;

const char* ssid = "E5576_93F9"; // Nom de la box Wi-Fi
const char* password = "inagd6TbhmB"; // MDP de la box Wi-Fi

const char* ntpServer = "pool.ntp.org";
const long  gmtOffset_sec = 3600 * 1;
const int   daylightOffset_sec = 3600 * 0;

void setup() {
  Serial.begin(115200);
  WiFi.mode(WIFI_STA); // Optional
  WiFi.begin(ssid, password);
  Serial.println("\nConnecting");

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(100);
  }

  Serial.println("\nConnected to the WiFi network");
  Serial.print("Local ESP32 IP: ");
  Serial.println(WiFi.localIP());

  // On configure le seveur NTP
  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);
  SPI.begin();
  mfrc522.PCD_Init();
  pinMode(pin_num1, OUTPUT);
  pinMode(pin_num2, OUTPUT);
  digitalWrite(pin_num1, LOW);
  digitalWrite(pin_num2, LOW);
  servo.attach(32);
  struct tm timeinfo;
  Serial.println(&timeinfo, "%A, %B %d %Y %H:%M:%S");
}

void loop() {

  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Carte détectée.");

    
    String cardID = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      cardID += String(mfrc522.uid.uidByte[i], HEX);
    }
    
    Serial.println("ID de la carte: " + cardID);

  
    if (cardID == "24d7ee7") {
      digitalWrite(pin_num2, HIGH);
      servo.attach(32);
      delay(50);
      servo.write(35);
      delay(2000);
      servo.write(0);
      struct tm timeinfo;
      if (!getLocalTime(&timeinfo)) {
          Serial.println(&timeinfo, "%A, %B %d %Y %H:%M:%S");
      }
      digitalWrite(pin_num2, LOW);
      delay(180);
      servo.detach();
    } else {
      digitalWrite(pin_num1, HIGH);
      delay(1000);
      digitalWrite(pin_num1, LOW);
    }
  }
}