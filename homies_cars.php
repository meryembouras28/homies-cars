<?php
session_start();
$isLoggedIn = isset($_SESSION['prenom']);
$prenom = $isLoggedIn ? htmlspecialchars($_SESSION['prenom']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homies Cars</title>
  
    <link rel="stylesheet" href="homies_carss.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
      
        <nav>
            <div class="container nav-container">
                <img class="logo"  src="11.png" alt="" width="180px"  >
                <ul class="nav-link">
                    <li><a href="homies_cars.php" style="--i:1;" class="active">Acceuil</a></li>
                    <li><a href="vehicules.php" style="--i:2;">véhicules</a></li>
                    <li><a href="reservation1.php" style="--i:3;">Réservation</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="mes-reservations.php" style="--i:4;">Mes réservations</a></li>
                    <li><a href="auth.php?action=logout" style="--i:6;">Bonjour <?php echo $prenom; ?> · Déconnexion</a></li>
                    <?php else: ?>
                    <li><a href="login.html" style="--i:5;">se connecter</a></li>
                    <?php endif; ?>
                    <li><a href="contact.html" style="--i:7;">Aide</a></li>
                    
                    
                    
                </ul>
               

                 
          
 
        </nav>

        <header>
            <div class="container header-container">
                <div class="header-left">
                    <h1>Réservez Votre Voiture </h1>
                    <h3>Moins CHER Chez Nous!</h3>
                    <p>
                        Plongez dans l'univers du luxe avec Homies Cars. Notre sélection de véhicules haut de gamme et notre service irréprochable vous promettent une expérience de conduite mémorable.    <br> Prêt à prendre la route ?     <br>Cliquez sur 'Réservez maintenant' et lancez-vous dans votre prochaine aventure en toute élégance.   <br>
                    </p>
                    <a href="vehicules.php" class="btn">Réservez Maintenant</a>
                </div>
                <div class="header-right">
                    <div class="sq-box">
                        <img src="im8.png" alt="">
                    </div>
                </div>
            </div>
            <div class="sq-box2">
              
              </div>
        </header>
       
        <footer>
            
  	 <div class="container">
  	 	<div class="row">
  	 		<div class="footer-col">
  	 			<h4>Entreprise</h4>
  	 			<ul>
  	 				<li><a href="#">À propos de nous</a></li>
  	 				<li><a href="#">nos services</a></li>
  	 				<li><a href="#">Politique de confidentialité</a></li>
  	 				<li><a href="#">Programme d'affiliation</a></li>
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>Obtenir de l'aide</h4>
  	 			<ul>
  	 				<li><a href="#">Maroc Oujda</a></li>
  	 				<li><a href="#">+212 642420376</a></li>
  	 				<li><a href="#">FIX 0576874356</a></li>
  	 				<li><a href="#">Homiescars@gmail.com</a></li>
  	 				<li><a href="#">options de paiment</a></li>
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>Options</h4>
  	 			<ul>
  	 				<li><a href="#">Homies Partenaires</a></li>
  	 				<li><a href="#">Homies Blog</a></li>
  	 				<li><a href="#">Groupe Homies</a></li>
  	 				<li><a href="#">Recrutement</a></li>
  	 			</ul>
  	 		</div>
  	 		<div class="footer-col">
  	 			<h4>Suivez-nous</h4>
  	 			<div class="social-links">
  	 				<a href="#"><i class="fab fa-facebook-f"></i></a>
  	 				<a href="#"><i class="fab fa-twitter"></i></a>
  	 				<a href="#"><i class="fab fa-instagram"></i></a>
  	 				<a href="#"><i class="fab fa-linkedin-in"></i></a>
  	 			</div>
  	 		</div>
  	 	</div>
  	 </div>
      <!-- <img
       class="card__img"
       src="C:\Users\User\Downloads\aoostrore.png"
       alt="Snowy Mountains"
     />-->
       <p class="footer-company-name">Homies cars © 2024 tous les droits sont réservés</p>
  

        </footer>
       
</body>
</html>