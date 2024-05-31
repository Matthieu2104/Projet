<?php
header('Content-Type: application/json');

// Inclure le fichier de configuration
require_once 'config2.php';

// Obtenir une instance de PDO
$pdo = get_pdo_instance();

// Récupérer les paramètres de la requête
$userId = isset($_GET['userId']) ? $_GET['userId'] : '';

// Vérifier si le paramètre userId est présent
if (empty($userId)) {
    echo json_encode(array("status" => "error", "message" => "User ID manquant"));
    exit();
}

try {
    // Préparer et exécuter la requête SQL
    $stmt = $pdo->prepare("SELECT username, mail, grade, numero FROM inscrit WHERE id = :userId");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // Vérifier si l'utilisateur existe
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array(
            "status" => "success",
            "username" => $user['username'],
            "mail" => $user['mail'],
            "grade" => $user['grade'],
            "numero" => $user['numero']
        ));
    } else {
        echo json_encode(array("status" => "error", "message" => "Utilisateur non trouvé"));
    }
} catch (PDOException $e) {
    echo json_encode(array("status" => "error", "message" => "Erreur de requête: " . $e->getMessage()));
}

// Fermer la connexion PDO
$pdo = null;
?>
