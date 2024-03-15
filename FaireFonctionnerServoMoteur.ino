#include <ESP32Servo.h>

// Définir le pin auquel le servomoteur est connecté
const int servoPin = 32; // Vous pouvez utiliser n'importe quel autre pin selon votre configuration

// Créer un objet Servo
Servo monServo;

void setup() {
  // Démarrez la communication série
  Serial.begin(115200);

  // Attachez le servomoteur au pin spécifié
  monServo.attach(servoPin);
}

void loop() {
  // Faites tourner le servomoteur de 0 à 180 degrés
  for (int angle = 0; angle <= 180; angle += 1) {
    monServo.write(angle);
    delay(15); // Ajoutez un délai pour laisser le servomoteur atteindre la position
  }

  delay(1000); // Attendez une seconde à la position maximale

  // Faites tourner le servomoteur de 180 à 0 degrés
  for (int angle = 180; angle >= 0; angle -= 1) {
    monServo.write(angle);
    delay(15);
  }

  delay(1000); // Attendez une seconde à la position minimale
}
