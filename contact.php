<?php
/**
 * contact.php — Traite le formulaire "Aide / Contact".
 *
 * Corrections : le formulaire envoie désormais en POST (les
 * coordonnées ne doivent pas se retrouver dans l'URL) ; la variable
 * $problemee (non définie) a été corrigée en $probleme, ce qui
 * provoquait une erreur fatale à l'exécution ; connexion centralisée
 * via db.php (même base que le reste du site) ; redirection avec
 * message de confirmation au lieu d'une page blanche.
 */
session_start();
require 'db.php';

$nom      = trim($_POST["nom"] ?? '');
$email    = trim($_POST["email"] ?? '');
$tel      = trim($_POST["tel"] ?? '');
$probleme = trim($_POST["probleme"] ?? '');

if ($nom === '' || $email === '' || $tel === '' || $probleme === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: contact.html?error=1");
    exit();
}

$statement = $conn->prepare("INSERT INTO messages_contact (nom, email, tel, probleme) VALUES (?, ?, ?, ?)");
$statement->bind_param('ssss', $nom, $email, $tel, $probleme);

if ($statement->execute()) {
    $statement->close();
    header("Location: contact.html?sent=1");
} else {
    $statement->close();
    header("Location: contact.html?error=1");
}
exit();
