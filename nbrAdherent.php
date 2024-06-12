<?php

include 'config2.php';

session_start();

// Vérifiez si l'utilisateur est authentifié
if (!isset($_SESSION['id'])) {
    die(json_encode(["error" => "Utilisateur non authentifié."]));
}

$pdo = get_pdo_instance();

// Vérifiez la connexion
if (!$pdo) {
    die(json_encode(["error" => "Échec de la connexion à la base de données : " . $pdo->errorInfo()[2]]));
}

// Requête SQL pour récupérer le nombre de personnes inscrites
$passage_sql = "SELECT COUNT(*) AS nombre_de_personnes FROM inscrit";

try {
    $requete = $pdo->prepare($passage_sql);
    $requete->execute();

    $data = $requete->fetch(PDO::FETCH_ASSOC);

    // Renvoyer les données au format JSON
    echo json_encode($data);
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    echo json_encode(["error" => "Erreur de requête : " . $e->getMessage()]);
}

// Fermer la connexion à la base de données
$pdo = null;
?>
