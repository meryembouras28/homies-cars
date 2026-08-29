<?php
/**
 * auth.php — Gère la connexion, l'inscription et la déconnexion.
 *
 * Remplace iindex.php qui : contenait une erreur de syntaxe fatale,
 * n'était appelé par aucun formulaire (les champs n'étaient même pas
 * dans une balise <form>), stockait/comparait les mots de passe en
 * clair, et construisait ses requêtes SQL par concaténation directe
 * (injection SQL).
 */

session_start();
require 'db.php';

/**
 * Ne redirige que vers une page connue du site après connexion,
 * pour éviter une redirection ouverte (open redirect) si quelqu'un
 * bricole le paramètre ?next= dans l'URL.
 */
function safe_next(?string $next): ?string {
    if (!$next) return null;
    $allowedPrefixes = ['reservation1.php', 'mes-reservations.php', 'vehicules.php', 'homies_cars.php'];
    foreach ($allowedPrefixes as $prefix) {
        if (strpos($next, $prefix) === 0) {
            return $next;
        }
    }
    return null;
}

$action = $_GET['action'] ?? null;

/* ---------------------- Déconnexion ---------------------- */
if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    header("Location: homies_cars.php");
    exit();
}

/* ------------------------ Connexion ------------------------ */
if (isset($_POST['login-submit'])) {
    $usernameOrEmail = trim($_POST['username_email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $next            = safe_next($_POST['next'] ?? null);

    if ($usernameOrEmail === '' || $password === '') {
        header("Location: login.html?error=emptyfields" . ($next ? "&next=" . urlencode($next) : ''));
        exit();
    }

    $stmt = $conn->prepare("SELECT id, firstname, password FROM users WHERE email = ? OR username = ? LIMIT 1");
    $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['prenom']  = $user['firstname'];
        header("Location: " . ($next ?: 'homies_cars.php'));
        exit();
    } else {
        header("Location: login.html?error=invalidcredentials" . ($next ? "&next=" . urlencode($next) : ''));
        exit();
    }
}

/* ------------------------ Inscription ------------------------ */
if (isset($_POST['register-submit'])) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $next      = safe_next($_POST['next'] ?? null);
    $nextParam = $next ? "&next=" . urlencode($next) : '';

    if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
        header("Location: login.html?error=emptyfields" . $nextParam);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: login.html?error=invalidemail" . $nextParam);
        exit();
    }
    if (strlen($password) < 6) {
        header("Location: login.html?error=passwordtooshort" . $nextParam);
        exit();
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $check->close();
        header("Location: login.html?error=emailexists" . $nextParam);
        exit();
    }
    $check->close();

    // Nom d'utilisateur dérivé de l'email, rendu unique si besoin
    $baseUsername = strstr($email, '@', true) ?: $email;
    $username = $baseUsername;
    $suffix = 1;
    while (true) {
        $u = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $u->bind_param('s', $username);
        $u->execute();
        $u->store_result();
        $exists = $u->num_rows > 0;
        $u->close();
        if (!$exists) break;
        $username = $baseUsername . $suffix;
        $suffix++;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $firstname, $lastname, $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: login.html?success=accountcreated" . $nextParam);
        exit();
    } else {
        $stmt->close();
        header("Location: login.html?error=registerfailed" . $nextParam);
        exit();
    }
}

header("Location: homies_cars.php");
exit();
