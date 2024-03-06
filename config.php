<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'projet');
define('DB_NAME', 'user');

function get_pdo_instance()
{
    header('Content-Type: application/json');

    /* Attempt to connect to MySQL database */
    try {
        $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $return["success"] = true;
        $return["message"] = "Connexion a la base de donnees reussie";

        // Prépare et exécute la requête SELECT
        $requete = $pdo->prepare("SELECT * FROM user.utilisateur");
        $requete->execute();
        $results = $requete->fetchAll(PDO::FETCH_ASSOC);

        // Affiche les résultats
        var_dump($results);

    } catch (PDOException $e) {
        $return["success"] = false;
        $return["message"] = "Connexion a la base de donnees impossible: " . $e->getMessage();
    }

    // Retourne les résultats encodés en JSON
    echo json_encode($return);
}

// Appelle la fonction pour obtenir les résultats
get_pdo_instance();
?>