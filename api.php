<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer les données du corps de la requête
    $data = json_decode(file_get_contents("php://input"), true);

    // Vérifier si les données nécessaires sont présentes
    if (isset($data["user"]) && isset($data["pass"])) {
        // Traitez les données si nécessaire
        // Dans cet exemple, simplement renvoyer "OK"
        $response = ["status" => "OK"];

        echo json_encode($response);
    } else {
        // Les données nécessaires ne sont pas présentes
        $response = ["error" => "Données manquantes"];
        echo json_encode($response);
    }
} else {
    // Méthode non autorisée
    http_response_code(405);
    $response = ["error" => "Méthode non autorisée"];
    echo json_encode($response);
}

// Ajoutez des messages de débogage
error_log("Script PHP exécuté avec succès");