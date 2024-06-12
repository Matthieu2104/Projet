#include <WiFi.h>


const char* ssid = "E5576_93F9";
const char* password = "inagd6TbhmB";

void setup() {
  Serial.begin(115200);
  // Initialiser le module WiFi
  WiFi.mode(WIFI_STA);
  WiFi.begin();
  
 //Attente de connexion
  while (WiFi.status() == WL_DISCONNECTED) {
    delay(100);
  }

  // Récupérer l'adresse MAC
  String macAddress = WiFi.macAddress();
  
  // Afficher l'adresse MAC
  Serial.print("Adresse MAC de l'ESP32: ");
  Serial.println(macAddress);
}

void loop() {

}
