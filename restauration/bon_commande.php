<?php
session_start();


?>

<?php

    if (!empty($_SESSION['error'])) {
      include("message_erreur.php");

        unset($_SESSION['error']);  // pour n’afficher qu'une seule fois
    }
?>
<?php
    include 'connexion.php'; // ton PDO $bdd

    // 1. Créer la commande
    $stmt = $bdd->prepare("INSERT INTO commandes () VALUES ()");
    $stmt->execute();
    $commandeId = $bdd->lastInsertId();
     $commandeEnregistree = true;

    // 2. Ajouter les plats choisis
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

         // je teste si au moins un met a ete choisi dans le bon de commande
            

        try{
            
            $quantites=$_POST['quantites'];
            
            if (!isset($_POST['quantites'])) {
    
                  $commandeEnregistree = false;
                }
           
           $quantites = array_filter($quantites, function($q){

            return intval($q) > 0;

            });

            if (empty($quantites)){

                $commandeEnregistree = false;
            }

            foreach ($quantites as $platId => $quantite) {

                $quantite = (int)$quantite;

                if ($quantite > 0) {
                    $sql = $bdd->prepare("
                        INSERT INTO plat_commande (commandeId, platId, quantite)
                        VALUES (?, ?, ?)
                    ");
                    $sql->execute([$commandeId, $platId, $quantite]);
                    
                }
            }
            // enregistement de la quantite dans une session.
            $_SESSION['quantites']=$quantites;

            if ($commandeEnregistree === true ) 
            {
                // On crée un message visible sur la page
                echo "
                <div id='message-succes' >
                     commnde enregistrée avec succès !
                </div>

                <style>
                    #message-succes 
                    {
                        position: fixed;           /* Le message reste visible en haut à droite */
                        top: 20px;
                        right: 20px;
                        background-color: #4CAF50; /* Vert pour succès */
                        color: white;
                        padding: 15px 25px;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        z-index: 1000;
                        opacity: 1;                /* Pleinement visible */
                        transition: opacity 0.5s ease-in-out; /* Pour une disparition douce */
                    }
                </style>
                <script>
                    setTimeout(function() {
                        var message = document.getElementById('message-succes');
                        message.style.opacity = '0';   // Commence à disparaître
                        setTimeout(function() {
                            message.remove();           // Supprime complètement du DOM après la transition
                        }, 500);                        // 500ms = durée de la transition
                    }, 3000);                            // 3000ms = 3 secondes avant disparition
                </script>";

                
            }
            else{
                echo "
                <div id='message-erreur' >
                     erreur d'enregistrement veullez choisir au moins un met dans la liste ci-dessous.
                </div>

                <style>
                    #message-erreur 
                    {
                        position: fixed;           /* Le message reste visible en haut à droite */
                        top: 20px;
                        right: 20px;
                        background-color: #fa0d0dff; /* rouge pour echec */
                        color: white;
                        padding: 15px 25px;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        z-index: 1000;
                        opacity: 1;                /* Pleinement visible */
                        transition: opacity 0.5s ease-in-out; /* Pour une disparition douce */
                    }
                </style>
                <script>
                    setTimeout(function() {
                        var message = document.getElementById('message-erreur');
                        message.style.opacity = '0';   // Commence à disparaître
                        setTimeout(function() {
                            message.remove();           // Supprime complètement du DOM après la transition
                        }, 500);                        // 500ms = durée de la transition
                    }, 3000);                            // 3000ms = 3 secondes avant disparition
                </script>";
            }
        }
        
        catch(Exception $e){
            return"erreurr commande non enregistre ". $e-> getMessage();
        }
    }
?>
 
    
<?php
    include ('header.php');
    include ('connexion.php'); // ton PDO $bdd

    // Récupérer tous les plats
    $sql = $bdd->query("SELECT * FROM plats");
    $listePlats = $sql->fetchAll(PDO::FETCH_ASSOC);

    ?>
    

    <h2 style="text-align:center; margin-bottom:20px;"> Nouveau bon de commande </h2>

    <form  method="POST">
    <h3>Liste des plats :</h3>
    <table style='border="1" cellpadding="10" cellspacing="0"'>
        <thead>
            <tr>
                <th>Plat</th>
                <th>Prix (FCFA)</th>
                <th>Quantite</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            foreach ($listePlats as $plat) { ?>
                <tr style='margin-left:2px'>
                    <td ><?php echo htmlspecialchars($plat['intitule']); ?></td>
                    <td><?php echo number_format($plat['prix'], 0, '', ' '); ?></td>
                    <td>
                        <input type="number" 
                            name="quantites[<?php echo $plat['id_plat']; ?>]" 
                            min="0" value="0">
                    </td>
                </tr>
                <?php } 
            ?>
        </tbody>
    </table>
    <br>
    <!-- Boutons -->
        <div class="buttons">
            <button type="submit" class="btn-enregistrer">Valider la commande</button>
            
            <button type="button" class="btn-etablir" onclick="window.location.href='facture.php'">  Etablir la facture </button>
            
        </div>

    </form>

        
    

</body>
</html>
