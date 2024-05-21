<?php

include 'config2.php';

session_start();

// Vérifiez si l'utilisateur est authentifié
if (!isset($_SESSION['id'])) {
    die(json_encode(["error" => "Utilisateur non authentifié."]));
}

$inscrit_id = $_SESSION['id'];

$pdo = get_pdo_instance();

// Vérifiez la connexion
if (!$pdo) {
    die(json_encode(["error" => "Échec de la connexion à la base de données : " . $pdo->errorInfo()[2]]));
}

// Requête SQL pour récupérer les données de passage pour l'utilisateur connecté
$passage_sql = "SELECT SUM(temps) FROM fablab2024.logs WHERE inscrit_id = :inscrit_id";

try {
    $requete = $pdo->prepare($passage_sql);
    $requete->bindParam(':inscrit_id', $inscrit_id, PDO::PARAM_INT);
    $requete->execute();

    $data = $requete->fetchAll(PDO::FETCH_ASSOC);

    // Renvoyer les données au format JSON
    echo json_encode($data);
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    echo json_encode(["error" => "Erreur de requête : " . $e->getMessage()]);
}

// Fermer la connexion à la base de données
$pdo = null;
?>
