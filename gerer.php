<?php
// Connexion à la base de données
include 'config2.php';

$pdo = get_pdo_instance();

// Vérifier la connexion
if (!$pdo) {
    die("Connection failed: " . $pdo->errorInfo()[2]); // Affiche l'erreur de connexion
}

// Requête SQL pour récupérer les données
$sql = "SELECT id, username, grade, password, mail, numero FROM fablab2024.inscrit";
$result = $pdo->query($sql);

$data = array();

if ($result) {
    // Ajouter chaque ligne de données à un tableau
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }
}

// Renvoyer les données au format JSON
echo json_encode($data);

// Fermer la connexion à la base de données
$pdo = null;
?>
