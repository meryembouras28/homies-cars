<?php

session_start();
require 'db.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: text/plain; charset=utf-8');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo "Vous devez être connecté pour réserver.";
        exit();
    }
    $userId = (int) $_SESSION['user_id'];

    $voitureId = (int) ($_POST['voiture_id'] ?? 0);
    $agenceId  = (int) ($_POST['agence_id'] ?? 0);
    $dateDebut = $_POST['date-debut'] ?? '';
    $dateFin   = $_POST['date-fin'] ?? '';

    if ($voitureId <= 0 || $agenceId <= 0 || $dateDebut === '' || $dateFin === '') {
        echo "Merci de remplir tous les champs.";
        exit();
    }

    $today = date('Y-m-d');
    if ($dateDebut < $today) {
        echo "La date de départ ne peut pas être dans le passé.";
        exit();
    }
    if (strtotime($dateFin) <= strtotime($dateDebut)) {
        echo "La date de fin doit être après la date de début.";
        exit();
    }

    
    $carStmt = $conn->prepare("SELECT prix_jour FROM voitures WHERE id = ? AND statut = 'disponible'");
    $carStmt->bind_param('i', $voitureId);
    $carStmt->execute();
    $car = $carStmt->get_result()->fetch_assoc();
    $carStmt->close();

    if (!$car) {
        echo "Ce véhicule n'est plus disponible.";
        exit();
    }

    $agStmt = $conn->prepare("SELECT id FROM agences WHERE id = ?");
    $agStmt->bind_param('i', $agenceId);
    $agStmt->execute();
    if ($agStmt->get_result()->num_rows === 0) {
        $agStmt->close();
        echo "Agence invalide.";
        exit();
    }
    $agStmt->close();

    
    $checkStmt = $conn->prepare(
        "SELECT id FROM reservations
         WHERE voiture_id = ?
           AND statut IN ('en_attente','confirmee')
           AND ? < date_fin AND ? > date_debut"
    );
    $checkStmt->bind_param('iss', $voitureId, $dateDebut, $dateFin);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        echo "Désolé, ce véhicule n'est pas disponible pour les dates sélectionnées.";
        exit();
    }
    $checkStmt->close();

    $nbJours = (strtotime($dateFin) - strtotime($dateDebut)) / 86400;
    $prixTotal = $nbJours * $car['prix_jour'];

    $insertStmt = $conn->prepare(
        "INSERT INTO reservations (user_id, voiture_id, agence_id, date_debut, date_fin, prix_total)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $insertStmt->bind_param('iiissd', $userId, $voitureId, $agenceId, $dateDebut, $dateFin, $prixTotal);

    if ($insertStmt->execute()) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Erreur lors de l'enregistrement de la réservation.";
    }
    $insertStmt->close();
    exit();
}



if (!isset($_SESSION['user_id'])) {
    $next = 'reservation1.php' . (isset($_GET['voiture_id']) ? '?voiture_id=' . (int)$_GET['voiture_id'] : '');
    header("Location: login.html?error=loginrequired&next=" . urlencode($next));
    exit();
}


$userStmt = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$userStmt->bind_param('i', $_SESSION['user_id']);
$userStmt->execute();
$currentUser = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

$voitures = $conn->query("SELECT id, marque, modele, prix_jour FROM voitures WHERE statut = 'disponible' ORDER BY marque");
$agences  = $conn->query("SELECT id, nom, ville FROM agences ORDER BY ville");

$preselectedVoiture = isset($_GET['voiture_id']) ? (int) $_GET['voiture_id'] : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homies Cars | Réservation</title>
  <link rel="stylesheet" href="navbar.css">
  <link rel="stylesheet" href="reservation1.css">
</head>
<body>
    <nav class="hc-navbar">
      <div class="hc-nav-container">
        <div class="hc-logo"><img src="11.png" alt="Homies Cars"></div>
        <ul class="hc-nav-links">
          <li><a href="homies_cars.php">Accueil</a></li>
          <li><a href="vehicules.php">Véhicules</a></li>
          <li><a href="reservation1.php" class="hc-active">Réservation</a></li>
          <li><a href="mes-reservations.php">Mes réservations</a></li>
          <li><a href="auth.php?action=logout">Déconnexion</a></li>
          <li><a href="contact.html">Aide</a></li>
        </ul>
      </div>
    </nav>
    <div class="background">
        <div class="booking-form">
            <h2>Réservation</h2>
            <p style="color:#eaeaea;font-size:14px;margin-top:-10px;">
                Pour : <strong><?php echo htmlspecialchars($currentUser['firstname'] . ' ' . $currentUser['lastname']); ?></strong>
                (<?php echo htmlspecialchars($currentUser['email']); ?>)
            </p>

            <div id="success-message" class="hc-alert hc-alert-success"></div>
            <div id="error-message" class="hc-alert hc-alert-error"></div>

            <form id="reservation-form" onsubmit="return submitForm(event)">
                <label for="vehicule">Type de véhicule :</label>
                <select class="booking-form select" name="voiture_id" id="vehicule" required>
                    <option value="">Sélectionnez votre voiture</option>
                    <?php while ($v = $voitures->fetch_assoc()): ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo ($v['id'] == $preselectedVoiture) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?> — <?php echo number_format($v['prix_jour'], 0, ',', ' '); ?> MAD/jour
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="agence">Agence de récupération :</label>
                <select class="booking-form select" name="agence_id" id="agence" required>
                    <option value="">Sélectionnez une agence</option>
                    <?php while ($a = $agences->fetch_assoc()): ?>
                        <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['nom'] . ' — ' . $a['ville']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="date-debut">Date de départ:</label>
                <input type="date" name="date-debut" id="date-debut" min="<?php echo date('Y-m-d'); ?>" required>

                <label for="date-fin">Date de Fin:</label>
                <input type="date" name="date-fin" id="date-fin" required>

                <button type="submit">Réservez</button>
            </form>
        </div>
    </div>
    <script src="reservation1.js"></script>
</body>
</html>
