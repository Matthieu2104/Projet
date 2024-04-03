<?php

// Inclusion du fichier de configuration
require_once('config2.php');

// Vérification si le formulaire a été soumis
if (isset($_POST['newGrade']) && isset($_POST['newAdresse'])) {
    // Récupération des champs du formulaire

    $grade = $_POST['newGrade'];
    $adresseMac = $_POST['newAdresse'];


    // Connexion à la base de données
    $pdo = get_pdo_instance();

    try {
        // Préparation de la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO fablab2024.cadenas(grade, adresseMac) VALUES (:grade, :adresseMac)");

        // Liaison des valeurs aux paramètres de la requête


        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':adresseMac', $adresseMac);

        // Exécution de la requête
        $stmt->execute();

        // Ajout d'un message de succès à la réponse JSON
        $response['success'] = true;
        $response['message'] = "ajout valider";

    } catch (PDOException $e) {
        // En cas d'erreur, ajout d'un message d'erreur à la réponse JSON
        $response['success'] = false;
        $response['message'] = "Erreur lors de l'insertion dans la base de données: " . $e->getMessage();
    }
} else {
    // Si la méthode de la requête n'est pas POST, ajout d'un message d'erreur à la réponse JSON
    $response['success'] = false;
    $response['message'] = "Méthode de requête incorrecte.";
}

// Conversion du tableau en JSON
echo json_encode($response);

