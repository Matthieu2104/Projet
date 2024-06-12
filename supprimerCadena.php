<?php
// Connexion à la base de données
include 'config2.php';

$pdo = get_pdo_instance();


// Vérifier si l'ID à supprimer est passé en paramètre
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Requête pour supprimer l'enregistrement avec l'ID spécifié
    $sql = "DELETE FROM fablab2024.cadenas WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Succès de la suppression
        echo json_encode(array("message" => "L'enregistrement a ete supprime avec succes."));
    } else {
        // Erreur lors de la suppression
        echo json_encode(array("error" => "Erreur lors de la suppression de l'enregistrement: " ));
    }
} else {
    // Aucun ID spécifié
    echo json_encode(array("error" => "Aucun ID spécifié pour la suppression."));
}

// Fermer la connexion à la base de données
$pdo = null;
?>

