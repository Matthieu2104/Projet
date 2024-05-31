<?php
require_once('config2.php');

date_default_timezone_set('Europe/Paris');

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

        // Préparation de la requête pour récupérer le dernier log de l'utilisateur actuel
        $logStmt = $pdo->prepare('SELECT anne, mois, jour, heure, minutes FROM fablab2024.logs WHERE inscrit_id = :userId ORDER BY anne DESC, mois DESC, jour DESC, heure DESC, minutes DESC LIMIT 1');
        $logStmt->execute(['userId' => $userId]);

        $lastLogin = 'Jamais';
        $presence = 'Pas présent';
        if ($log = $logStmt->fetch(PDO::FETCH_ASSOC)) {
            // Créer un objet DateTime pour la dernière date de connexion
            $logDateStr = sprintf('%04d-%02d-%02d %02d:%02d:00', $log['anne'], $log['mois'], $log['jour'], $log['heure'], $log['minutes']);
            $logDate = new DateTime($logDateStr);
            $lastLogin = $logDate->format('Y-m-d H:i:s');

            // Calculer la différence entre la date actuelle et la dernière connexion
            $currentDate = new DateTime();
            $interval = $currentDate->diff($logDate);
            $differenceInMinutes = abs(($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i);

            // Vérifier si la différence est de 5 minutes ou moins
            if ($differenceInMinutes <= 60) {
                $presence = 'Présent';
            }
        }

        // Ajouter l'utilisateur au tableau
        $usersWithIntervalOK[] = [
            'id' => $userId,
            'name' => $userName,
            'mail' => $userMail,
            'grade' => $userGrade,
            'last_login' => $lastLogin,
            'presence' => $presence
        ];
    }

    // Retourner les utilisateurs au format JSON
    echo json_encode($usersWithIntervalOK);
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
}
?>




