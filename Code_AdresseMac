#include <WiFi.h>


const char* ssid = "E5576_93F9";
const char* password = "inagd6TbhmB";

void setup() {
  Serial.begin(115200);
  // Initialiser le module WiFi
  WiFi.mode(WIFI_STA);
  WiFi.begin();
  
  // Attendre que le module WiFi soit initialisé
  while (WiFi.status() == WL_DISCONNECTED) {
    delay(100);
  }

  // Récupérer l'adresse MAC
  String macAddress = WiFi.macAddress();
  
  // Afficher l'adresse MAC dans la console série
  Serial.print("Adresse MAC de l'ESP32: ");
  Serial.println(macAddress);
}

void loop() {
  // Rien à faire dans la boucle principale
}
