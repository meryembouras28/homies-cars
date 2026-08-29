<?php

session_start();
require 'db.php';

$categories = $conn->query("SELECT id, nom FROM categories ORDER BY nom");

$categorieId = isset($_GET['categorie']) ? (int) $_GET['categorie'] : null;

if ($categorieId) {
    $stmt = $conn->prepare(
        "SELECT v.id, v.marque, v.modele, v.prix_jour, v.image, v.description
         FROM voitures v
         WHERE v.statut = 'disponible' AND v.categorie_id = ?
         ORDER BY v.marque"
    );
    $stmt->bind_param('i', $categorieId);
    $stmt->execute();
    $voitures = $stmt->get_result();
} else {
    $voitures = $conn->query(
        "SELECT v.id, v.marque, v.modele, v.prix_jour, v.image, v.description
         FROM voitures v
         WHERE v.statut = 'disponible'
         ORDER BY v.marque"
    );
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="navbar.css" />
    <link rel="stylesheet" href="vehicules1.css" />
    <script src="https://unpkg.com/splitting/dist/splitting.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Homies Cars | Véhicules</title>
  </head>
  <body>
    <nav class="hc-navbar">
      <div class="hc-nav-container">
        <div class="hc-logo"><img src="11.png" alt="Homies Cars"></div>
        <ul class="hc-nav-links">
          <li><a href="homies_cars.php">Accueil</a></li>
          <li><a href="vehicules.php" class="hc-active">Véhicules</a></li>
          <li><a href="reservation1.php">Réservation</a></li>
          <?php if (isset($_SESSION['prenom'])): ?>
            <li><a href="mes-reservations.php">Mes réservations</a></li>
            <li><a href="auth.php?action=logout">Déconnexion</a></li>
          <?php else: ?>
            <li><a href="login.html">Se connecter</a></li>
          <?php endif; ?>
          <li><a href="contact.html">Aide</a></li>
        </ul>
      </div>
    </nav>

    <div class="hc-filter-bar">
      <a href="vehicules.php" class="hc-filter-btn <?php echo !$categorieId ? 'hc-active' : ''; ?>">Toutes</a>
      <?php while ($cat = $categories->fetch_assoc()): ?>
        <a href="vehicules.php?categorie=<?php echo $cat['id']; ?>"
           class="hc-filter-btn <?php echo ($categorieId === (int)$cat['id']) ? 'hc-active' : ''; ?>">
          <?php echo htmlspecialchars($cat['nom']); ?>
        </a>
      <?php endwhile; ?>
    </div>

    <main id="main">
      <?php if ($voitures->num_rows === 0): ?>
        <p style="grid-column:1/-1;color:#eaeaea;">Aucun véhicule disponible dans cette catégorie pour le moment.</p>
      <?php endif; ?>
      <?php while ($v = $voitures->fetch_assoc()): ?>
        <a href="reservation1.php?voiture_id=<?php echo $v['id']; ?>" class="card-link">
          <div class="card" tabindex="0">
            <img src="<?php echo htmlspecialchars($v['image']); ?>" alt="<?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?>" />
            <div class="text">
              <h2 data-splitting=""><?php echo htmlspecialchars($v['marque'] . ' ' . $v['modele']); ?></h2>
              <p data-splitting=""><?php echo htmlspecialchars($v['description']); ?></p>
              <p style="margin-top:8px;font-weight:700;color:#fff;"><?php echo number_format($v['prix_jour'], 0, ',', ' '); ?> MAD / jour</p>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </main>
    <footer class="hc-footer-mini">Homies cars © 2024 tous les droits sont réservés</footer>
    <script src="vehicules11.js"></script>
  </body>
</html>
