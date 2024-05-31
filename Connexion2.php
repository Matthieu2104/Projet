<?php
header('Content-Type: application/json');

// Inclure le fichier de configuration
require_once 'config2.php';

// Obtenir une instance de PDO
$pdo = get_pdo_instance();

// R�cup�rer les param�tres de la requ�te
$mail = isset($_GET['mail']) ? $_GET['mail'] : '';
$password = isset($_GET['password']) ? $_GET['password'] : '';

// V�rifier si les param�tres sont pr�sents
if (empty($mail) || empty($password)) {
    echo json_encode(array("status" => "error", "message" => "Email ou password manquant"));
    exit();
}

try {
    // Pr�parer et ex�cuter la requ�te SQL
    $stmt = $pdo->prepare("SELECT * FROM inscrit WHERE mail = :mail AND password = :password");
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // V�rifier si l'utilisateur existe
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array(
            "status" => "success",
            "id" => $user['id'],
            "mail" => $user['mail'],
            "password" => $user['password'],
            "grade" => $user['grade'],
            "numero" => $user['numero'] // Ajoutez cette ligne pour inclure le numero
        ));
    } else {
        echo json_encode(array("status" => "error", "message" => "Email ou Mot De Passe Incorrect"));
    }
} catch (PDOException $e) {
    echo json_encode(array("status" => "error", "message" => "Erreur de requ�te: " . $e->getMessage()));
}

// Fermer la connexion PDO
$pdo = null;
?>
