<?php
define('DB_SERVER', '51.210.151.13');
define('DB_USERNAME', 'fablab');
define('DB_PASSWORD', 'Fablab2024!');
define('DB_NAME', 'fablab2024');

function get_pdo_instance()
{
    try {
        $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connexion � la base de donn�es impossible: " . $e->getMessage());
    }
}

// R�cup�ration de l'ID de l'inscrit
$inscrit_id = isset($_GET['inscrit_id']) ? $_GET['inscrit_id'] : '';

if (empty($inscrit_id)) {
    echo json_encode(array("status" => "error", "message" => "Inscrit ID manquant"));
    exit();
}

$pdo = get_pdo_instance();

try {
    // Requ�te SQL pour r�cup�rer le temps de pr�sence total de l'utilisateur pour le jour actuel
    $query = "SELECT SUM(temps) as total_temps FROM logs WHERE inscrit_id = ? AND DATE(CONCAT(anne, '-', mois, '-', jour)) = CURDATE()";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$inscrit_id]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_temps = $row['total_temps'];
        echo json_encode(array('temps' => $total_temps));
    } else {
        echo json_encode(array("status" => "error", "message" => "Aucune donn�e trouv�e pour cet ID"));
    }
} catch (PDOException $e) {
    echo json_encode(array("status" => "error", "message" => "Erreur de requ�te: " . $e->getMessage()));
}

// Fermer la connexion PDO
$pdo = null;
?>
