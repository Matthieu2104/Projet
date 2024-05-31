<?php

// Inclusion du fichier de configuration
require_once('config2.php');

// Activer l'affichage des erreurs pour le d�bogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// V�rifiez si le contenu POST est pr�sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // R�cup�rez le corps de la requ�te HTTP
    $data = json_decode(file_get_contents('php://input'), true);

    // V�rifiez si les donn�es sont pr�sentes et non vides
    if (isset($data['adresseMac']) && isset($data['CardID']) && isset($data['role'])) {
        // R�cup�rez les donn�es
        $adresseMac = $data['adresseMac'];
        $numero = $data['CardID'];
        $role = $data['role'];

        // Initialiser la variable pour stocker le r�sultat de la validation
        $valide = "non";

        // V�rifier l'authentification de l'utilisateur
        $pdo = get_pdo_instance(); // Supposant que cette fonction est d�finie dans config2.php

        try {
            // Utilisation de requ�tes pr�par�es pour �viter les injections SQL
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

            // Ajout de logs pour v�rifier les valeurs r�cup�r�es
            error_log("Grade du cadenas : " . print_r($cadena, true));
            error_log("Grade de la carte : " . print_r($carte, true));
            error_log("ID inscrit : " . print_r($inscrit_id, true));

            if ($carte && $cadena) {
                $gradesAllowed = [
                    "teacher" => ["member", "teacher"],
                    "manager" => ["member", "teacher", "manager"],
                    "admin" => ["member", "teacher", "manager", "admin"]
                ];

                // R�cup�rer les d�tails actuels
                $currentDateTime = new DateTime();
                $annee = $currentDateTime->format('Y');
                $mois = $currentDateTime->format('m');
                $jour = $currentDateTime->format('d');
                $heure = $currentDateTime->format('H');
                $minutes = $currentDateTime->format('i');

                // V�rifier l'heure du dernier badgeage
                $lastLoginQuery = "SELECT MAX(CONCAT(anne, '-', LPAD(mois, 2, '0'), '-', LPAD(jour, 2, '0'), ' ', LPAD(heure, 2, '0'), ':', LPAD(minutes, 2, '0'))) as last_login, MAX(passage) as last_passage 
                                   FROM fablab2024.logs 
                                   WHERE inscrit_id = :inscrit_id";
                
                $lastLoginRe = $pdo->prepare($lastLoginQuery);
                $lastLoginRe->bindParam(':inscrit_id', $inscrit_id['id'], PDO::PARAM_INT);
                $lastLoginRe->execute();

                $lastLogin = $lastLoginRe->fetch(PDO::FETCH_ASSOC);

                error_log("Dernier login : " . print_r($lastLogin, true));

                $interval = null;
                $newPassage = 1; // Initialiser � 1 par d�faut, au cas o� il n'y aurait aucun passage pr�c�dent
                if ($lastLogin['last_login']) {
                    $lastLoginDateTime = new DateTime($lastLogin['last_login']);
                    $interval = $currentDateTime->diff($lastLoginDateTime);
                    $newPassage = $lastLogin['last_passage'] + 1;
                    error_log("Intervalle : " . print_r($interval, true));
                }

                // Validation si l'intervalle est sup�rieur ou �gal � une heure ou s'il n'y a pas d'enregistrement pr�c�dent
                if (in_array($cadena["grade"], $gradesAllowed[$carte["grade"]])) {
                    $valide = "ok";
                    if (!$interval || $interval->h >= 1 || $interval->d > 0) {
                        $logincre = "INSERT INTO fablab2024.logs (anne, mois, jour, heure, minutes, inscrit_id, passage) VALUES (:anne, :mois, :jour, :heure, :minutes, :inscrit_id, :passage)";
                        $logRE = $pdo->prepare($logincre);
        
                        $logRE->bindParam(':anne', $annee, PDO::PARAM_INT);
                        $logRE->bindParam(':mois', $mois, PDO::PARAM_INT);
                        $logRE->bindParam(':jour', $jour, PDO::PARAM_INT);
                        $logRE->bindParam(':heure', $heure, PDO::PARAM_INT);
                        $logRE->bindParam(':minutes', $minutes, PDO::PARAM_INT);
                        $logRE->bindParam(':inscrit_id', $inscrit_id['id'], PDO::PARAM_INT);
                        $logRE->bindParam(':passage', $newPassage, PDO::PARAM_INT);
        
                        $logRE->execute();
                    }
                } else {
                    error_log("Le grade de la carte n'est pas autoris� � ouvrir ce cadenas.");
                    echo json_encode(array("grade_valide" => false, "message" => "Le grade de la carte n'est pas autoris� � ouvrir ce cadenas."));
                    exit;
                }
            } else {
                error_log("Les informations de la carte ou du cadenas sont incorrectes.");
                echo json_encode(array("grade_valide" => false, "message" => "Les informations de la carte ou du cadenas sont incorrectes."));
                exit;
            }

        } catch (PDOException $e) {
            // Gestion des erreurs de base de donn�es
            error_log("Erreur de base de donn�es: " . $e->getMessage());
            echo json_encode(array("grade_valide" => false, "message" => "Erreur de base de donn�es: " . $e->getMessage()));
            exit;
        }

        // Fermer la connexion PDO
        $pdo = null;

        // Envoyer la r�ponse HTTP en fonction de la validation
        if ($valide == "ok") {
            http_response_code(200);
            echo json_encode(array("grade_valide" => true, "message" => "CardID valide."));
        } else {
            error_log("CardID invalide ou moins d'une heure depuis le dernier badgeage.");
            http_response_code(400);
            echo json_encode(array("grade_valide" => false, "message" => "CardID invalide ou moins d'une heure depuis le dernier badgeage."));
        }
    } else {
        // Si les donn�es sont manquantes ou invalides
        error_log("Donn�es manquantes ou invalides.");
        http_response_code(410);
        echo json_encode(array("grade_valide" => false, "message" => "Donn�es manquantes ou invalides."));
    }
} else {
    echo "Aucune donn�e POST re�ue.";
}
?>
