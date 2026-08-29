<?php
/**
 * index.php — Existe uniquement parce que les serveurs web cherchent
 * "index.php" par défaut quand on ouvre le dossier du site
 * (ex: http://localhost/homies-cars/). La vraie page d'accueil est
 * homies_cars.php.
 */
header("Location: homies_cars.php");
exit();
