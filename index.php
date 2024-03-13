<?php
// Include config file
//require_once "config.php";
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    //$user = htmlspecialchars($_POST["username"]);
    //$pass = htmlspecialchars($_POST["password"]);

    // Vous pouvez traiter les données comme vous le souhaitez ici
    // Par exemple, vérifier les informations de connexion, les enregistrer dans une base de données, etc.

    // Redirection vers une autre page après le traitement
   // header("Location: dashboard.html");
   // exit();
//}


// Inclure le fichier de configuration pour la connexion à la base de données
include 'config.php';

// Récupération des données du formulaire
$pdo = get_pdo_instance();

$username = "hadine";
$password = "popo";

$username_valid = false;
$password_valid = false;

if ($username && $password) {
    try {
        // Utilisation de requêtes préparées pour éviter les injections SQL
        $sql = "SELECT username, password FROM user.inscrit WHERE username = :username";
        $requete = $pdo->prepare($sql);
        $requete->bindParam(':username', $username, PDO::PARAM_STR);
        $requete->execute();

        while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
            if ($row['username'] === $username) {
                // Le nom d'utilisateur est valide
                $username_valid = true;
                break;
            }
        }

        if ($username_valid) {
            // Si le nom d'utilisateur est valide, vérifier le mot de passe
            if ($row['password'] === $password) {
                // Le nom d'utilisateur est valide
                $password_valid = true;
            }
        }

        // Envoyer une validation pour chaque étape si elles sont correctes
        if ($username_valid && $password_valid) {
            echo "Validation du nom d'utilisateur et du mot de passe réussie. Connexion réussie.";
        } elseif ($username_valid && !$password_valid) {
            echo "Nom d'utilisateur correct, mais le mot de passe est incorrect.";
        } else {
            echo "Nom d'utilisateur ou mot de passe incorrect.";
        }

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

$pdo = null;

?>
