<?php
// Récupérer le JSON brut de la requête HTTP
$json_raw = file_get_contents('php://input');

// Vérifier si le JSON est vide
if (!empty($json_raw)) {
// Décoder le JSON en un tableau associatif
$jsonData = json_decode($json_raw, true);

// Vérifier si le décodage a réussi
if ($jsonData !== null) {
// Utiliser les données JSON comme nécessaire
foreach ($jsonData as $key => $value) {
echo "Clé : $key, Valeur : $value";
}
} else {
echo "Erreur lors du décodage JSON";
}
} else {
echo "Aucun JSON reçu";
}
?>
