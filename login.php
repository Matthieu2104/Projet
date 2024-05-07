<?php
// Inclure le fichier de configuration
include 'config2.php';

// Vérifier si les champs username et password ont été envoyés
if(isset($_POST['username']) && isset($_POST['password'])) {
    // Récupérer les valeurs des champs username et password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Initialiser les variables pour la validation des champs
    $username_valid = false;
    $password_valid = false;

    // Initialiser la variable pour stocker le résultat de la connexion
    $connexion_reussie = false;

    // Vérifier l'authentification de l'utilisateur
    $pdo = get_pdo_instance(); // Supposant que cette fonction est définie dans config2.php

    try {
        // Utilisation de requêtes préparées pour éviter les injections SQL
        $sql = "SELECT username, password, grade FROM fablab2024.inscrit WHERE username = :username";
        $requete = $pdo->prepare($sql);
        $requete->bindParam(':username', $username, PDO::PARAM_STR);
        $requete->execute();

        while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
            if ($row['username'] === $username) {
                // Le nom d'utilisateur est valide
                $username_valid = true;
                break;
            }
        }

        if ($username_valid) {
            // Si le nom d'utilisateur est valide, vérifier le mot de passe
            if ($row['password'] === $password) {
                // Le nom d'utilisateur et le mot de passe sont valides
                $connexion_reussie = true;
                $grade = $row['grade']; // Récupérer le grade de l'utilisateur
            }
        }

        // Retourner une réponse JSON indiquant le succès ou l'échec de la connexion
        if ($connexion_reussie) {
            session_start();
            $_SESSION['username'] = $username;
            if ($grade=='Admin'){
                echo json_encode(array("connexion_reussie" => true, "message" => "Admin"));
            }else{
                echo json_encode(array("connexion_reussie" => true, "message" => "Connexion"));
            }

        } else {
            echo json_encode(array("connexion_reussie" => false, "message" => "Nom utilisateur ou mot de passe incorrect."));
        }

    } catch (PDOException $e) {
        // Gestion des erreurs de base de données
        echo json_encode(array("connexion_reussie" => false, "message" => "Erreur de base de données: " . $e->getMessage()));
    }

    // Fermer la connexion PDO
    $pdo = null;
} else {
    // Les champs n'ont pas été envoyés
    echo json_encode(array("connexion_reussie" => false, "message" => "Champs manquants."));
}
?>
