<?php
/**
 * db.php — Connexion centralisée à la base de données.
 *
 * Avant : chacun des 3 scripts PHP (iindex.php, reservation1.php,
 * contact.php) ouvrait sa propre connexion vers une base différente
 * (respectivement "users", "reservation", "probleme" — jamais créées).
 * Désormais tous les scripts incluent ce fichier unique et travaillent
 * sur la même base "homies_cars".
 */

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname     = "homies_cars";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // On ne montre jamais le détail de l'erreur SQL à l'utilisateur final
    error_log("Erreur de connexion DB : " . $e->getMessage());
    http_response_code(500);
    die("Le service est momentanément indisponible. Merci de réessayer plus tard.");
}
