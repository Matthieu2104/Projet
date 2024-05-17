<?php

// Inclusion du fichier de configuration
require_once('config2.php');

// Vérifiez si le contenu POST est présent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérez le corps de la requête HTTP
    $data = json_decode(file_get_contents('php://input'), true);

    // Inclusion du fichier de configuration
    require_once('config2.php');
    
    // Vérifiez si les données sont présentes et non vides
    if (isset($data['MacAddress']) && isset($data['CardID']) && isset($data['DateTime'])) {
        // Récupérez les données
        $adresseMac = $data['MacAddress'];
        $numero = $data['CardID'];
        $dateTime = $data['DateTime'];
    

        // Initialiser la variable pour stocker le résultat de la connexion
        $valide = "non";

        // Vérifier l'authentification de l'utilisateur
        $pdo = get_pdo_instance(); // Supposant que cette fonction est définie dans config2.php

        try {
            // Utilisation de requêtes préparées pour éviter les injections SQL
            $cadenaGrade = "SELECT grade FROM fablab2024.cadenas WHERE adresseMac = :adresseMac";
            $carteGrade = "SELECT grade FROM fablab2024.inscrit WHERE numero = :numero";

            $cadenaRe = $pdo->prepare($cadenaGrade);
            $cadenaRe->bindParam(':adresseMac', $adresseMac, PDO::PARAM_STR);
            $cadenaRe->execute();

            $carteRe = $pdo->prepare($carteGrade);
            $carteRe->bindParam(':numero', $numero, PDO::PARAM_STR);
            $carteRe->execute();

            $cadena = $cadenaRe->fetch(PDO::FETCH_ASSOC);
            $carte = $carteRe->fetch(PDO::FETCH_ASSOC);

            if ($carte && $cadena) {
                $gradesAllowed = [
                    "member" => ["member"],
                    "teacher" => ["member", "teacher"],
                    "manager" => ["member", "teacher", "manager"],
                    "admin" => ["member", "teacher", "manager", "admin"]
                ];

                if (in_array($cadena["grade"], $gradesAllowed[$carte["grade"]])) {
                    $valide = "ok";
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
            echo json_encode(array("grade_valide" => false, "message" => "CardID invalide."));
        }
    } else {
        // Si les données sont manquantes ou invalides
        http_response_code(410);
        echo json_encode(array("grade_valide" => false, "message" => "Données manquantes ou invalides."));
    }
}
    ?>
