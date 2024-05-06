#include <WiFi.h>

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
  delay(5000); // Attendez 5 secondes avant d'envoyer les prochaines données
}

void sendToPHP(String data) {
  WiFiClient client;

  if (client.connect(serverAddress, serverPort)) {
    Serial.println("Connexion établie avec le serveur PHP");

    client.print("POST /btssnir/projets2024/fablab2024/fablab2024/site/projetApiXweb/recupArduino.php HTTP/1.1\r\n");
    client.print("Host: ");
    client.println(serverAddress);
    client.println("Connection: close");
    client.println("Content-Type: text/plain");
    client.print("Content-Length: ");
    client.println(data.length());
    client.println();
    client.println(data);
  } else {
    Serial.println("Échec de la connexion avec le serveur PHP");
  }

  while (client.connected()) {
    if (client.available()) {
      String line = client.readStringUntil('\r');
      Serial.print(line);
    }
  }
  Serial.println();
  Serial.println("Déconnecté du serveur");
}
