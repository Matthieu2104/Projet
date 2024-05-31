if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Validation des données (optionnel mais recommandé)
    if (filter_var($email) && !empty($name) && !empty($message)) {
        // Destinataire de l'email
        $to = 'fablabpass@gmail.com';
        // Sujet de l'email
        $subject = 'Nouveau message de ' . $name;
        // Corps de l'email
        $body = "Nom: $name\nEmail: $email\n\nMessage:\n$message";
        // En-têtes de l'email
        $headers = "From: $email";

        // Envoi de l'email
        if (mail($to, $subject, $body, $headers)) {
            echo "L'email a été envoyé avec succès.";
        } else {
            echo "Une erreur s'est produite lors de l'envoi de l'email.";
        }
    } else {
        echo "Veuillez fournir des informations valides.";
    }
} else {
    echo "Méthode de requête non supportée";
}
?>



