<?php
// Include config file
//require_once "config.php";
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    //$user = htmlspecialchars($_POST["username"]);
    //$pass = htmlspecialchars($_POST["password"]);

    // Vous pouvez traiter les données comme vous le souhaitez ici
    // Par exemple, vérifier les informations de connexion, les enregistrer dans une base de données, etc.

    // Redirection vers une autre page après le traitement
   // header("Location: dashboard.html");
   // exit();
//}


// Inclure le fichier de configuration pour la connexion à la base de données
include 'config.php';

// Récupération des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = "James";
    $password = "azerty";

    // Requête pour vérifier l'authentification dans la base de données
    $query = "SELECT * FROM utilisateur WHERE username='$username' AND password='$password'";
    $result = $pdo->query($query);

    // Vérification du résultat de la requête
    if ($result->num_rows > 0) {
        // Authentification réussie
        echo json_encode(["message" => "connexion réussie"]);
    } else {
        // Authentification échouée
        echo json_encode(["message" => "Mot de passe incorrect ou utilisateur incorrect"]);
    }
}

// Fermeture de la connexion à la base de données (vous pouvez le faire à la fin de votre script)
$pdo->close();


?>