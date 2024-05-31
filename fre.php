<?php
// Inclure le fichier de configuration
require_once 'config2.php';

date_default_timezone_set('Europe/Paris');

try {
    // Obtenir une instance PDO � partir de la fonction d�finie dans config2.php
    $pdo = get_pdo_instance();

    // R�cup�rer le num�ro de la semaine actuelle et l'ann�e actuelle
    $currentWeekNumber = date("W");
    $currentYear = date("Y");

    // R�cup�rer les donn�es de la table logs pour la semaine actuelle
    $sql = "SELECT anne, mois, jour, heure, minutes, passage FROM fablab2024.logs 
            WHERE WEEKOFYEAR(DATE(CONCAT(anne, '-', mois, '-', jour))) = ? AND anne = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentWeekNumber, $currentYear]);

    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    $timeSlots = [
        "8h30-10h30", "10h30-12h30", "12h30-14h00", 
        "14h00-16h00", "16h00-18h00", "18h00-23h00"
    ];

    // Initialiser un tableau pour stocker les fr�quentations
    $frequentation = [];
    foreach ($jours as $jour) {
        $frequentation[$jour] = array_fill_keys($timeSlots, 0);
    }

    // Cr�er un formateur de date pour obtenir le nom du jour de la semaine
    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Paris', IntlDateFormatter::GREGORIAN, 'EEEE');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dateString = $row['anne'] . '-' . $row['mois'] . '-' . $row['jour'];
        $timestamp = strtotime($dateString);
        $jourSemaine = ucfirst($formatter->format($timestamp));

        // Trouver l'index du jour de la semaine
        $indexJour = array_search($jourSemaine, $jours);

        // D�terminer le cr�neau horaire en tenant compte des heures et des minutes
        $heure = (int)$row['heure'];
        $minutes = (int)$row['minutes'];
        $creneau = '';

        if ($heure >= 8 && $heure < 10) {
            $creneau = "8h30-10h30";
        } elseif ($heure >= 10 && $heure < 12) {
            $creneau = "10h30-12h30";
        } elseif ($heure >= 12 && $heure < 14) {
            $creneau = "12h30-14h00";
        } elseif ($heure >= 14 && $heure < 16) {
            $creneau = "14h00-16h00";
        } elseif ($heure >= 16 && $heure < 18) {
            $creneau = "16h00-18h00";
        } elseif ($heure >= 18 && $heure < 23) {
            $creneau = "18h00-23h00";
        }

        if ($creneau !== '' && $indexJour !== false) {
            $frequentation[$jours[$indexJour]][$creneau] += $row['passage'];
        }
    }

    // Encodage des donn�es en JSON pour les utiliser dans le fichier HTML
    header('Content-Type: application/json');
    echo json_encode($frequentation);

} catch (PDOException $e) {
    // En cas d'erreur, retourner un message d'erreur en JSON
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erreur de base de donn�es: " . $e->getMessage()]);
} catch (Exception $e) {
    // Capturer d'autres exceptions g�n�riques
    header('Content-Type: application/json');
    echo json_encode(["error" => "Erreur: " . $e->getMessage()]);
}
?>
