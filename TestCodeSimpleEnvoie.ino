#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* ssid = "E5576_93F9";
const char* password = "inagd6TbhmB";
const char* serverAddress = "51.210.151.13";
const int serverPort = 80;

void setup() {
  Serial.begin(115200);
  delay(100);

  // Connexion au réseau WiFi
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
}

void loop() {
  // Exemple d'envoi de données à votre fichier PHP
  sendToPHP("Test envoie");
  delay(5000); 
}

void sendToPHP() {
  WiFiClient client;

  HTTPClient http;

  // Créez un objet JSON
  DynamicJsonDocument jsonDoc(200); // Taille du document JSON
  jsonDoc["key"] = "value"; // Ajoutez des données à l'objet JSON

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
