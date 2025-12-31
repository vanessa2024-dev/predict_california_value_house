<?php
session_start();
include "connexion.php";
$nom = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    try{
        if(empty('username') || empty('email')|| empty('email')){
            die('vous ne pouvez vous connecter');
            //exit;
        }

        $sqlConnexion = $bdd->prepare('SELECT id , user_nom, user_email,mot_pass FROM users WHERE user_email=? ');
        $sqlConnexion->execute([ $email]);
        $user = $sqlConnexion->fetch(PDO::FETCH_ASSOC);
        
        // verifie si au moins un utilisateur a ete trouve.
        if(!$user){
            echo " veuillez verifier vos emails ";
        }

        if ($password !== $user['mot_pass']) {
            die ("mauvais mot de pass");
        }

        // Connexion réussie → Création de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['user_nom'];
        $_SESSION['user_email'] = $user['user_email'];
       

        header("Location: bon_commande.php");
        
        exit();

    }catch(Exception $e){
        return "ERREUR: probleme de connexion".$e->getMessage();
    }
 }




















?>