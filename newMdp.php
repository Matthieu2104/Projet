<?php
include 'config2.php';

// Vérification si la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Récupération des données envoyées depuis le formulaire
    $mail = $_POST['email'];
    $newPassword = $_POST['new-passworde'];

    $pdo = get_pdo_instance();

    try {
        // Exécution de la requête pour récupérer le grade
        $gradeStmt = $pdo->prepare("SELECT grade FROM fablab2024.inscrit WHERE mail = :mail");
        $gradeStmt->bindParam(':mail', $mail);
        $gradeStmt->execute();
        $gradeResult = $gradeStmt->fetch(PDO::FETCH_ASSOC);
        $grade = $gradeResult['grade'];

        // Exécution de la requête pour récupérer le nom d'utilisateur
        $usernameStmt = $pdo->prepare("SELECT username FROM fablab2024.inscrit WHERE mail = :mail");
        $usernameStmt->bindParam(':mail', $mail);
        $usernameStmt->execute();
        $usernameResult = $usernameStmt->fetch(PDO::FETCH_ASSOC);
        $username = $usernameResult['username'];

        // Préparation de la requête de mise à jour
        $stmt = $pdo->prepare("UPDATE fablab2024.inscrit SET password = :newPassword WHERE mail = :mail");

        // Liaison des valeurs aux paramètres de la requête
        $stmt->bindParam(':newPassword', $newPassword);
        $stmt->bindParam(':mail', $mail);

        // Exécution de la requête
        $stmt->execute();

        // Ajout d'un message de succès à la réponse JSON
        $response['success'] = true;
        $response['message'] = "ok";

    } catch (PDOException $e) {
        // En cas d'erreur, ajout d'un message d'erreur à la réponse JSON
        $response['success'] = false;
        $response['message'] = "Erreur lors de la mise à jour dans la base de données: " . $e->getMessage();
    }
} else {
    // Si la méthode de la requête n'est pas POST, ajout d'un message d'erreur à la réponse JSON
    $response['success'] = false;
    $response['message'] = "Méthode de requête incorrecte.";
}

// Conversion du tableau en JSON
echo json_encode($response);
?>

