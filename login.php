<?php

session_start();

// Inclure le fichier de configuration
include 'config2.php';

// Vérifier si les champs username et password ont été envoyés
if(isset($_POST['username']) && isset($_POST['password'])) {
    // Récupération des champs username et password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Initialiser les variables pour la validation des champs
    $username_valid = false;
    $password_valid = false;

    // Initialiser la variable pour stocker le résultat de la connexion
    $connexion_reussie = false;

    // connection a la bdd
    $pdo = get_pdo_instance(); // 

    try {
        //  requêtes  SQL
        $sql = "SELECT id, username, password, grade FROM fablab2024.inscrit WHERE username = :username";

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
        
                $connexion_reussie = true;
                $grade = $row['grade']; // Récupérer le grade de l'utilisateur
                $id = $row['id'];
            }
        }

        // Retourner une réponse JSON indiquant le succès ou l'échec de la connexion
        if ($connexion_reussie) {

            $_SESSION['id'] = $id;
            if ($grade=='admin'){
                echo json_encode(array("connexion_reussie" => true, "message" => "admin"));
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
