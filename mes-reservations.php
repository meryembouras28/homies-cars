<?php
/**
 * mes-reservations.php — Historique des réservations du client connecté.
 *
 * Complète le CRUD sur les réservations : reservation1.php gère déjà
 * la création (Create) ; cette page ajoute la consultation (Read) et
 * l'annulation (Update du statut — "soft delete", on garde
 * l'historique plutôt que de supprimer la ligne, ce qui est la
 * pratique standard pour un système de réservation).
 */
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=loginrequired&next=" . urlencode('mes-reservations.php'));
    exit();
}
$userId = (int) $_SESSION['user_id'];

/* ---- Annulation (Update) ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $reservationId = (int) $_POST['cancel_id'];

    // On ne peut annuler que SA PROPRE réservation (vérifié dans le WHERE)
    $stmt = $conn->prepare(
        "UPDATE reservations SET statut = 'annulee'
         WHERE id = ? AND user_id = ? AND statut IN ('en_attente','confirmee')"
    );
    $stmt->bind_param('ii', $reservationId, $userId);
    $stmt->execute();
    $stmt->close();

    header("Location: mes-reservations.php?cancelled=1");
    exit();
}

/* ---- Consultation (Read) ---- */
$stmt = $conn->prepare(
    "SELECT r.id, r.date_debut, r.date_fin, r.prix_total, r.statut,
            v.marque, v.modele, a.nom AS agence_nom, a.ville AS agence_ville
     FROM reservations r
     JOIN voitures v ON v.id = r.voiture_id
     JOIN agences  a ON a.id = r.agence_id
     WHERE r.user_id = ?
     ORDER BY r.date_debut DESC"
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$reservations = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homies Cars | Mes réservations</title>
  <link rel="stylesheet" href="navbar.css">
  <style>
    body { background:#000; margin:0; min-height:100vh; font-family: Arial, sans-serif; }
    h1 { color:#FFD700; text-align:center; margin-top:30px; }
  </style>
</head>
<body>
    <nav class="hc-navbar">
      <div class="hc-nav-container">
        <div class="hc-logo"><img src="11.png" alt="Homies Cars"></div>
        <ul class="hc-nav-links">
          <li><a href="homies_cars.php">Accueil</a></li>
          <li><a href="vehicules.php">Véhicules</a></li>
          <li><a href="reservation1.php">Réservation</a></li>
          <li><a href="mes-reservations.php" class="hc-active">Mes réservations</a></li>
          <li><a href="auth.php?action=logout">Déconnexion</a></li>
          <li><a href="contact.html">Aide</a></li>
        </ul>
      </div>
    </nav>

    <h1>Mes réservations</h1>

    <?php if (isset($_GET['cancelled'])): ?>
      <div class="hc-alert hc-alert-success" style="display:block;">Réservation annulée.</div>
    <?php endif; ?>

    <div class="hc-table-wrap">
      <?php if ($reservations->num_rows === 0): ?>
        <p style="color:#eaeaea;text-align:center;">
          Vous n'avez aucune réservation. <a href="vehicules.php" style="color:#FFD700;">Découvrir les véhicules</a>
        </p>
      <?php else: ?>
        <table class="hc-res-table">
          <thead>
            <tr>
              <th>Véhicule</th>
              <th>Agence</th>
              <th>Du</th>
              <th>Au</th>
              <th>Prix total</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php while ($r = $reservations->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['marque'] . ' ' . $r['modele']); ?></td>
                <td><?php echo htmlspecialchars($r['agence_nom'] . ' — ' . $r['agence_ville']); ?></td>
                <td><?php echo htmlspecialchars($r['date_debut']); ?></td>
                <td><?php echo htmlspecialchars($r['date_fin']); ?></td>
                <td><?php echo number_format($r['prix_total'], 0, ',', ' '); ?> MAD</td>
                <td><span class="hc-badge hc-badge-<?php echo $r['statut']; ?>"><?php echo $r['statut']; ?></span></td>
                <td>
                  <?php if (in_array($r['statut'], ['en_attente', 'confirmee'])): ?>
                    <form method="post" onsubmit="return confirm('Annuler cette réservation ?');" style="margin:0;">
                      <input type="hidden" name="cancel_id" value="<?php echo $r['id']; ?>">
                      <button type="submit" class="hc-cancel-btn">Annuler</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <footer class="hc-footer-mini">Homies cars © 2024 tous les droits sont réservés</footer>
</body>
</html>
