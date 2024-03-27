<?php
// config.php

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
        die("Connection à la base de donnée impossible: " . $e->getMessage());
    }
}
?>
