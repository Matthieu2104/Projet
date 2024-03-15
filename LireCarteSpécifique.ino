#include <Arduino.h>
#include <ESP32Servo.h>
#include <MFRC522.h>
#include <SPI.h>

#define SS_PIN 21
#define RST_PIN 22
MFRC522 mfrc522(SS_PIN, RST_PIN);

const gpio_num_t GPIO_PIN = GPIO_NUM_32;

Servo servo;

void resetGPIO();

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  
  servo.attach(32); // Modifiez cela en fonction de votre configuration
}

void loop() {
  // Vérifiez la présence d'une carte
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Carte détectée.");

    // Lisez l'ID de la carte
    String cardID = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      cardID += String(mfrc522.uid.uidByte[i], HEX);
    }
    
    Serial.println("ID de la carte: " + cardID);

    // Vérifiez si la carte détectée est autorisée
    if (cardID == "24d7ee7") {
      Serial.println("Accès autorisé. Activation du servomoteur.");
      servo.write(35);
      delay(2000);
      servo.write(0);
      delay(2000);
      Serial.println("cleanup");
      resetGPIO();
    } else {
      Serial.println("Accès refusé. Carte non autorisée.");
    }
    
  }
}

void resetGPIO(){
        gpio_reset_pin(GPIO_PIN);
        Serial.println("GPIO réinitialisée à son état par défaut.");
    }