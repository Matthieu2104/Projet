#include <Arduino.h>
#include <ESP32Servo.h>
#include <MFRC522.h>
#include <SPI.h>

#define SS_PIN 21
#define RST_PIN 22
MFRC522 mfrc522(SS_PIN, RST_PIN);

Servo servo;

const int pin_num1 = 1; 
const int pin_num2 = 3;

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  pinMode(pin_num1, INPUT_PULLUP);
  pinMode(pin_num2, INPUT_PULLUP);
  servo.attach(32);
}
void cleanup() {
  pinMode(pin_num1, INPUT);
  pinMode(pin_num2, INPUT);
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
      Serial.println("Accès autorisé. Activation du servomoteur.");
      digitalWrite(pin_num2, HIGH);
      servo.write(35);
      delay(2000);
      servo.write(0);
      digitalWrite(pin_num2, LOW);
    } else {
      Serial.println("Accès refusé. Carte non autorisée.");
      digitalWrite(pin_num1, HIGH);
      delay(1000);
      digitalWrite(pin_num1, LOW);
    }
  }
}
