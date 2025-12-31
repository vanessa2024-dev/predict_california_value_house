<?php
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
    include("header.php");
    include 'connexion.php';
    require("fpdf/fpdf.php");
    $factureEnregistree=true;
    $quantitesCommandes = array_filter($_SESSION['quantites'], function($q){ return intval($q) > 0;});
  


    // Stocker les quantités dans la session
   
    // afficher l facture avant avant de l'enregistrer
    try{

        if(empty($quantitesCommandes)){

            //affiche le message d'erreur. 
            //j'envoie le message d'erreur dans la session 
           // car apres la redirection auncune instruction ne s'executera.

            $_SESSION["error"]="aucun plat n'a ete choisi.";
            header("location:bon_commande.php");

            exit;
        }
    
    
        // Récupérer les plats depuis la table plats
        $idsPlats = array_keys($quantitesCommandes);
        $in = str_repeat('?,', count($idsPlats)-1) . '?';
        $sql = $bdd->prepare("SELECT * FROM plats WHERE id_plat IN ($in)");
        $sql->execute($idsPlats);
        $listePlats = $sql->fetchAll(PDO::FETCH_ASSOC);


        // Calculer le total
        $totalCommande = 0;
        foreach($listePlats as $plat){
            $qte = $quantitesCommandes[$plat['id_plat']];
            $totalCommande += $plat['prix'] * $qte;
            
        }
    }catch(Exception $e){
        return " ERREUR: la facture ne peut pas s'afficher".$e->getMessage();
    }

    //enregistrer la facture dans la base de donnee

    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        try{

            // verifier si la session a ete enregistre
       
            if (empty($quantitesCommandes)) { 

                die("erreur");
                
                }
         
            // debut de la transaction ceci bloque toutes les operations afin quelles s'effectuent toutes a la fin.

            $bdd->beginTransaction();

            // créer commande
            // cette commande laisse chaque colone de la base de donnees 
            // prendre ses valeurs par defauts et l'id s'incremente

            $bdd->exec("INSERT INTO commandes () VALUES ()");
            //recupere le dernier id incrementer
            $commandeId = $bdd->lastInsertId();

            $stmt = $bdd->prepare("SELECT COUNT(*) FROM plats WHERE id_plat = ?");
            foreach ($idsPlats as $idPlat) {
                $stmt->execute([$idPlat]); // exécute pour un seul ID à la fois
                if ($stmt->fetchColumn() == 0) {
                    die("Erreur : le plat $idPlat n'existe pas");
            }
            }


            // Vérifier la commande
            $stmt = $bdd->prepare("SELECT COUNT(*) FROM commandes WHERE id_commande = ?");
            $stmt->execute([$commandeId]);
            if ($stmt->fetchColumn() == 0) {
                die("Erreur : la commande $commandeId n'existe pas");
            }

            $total = 0;
            $stmtPlat = $bdd->prepare("SELECT prix FROM plats WHERE id_plat = ?");
            $stmtInsert = $bdd->prepare("INSERT INTO plat_commande (commandeId, platId, quantite) VALUES (?, ?, ?)");

            foreach ($quantitesCommandes as $idPlat => $qte) {
                $qte = (int)$qte;
                if ($qte <= 0) continue;
                $stmtInsert->execute([$commandeId, $idPlat, $qte]);
                $stmtPlat->execute([$idPlat]);
                $prix = $stmtPlat->fetchColumn();
                $total += $prix * $qte;
                
            }

            // créer facture
            $ins = $bdd->prepare("INSERT INTO factures (commande_id, montant_total) VALUES (?, ?)");
            $ins->execute([$commandeId, $total]);
            $LastFactureId = $bdd-> lastInsertId();

            $_SESSION ["facture"]=$LastFactureId;
            $_SESSION["quantites"]=$quantitesCommandes;
            //enregistrement de la factur.
            $bdd->commit();
            
          
            if ($factureEnregistree) {
                // On crée un message visible sur la page
                echo '
                <div id="message-succes" >
                    ✅ Facture enregistrée avec succès..... !
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
                        var message = document.getElementById("message-succes");
                        message.style.opacity = "0";   // Commence à disparaître
                        setTimeout(function() {
                            message.remove();           // Supprime complètement du DOM après la transition
                        }, 500);                        // 500ms = durée de la transition
                    }, 3000);                            // 3000ms = 3 secondes avant disparition
                    
                </script>';
                unset($_SESSION['quantites']);
                

                
            }


            else{
                // On crée un message visible sur la page
                echo '
                <div id="message-erreur" >
                     la facture a deja ete enregistre..
                </div>

                <style>
                    #message-erreur 
                    {
                        position: fixed;           /* Le message reste visible en haut à droite */
                        top: 20px;
                        right: 20px;
                        background-color: #f90b0bff; /* Vert pour succès */
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
                        var message = document.getElementById("message-erreur");
                        message.style.opacity = "0";   // Commence à disparaître
                        setTimeout(function() {
                            message.remove();           // Supprime complètement du DOM après la transition
                        }, 500);                        // 500ms = durée de la transition
                    }, 3000);                            // 3000ms = 3 secondes avant disparition
                    
                </script>

                ';
            }
             
            
        }   
        catch (Exception $e) {
                $bdd->rollBack();
                die("Erreur: la facture n'a pas ete enregistre ".$e->getMessage());
            }
    }

?>



<form  method="POST" id="facture-form" onsubmit="return mfValidation();">
 
    <!-- Détails de la facture -->
    <fieldset>
        <legend><b>Détails de la Facture</b></legend> 
        <table style='border="1" cellpadding="10"'>
       
            <?php foreach($listePlats as $plat): 

                $qte = $quantitesCommandes[$plat['id_plat']];

                $totalPlat = $plat['prix'] * $qte;
            ?>
            <tr>
                <td><?= htmlspecialchars($plat['intitule']) ?></td>
                <td><?= number_format($plat['prix'],0,',',' ') ?> FCFA</td>
                <td><?= $qte ?></td>
                <td><?= number_format($totalPlat,0,',',' ') ?> FCFA</td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total : <?= number_format($totalCommande,0,',',' ') ?> FCFA</h3>


        <button type="submit">Confirmer et enregistrer la facture</button>

    </fieldset>

    <!-- Mode de paiement et totaux -->
    
    <!-- Boutons -->
    <div>
        
        <a href="imprimer_facture.php" target="_blank" >  <button type="button" >Imprimer</button> </a>
        <button type="button"> <a href="bon_commande.php"> bon de commande </button> </a>
        
    </div>

</form>


<!-- Lien vers le JS -->
<script src="code1.js"></script>

</body>
</html>