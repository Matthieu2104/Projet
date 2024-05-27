<?php
require_once('config2.php');


date_default_timezone_set('Europe/Paris');

// Fonction pour récupérer une instance PDO
$pdo = get_pdo_instance();

// Fonction pour récupérer une instance PDO
$pdo = get_pdo_instance();

try {
    // Préparation de la requête pour sélectionner tous les utilisateurs
    $stmt = $pdo->prepare('SELECT id, username, mail, grade FROM fablab2024.inscrit');
    $stmt->execute();

    // Tableau pour stocker les utilisateurs avec un intervalle "OK"
    $usersWithIntervalOK = [];

    // Boucle tant qu'il y a des utilisateurs à traiter
    while ($utilisateur = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userId = $utilisateur['id'];
        $userName = $utilisateur['username'];
        $userMail = $utilisateur['mail'];
        $userGrade = $utilisateur['grade'];

        // Préparation de la requête pour récupérer les logs de l'utilisateur actuel
        $logStmt = $pdo->prepare('SELECT anne, mois, jour, heure, minutes FROM fablab2024.logs WHERE inscrit_id = :userId');
        $logStmt->execute(['userId' => $userId]);

        // Récupérer la date actuelle
        $currentDate = new DateTime();

        // Variable pour vérifier si l'utilisateur a au moins un intervalle OK
        $hasIntervalOK = false;

        // Boucle tant qu'il y a des logs à traiter pour l'utilisateur actuel
        while ($log = $logStmt->fetch(PDO::FETCH_ASSOC)) {
            // Créer un objet DateTime pour la date du log
            $logDateStr = sprintf('%04d-%02d-%02d %02d:%02d:00', $log['anne'], $log['mois'], $log['jour'], $log['heure'], $log['minutes']);
            $logDate = new DateTime($logDateStr);

            // Calculer la différence entre les deux dates
            $interval = $currentDate->diff($logDate);

            // Convertir la différence en minutes
            $differenceInMinutes = abs(($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i);

            // Vérifier si la différence est de 5 minutes ou moins
            if ($differenceInMinutes <= 5) {
                // Marquer que l'utilisateur a au moins un intervalle OK
                $hasIntervalOK = true;
                break;
            }
        }

        // Si l'utilisateur a au moins un intervalle OK, l'ajouter au tableau
        if ($hasIntervalOK) {
            $usersWithIntervalOK[] = ['id' => $userId, 'name' => $userName, 'mail' => $userMail, 'grade' => $userGrade];
        }
    }

    // Afficher les utilisateurs avec un intervalle OK
    echo json_encode($usersWithIntervalOK);
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>
