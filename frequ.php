<?php
// Inclure le fichier de configuration
require_once 'config2.php';

date_default_timezone_set('Europe/Paris');

try {
    // Obtenir une instance PDO
    $pdo = get_pdo_instance();

    // Récupérer les données de la table logs
    $sql = "SELECT id, anne, mois, jour, heure, minutes, passage FROM fablab2024.logs";
    $stmt = $pdo->query($sql);

    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    // Initialiser un tableau pour stocker les fréquentations
    $frequentation = array_fill_keys($jours, array_fill(0, 6, 0));

    // Créer un formateur de date pour obtenir le nom du jour de la semaine
    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Paris', IntlDateFormatter::GREGORIAN, 'EEEE');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dateString = $row['anne'] . '-' . $row['mois'] . '-' . $row['jour'];
        $timestamp = strtotime($dateString);
        $jourSemaine = ucfirst($formatter->format($timestamp));

        // Trouver l'index du jour de la semaine
        $indexJour = array_search($jourSemaine, $jours);

        // Déterminer le créneau horaire en tenant compte des heures et des minutes
        $heure = (int)$row['heure'];
        $minutes = (int)$row['minutes'];
        $creneau = -1;

        if ($heure === 8 && $minutes >= 30) {
            $creneau = 0;
        } elseif (($heure === 8 && $minutes < 30) || ($heure === 10 && $minutes >= 30)) {
            $creneau = 1;
        } elseif (($heure === 10 && $minutes < 30) || ($heure === 12 && $minutes >= 30)) {
            $creneau = 2;
        } elseif (($heure === 12 && $minutes < 30) || ($heure === 14 && $minutes >= 30)) {
            $creneau = 3;
        } elseif (($heure === 14 && $minutes < 30) || ($heure === 16 && $minutes >= 30)) {
            $creneau = 4;
        } elseif (($heure === 16 && $minutes < 30) || ($heure === 18 && $minutes >= 30)) {
            $creneau = 5;
        } elseif ($heure === 18 && $minutes < 30) {
            $creneau = 6;
        } elseif ($heure === 18 && $minutes >= 30) {
            $creneau = 7;
        } elseif ($heure === 19 || ($heure >= 20 && $heure < 23)) {
            $creneau = 8;
        }

        if ($creneau != -1 && $indexJour !== false) {
            $frequentation[$jours[$indexJour]][$creneau] += $row['passage'];
        }
    }

    // Encodage des données en JSON pour les utiliser dans le fichier HTML
    header('Content-Type: application/json');
    echo json_encode($frequentation);

} catch (PDOException $e) {
    // En cas d'erreur, retourner un message d'erreur en JSON
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erreur de base de données: " . $e->getMessage()]);
} catch (Exception $e) {
    // Capturer d'autres exceptions génériques
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erreur: " . $e->getMessage()]);
}
?>
