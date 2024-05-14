#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino.h>
#include <ESP32Servo.h>
#include <MFRC522.h>
#include <SPI.h>
#include <NTPClient.h>
#include <WiFiUdp.h>

#define SS_PIN 21
#define RST_PIN 22
MFRC522 mfrc522(SS_PIN, RST_PIN);

Servo servo;

const int pin_num1 = 1; 
const int pin_num2 = 3;

const char* ssid = "E5576_93F9";
const char* password = "inagd6TbhmB";
const char* serverAddress = "51.210.151.13";
const int serverPort = 80;

WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", 3600, 60000);

void setup() {
  Serial.begin(115200);

  delay(1000);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("\nConnecting");
  while(WiFi.status() != WL_CONNECTED) {
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

  timeClient.begin();
  timeClient.setTimeOffset(3600);
}

void loop() {
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Carte détectée.");
    presence_carte();
  }
}

void presence_carte() {
  String cardID = "";
  String macAddress = WiFi.macAddress();
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    cardID += String(mfrc522.uid.uidByte[i], HEX);
  }

  timeClient.update();
  unsigned long epochTime = timeClient.getEpochTime();
  struct tm *ptm = gmtime((time_t *)&epochTime);
  int mois = ptm->tm_mon + 1;
  int jour = ptm->tm_mday;
  int annee = ptm->tm_year + 1900;
  int heure = ptm->tm_hour;
  int minute = ptm->tm_min;
  int seconde = ptm->tm_sec;
  char dateTime[20];
  snprintf(dateTime, sizeof(dateTime), "%04d-%02d-%02d %02d:%02d:%02d", annee, mois, jour, heure, minute, seconde);
  Serial.printf("Date et Heure : %s\n", dateTime);
  Serial.println("ID de la carte: " + cardID);

  requete_api(macAddress, cardID, String(dateTime));
}

void requete_api(String macAddress, String cardID, String dateTime) {
  WiFiClient client;
  HTTPClient http;

  // Construire la chaîne JSON manuellement
  String jsonString = "{";
  jsonString += "\"MacAddress\":\"" + macAddress + "\",";
  jsonString += "\"CardID\":\"" + cardID + "\",";
  jsonString += "\"DateTime\":\"" + dateTime + "\"";
  jsonString += "}";

  if (http.begin(client, "http://" + String(serverAddress) + ":" + String(serverPort) + "/btssnir/projets2024/fablab2024/fablab2024/site/projetApiXweb/recupArduino.php")) {
    http.addHeader("Content-Type", "application/json");
    http.addHeader("User-Agent", "ESP32");

    Serial.println("JSON envoyé : " + jsonString);

    int httpResponseCode = http.POST(jsonString);

    Serial.print("Code de réponse HTTP: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode == 200) {
      String response = http.getString();
      Serial.println("Réponse du serveur: " + response);
      moteur_esp32();
    } else if (httpResponseCode == 400) {
      Serial.println("Carte non valide");
      digitalWrite(pin_num1, HIGH);
      delay(1000);
      digitalWrite(pin_num1, LOW);
    } else {
      Serial.println("Échec de la requête HTTP");
    }

    http.end();
  } else {
    Serial.println("Échec de la connexion avec le serveur PHP");
  }
}

void moteur_esp32() {
  digitalWrite(pin_num2, HIGH);
  servo.attach(32);
  delay(500);
  servo.write(35);
  delay(2000);
  servo.write(0);
  digitalWrite(pin_num2, LOW);
  delay(1000);
  servo.detach();
}
