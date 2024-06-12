<?php

// Inclusion du fichier de configuration
require_once('config2.php');

// Vérifiez si le contenu POST est présent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérez le corps de la requête HTTP
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Vérifiez si les données sont présentes et non vides
    if (isset($data['MacAddress']) && isset($data['CardID'])) {
        // Récupérez les données
        $adresseMac = $data['MacAddress'];
        $numero = $data['CardID'];

        // Initialiser la variable pour stocker le résultat de la validation
        $valide = "non";

        // Vérifier l'authentification de l'utilisateur
        $pdo = get_pdo_instance(); // Supposant que cette fonction est définie dans config2.php

        try {
            // Utilisation de requêtes préparées pour éviter les injections SQL
            $cadenaGrade = "SELECT grade FROM fablab2024.cadenas WHERE adresseMac = :adresseMac";
            $carteGrade = "SELECT grade FROM fablab2024.inscrit WHERE numero = :numero";
            $idQuery = "SELECT id FROM fablab2024.inscrit WHERE numero = :numero";

            $cadenaRe = $pdo->prepare($cadenaGrade);
            $cadenaRe->bindParam(':adresseMac', $adresseMac, PDO::PARAM_STR);
            $cadenaRe->execute();

            $carteRe = $pdo->prepare($carteGrade);
            $carteRe->bindParam(':numero', $numero, PDO::PARAM_STR);
            $carteRe->execute();

            $idRe = $pdo->prepare($idQuery);
            $idRe->bindParam(':numero', $numero, PDO::PARAM_STR);
            $idRe->execute();

            $cadena = $cadenaRe->fetch(PDO::FETCH_ASSOC);
            $carte = $carteRe->fetch(PDO::FETCH_ASSOC);
            $inscrit_id = $idRe->fetch(PDO::FETCH_ASSOC);

            if ($carte && $cadena) {
                $gradesAllowed = [
                    "teacher" => ["member", "teacher"],
                    "manager" => ["member", "teacher", "manager"],
                    "admin" => ["member", "teacher", "manager", "admin"]
                ];

                // Récupérer les détails actuels
                $currentDateTime = new DateTime();
                $annee = $currentDateTime->format('Y');
                $mois = $currentDateTime->format('m');
                $jour = $currentDateTime->format('d');
                $heure = $currentDateTime->format('H');
                $minutes = $currentDateTime->format('i');

                // Vérifier l'heure du dernier badgeage
                $lastLoginQuery = "SELECT MAX(CONCAT(anne, '-', LPAD(mois, 2, '0'), '-', LPAD(jour, 2, '0'), ' ', LPAD(heure, 2, '0'), ':', LPAD(minutes, 2, '0'))) as last_login 
                                   FROM fablab2024.logs 
                                   WHERE inscrit_id = :inscrit_id";
                
                $lastLoginRe = $pdo->prepare($lastLoginQuery);
                $lastLoginRe->bindParam(':inscrit_id', $inscrit_id['id'], PDO::PARAM_INT);
                $lastLoginRe->execute();

                $lastLogin = $lastLoginRe->fetch(PDO::FETCH_ASSOC);

                $interval = null;
                if ($lastLogin['last_login']) {
                    $lastLoginDateTime = new DateTime($lastLogin['last_login']);
                    $interval = $currentDateTime->diff($lastLoginDateTime);
                }

                // Validation si l'intervalle est supérieur ou égal à une heure ou s'il n'y a pas d'enregistrement précédent
                if (in_array($cadena["grade"], $gradesAllowed[$carte["grade"]])) {
                    $valide = "ok";
                    if (!$interval || $interval->h >= 1 || $interval->d > 0) {
                        // Incrémenter le passage et le temps si l'intervalle est inférieur à 1 heure
                        $logincre = "INSERT INTO fablab2024.logs (anne, mois, jour, heure, minutes, inscrit_id, passage, temps) 
                                     VALUES (:anne, :mois, :jour, :heure, :minutes, :inscrit_id, 1, 1)";
        
                        $logRE = $pdo->prepare($logincre);
        
                        $logRE->bindParam(':anne', $annee, PDO::PARAM_INT);
                        $logRE->bindParam(':mois', $mois, PDO::PARAM_INT);
                        $logRE->bindParam(':jour', $jour, PDO::PARAM_INT);
                        $logRE->bindParam(':heure', $heure, PDO::PARAM_INT);
                        $logRE->bindParam(':minutes', $minutes, PDO::PARAM_INT);
                        $logRE->bindParam(':inscrit_id', $inscrit_id['id'], PDO::PARAM_INT);
        
                        $logRE->execute();
                    
                        // Sinon, incrémente le passage et le temps existants
                        $updatePassageTimeQuery = "UPDATE fablab2024.logs 
                                                   SET passage = 1, temps = 1
                                                   WHERE inscrit_id = :inscrit_id";
                        
                        $updatePassageTimeRe = $pdo->prepare($updatePassageTimeQuery);
                        $updatePassageTimeRe->bindParam(':inscrit_id', $inscrit_id['id'], PDO::PARAM_INT);
                        $updatePassageTimeRe->execute();
                    }
                }
            }

        } catch (PDOException $e) {
            // Gestion des erreurs de base de données
            echo json_encode(array("grade_valide" => false, "message" => "Erreur de base de données: " . $e->getMessage()));
            exit;
        }

        // Fermer la connexion PDO
        $pdo = null;

        // Envoyer la réponse HTTP en fonction de la validation
        if ($valide == "ok") {
            http_response_code(200);
            echo json_encode(array("grade_valide" => true, "message" => "CardID valide."));
        } else {
            http_response_code(400);
            echo json_encode(array("grade_valide" => false, "message" => "CardID invalide ou moins d'une heure depuis le dernier badgeage."));
        }
    } else {
        // Si les données sont manquantes ou invalides
        http_response_code(410);
        echo json_encode(array("grade_valide" => false, "message" => "Données manquantes ou invalides."));
    }
}
?>
