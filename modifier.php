<?php
// Connexion à la base de données
include 'config2.php';

// Vérifier si des données sont envoyées en POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données envoyées depuis le formulaire
    $id = $_POST['id']; // Correction du nom du champ pour récupérer l'ID
    $username = $_POST['editName'];
    $grade = $_POST['editGrade'];
    $password = $_POST['editPassword'];
    $mail = $_POST['editMail'];
    $numero = $_POST['editNumero'];

    // Préparer la requête SQL pour mettre à jour les données de l'employé
    $pdo = get_pdo_instance();

    try {
        $sql = "UPDATE fablab2024.inscrit SET username=:username, grade=:grade, password=:password, mail=:mail, numero=:numero WHERE id=:id";

        // Préparation de la requête
        $stmt = $pdo->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':numero', $numero);

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
?>
