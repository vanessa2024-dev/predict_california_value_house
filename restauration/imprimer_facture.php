<?php
session_start();
ob_clean();

require('fpdf/fpdf.php');
include('connexion.php');

// je recupere l'id de la precedente facture
$factureId = $_SESSION['facture'];
// je fais un test afin de confirmer l'exitence da la facture
if (!$factureId) {
    die("Erreur : aucune facture trouvée.");
}

//  je recupere toutes les informations de la factures
$req = $bdd->prepare("
    SELECT f.id_facture, f.montant_total, f.date_facture, f.commande_id
    FROM factures f
    WHERE f.id_facture = ?
");
$req->execute([$factureId]);
$facture = $req->fetch(PDO::FETCH_ASSOC);

// je  recupere les plats lies a la commande
$req2 = $bdd->prepare("
    SELECT p.intitule, p.prix, pc.quantite 
    FROM plat_commande pc
    JOIN plats p ON p.id_plat = pc.platId
    WHERE pc.commandeId = ?
");
$req2->execute([$facture['commande_id']]);
$plats = $req2->fetchAll(PDO::FETCH_ASSOC);


// je genere la facture pdf
$pdf = new FPDF('P', 'mm', 'A5');
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

$pdf->Cell(0,12, utf8_decode("LES Delices du Saphirs"), 0, 1, 'C');
$pdf->Ln(3);

$pdf->Cell(0,10, utf8_decode("FACTURE N° ".$factureId), 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8, utf8_decode("Date : ".$facture['date_facture']), 0, 1);
$pdf->Ln(5);

// En-têtes du tableau
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,10,"Plat",1);
$pdf->Cell(25,10,"Quantite",1);
$pdf->Cell(30,10,"Prix",1);
$pdf->Cell(30,10,"Total",1);
$pdf->Ln();

// Contenu du tableau
$pdf->SetFont('Arial','',11);

foreach ($plats as $plat) {

    $totalPlat = $plat['prix'] * $plat['quantite'];

    $pdf->Cell(60,10, utf8_decode($plat['intitule']),1);
    $pdf->Cell(25,10, $plat['quantite'],1);
    $pdf->Cell(30,10, number_format($plat['prix'],0,',',' ') . " FCFA",1);
    $pdf->Cell(30,10, number_format($totalPlat,0,',',' ') . " FCFA",1);
    $pdf->Ln();
}

// Montant total
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10, utf8_decode("Montant total : " . number_format($facture['montant_total'],0,',',' ') . " FCFA"),0,1);

$pdf->Output();
unset($_SESSION['facture']);
exit;