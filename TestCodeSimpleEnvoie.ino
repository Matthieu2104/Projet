#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Arduino.h>
#include <ESP32Servo.h>
#include <MFRC522.h>
#include <SPI.h>

#define SS_PIN 21
#define RST_PIN 22
MFRC522 mfrc522(SS_PIN, RST_PIN);

Servo servo;

const char* ssid = "E5576_93F9";
const char* password = "inagd6TbhmB";
const char* serverAddress = "51.210.151.13";
const int serverPort = 80;


void setup() {
  Serial.begin(115200);
  delay(100);

  //Connexion au réseau WiFi
  Serial.println();
  Serial.println();
  Serial.print("Connexion au réseau WiFi: ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connecté");
  Serial.println("Adresse IP: ");
  Serial.println(WiFi.localIP());

  SPI.begin();
  mfrc522.PCD_Init();

}


void loop() {
   if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Carte détectée.");

    
    String cardID = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      cardID += String(mfrc522.uid.uidByte[i], HEX);
    }

      // Exemple d'envoi de données à votre fichier PHP
      sendToPHP(cardID);
      delay(5000);
  }
}

void sendToPHP(String jsonData) {
  WiFiClient client;

  HTTPClient http;

  // Créez un objet JSON
  DynamicJsonDocument jsonDoc(100); // Taille du document JSON
  jsonDoc["CardID"] = jsonData; // Ajoutez des données à l'objet JSON

  // Convertissez l'objet JSON en une chaîne JSON
  String jsonString;
  serializeJson(jsonDoc, jsonString);

  if (http.begin(client, "http://" + String(serverAddress) + ":" + String(serverPort)  + "/btssnir/projets2024/fablab2024/fablab2024/site/projetApiXweb/recupArduino.php")) {
    http.addHeader("Content-Type", "application/json");

    Serial.println("JSON envoyé : " + jsonString);

    int httpResponseCode = http.POST(jsonString);

    if (httpResponseCode > 0) {
      Serial.print("Code de réponse HTTP: ");
      Serial.println(httpResponseCode);
      String response = http.getString();
      Serial.println(response);
    } else {
      Serial.print("Échec de la requête HTTP : ");
      Serial.println(httpResponseCode);
    }

    http.end();
  } else {
    Serial.println("Échec de la connexion avec le serveur PHP");
  }
}
