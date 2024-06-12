<?php
include 'config2.php';

// Vérifier si des données sont envoyées en POST

// Préparer la requête SQL pour mettre à jour les données de l'employé
$pdo = get_pdo_instance();

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Préparer la requête SQL pour sélectionner les détails de l'employé correspondant à l'ID
    $sql = "SELECT nom, grade, adresseMac FROM fablab2024.cadenas WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Exécuter la requête
    $stmt->execute();

    // Récupérer les résultats
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Retourner les données sous forme de JSON
        echo json_encode($data);
    } else {
        // Si aucun résultat trouvé pour cet ID, retourner un message d'erreur
        echo json_encode(array("error" => "Aucun employé trouvé avec cet ID"));
    }

    // Fermer la connexion à la base de données
    $pdo = null;
} else {
    // Si aucun ID n'est passé en paramètre GET, retourner un message d'erreur
    echo json_encode(array("error" => "ID d'employé manquant dans la requête"));
}
?>
