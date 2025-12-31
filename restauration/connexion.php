<?php
$serveur = "localhost";
$db_nom = "db_client"; // BDD de l'étape 1
$utilisateur = "root";      // Défaut sur WAMP/XAMPP
$mot_de_passe = "";        // Défaut sur WAMP/XAMPP
try {
    $bdd = new PDO('mysql:host=localhost;dbname=db_client;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
