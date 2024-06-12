#include <WiFi.h>
#include <HTTPClient.h>
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

const char* ssid = "E5576_93F9"; //nom du reseau
const char* password = "inagd6TbhmB"; //mot de passe du réseau
const char* serverAddress = "51.210.151.13"; //url du serveur
const int serverPort = 80; //port du serveur



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


}

void loop() {
  //reconnaitre une carte et la lire
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Carte détectée.");
    presence_carte(); //appel de la fonction presence_carte
  }
}

void presence_carte() {
  String cardID = "";
  //récupérer Adresse Mac
  String macAddress = WiFi.macAddress();
  //récupérer l'ID de la carte en Hexadecimal
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    cardID += String(mfrc522.uid.uidByte[i], HEX);
  }

  Serial.println("ID de la carte: " + cardID);

  requete_api(macAddress, cardID); //appel de la fonction requete_api avec l'ID de la carte et l'adresse mac
}

void requete_api(String macAddress, String cardID) {
  WiFiClient client;
  HTTPClient http;

  // Construire la chaîne JSON
  String jsonString = "{";
  jsonString += "\"MacAddress\":\"" + macAddress + "\",";
  jsonString += "\"CardID\":\"" + cardID + "\"";
  jsonString += "}";
  //connexion a l'adresse de l'API
  if (http.begin(client, "http://" + String(serverAddress) + ":" + String(serverPort) + "/btssnir/projets2024/fablab2024/fablab2024/site/projetApiXweb/recupArduino.php")) {
    http.addHeader("Content-Type", "application/json");
    http.addHeader("User-Agent", "ESP32");

    Serial.println("JSON envoyé : " + jsonString);
    
    //envoie de la chaine JSON a l'API en methode POST
    int httpResponseCode = http.POST(jsonString);

    Serial.print("Code de réponse HTTP: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode == 200) {
      String response = http.getString();
      Serial.println("Réponse du serveur: " + response);
      moteur_esp32(); //appel de la fopnction moteur_esp32
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
  servo.attach(32); //mise en fonctionnement du servomoteur
  delay(500);
  servo.write(35); //ouverture du servomoteur a 35°
  delay(2000);
  servo.write(0);//remise en place a 0°
  digitalWrite(pin_num2, LOW);
  delay(1000);
  servo.detach(); //mise hors tension du servomoteur
}
