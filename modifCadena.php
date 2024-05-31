<?php

// Connexion à la base de données
include 'config2.php';

// Vérifier si des données sont envoyées en POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données envoyées depuis le formulaire
    $id = $_POST['id']; // Correction du nom du champ pour récupérer l'ID
    $nom= $_POST['editNom'];
    $grade = $_POST['editGrade'];
    $adresseMac = $_POST['editAdresse'];

    // Préparer la requête SQL pour mettre à jour les données de l'employé
    $pdo = get_pdo_instance();

    try {
        $sql = "UPDATE fablab2024.cadenas SET nom=:nom, grade=:grade, adresseMac=:adresseMac WHERE id=:id";

        // Préparation de la requête
        $stmt = $pdo->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':adresseMac', $adresseMac);

        // Exécution de la requête
        $stmt->execute();

        // Ajout d'un message de succès à la réponse JSON
        $response['success'] = true;
        $response['message'] = "Modification validée";

    } catch (PDOException $e) {
        // En cas d'erreur, ajout d'un message d'erreur à la réponse JSON
        $response['success'] = false;
        $response['message'] = "Erreur lors de la modification dans la base de données: " . $e->getMessage();
    }
} else {
    // Si la méthode de la requête n'est pas POST, ajout d'un message d'erreur à la réponse JSON
    $response['success'] = false;
    $response['message'] = "Méthode de requête incorrecte.";
}

// Conversion du tableau en JSON
echo json_encode($response);

