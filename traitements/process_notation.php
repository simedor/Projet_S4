<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: ../connexion.php');
    exit();
}

$commande_id    = $_POST['commande_id'];
$note_livraison = $_POST['note_livraison'];
$note_produit   = $_POST['note_produit'];

// On lit le fichier notations
$data      = file_get_contents('../data/notations.json');
$notations = json_decode($data, true);

// Si le fichier est vide, on part d'un tableau vide
if ($notations == null) {
    $notations = [];
}

// On ajoute la nouvelle notation
$nouvelle_notation = [
    "commande_id"    => $commande_id,
    "client_id"      => $_SESSION['id'],
    "note_livraison" => $note_livraison,
    "note_produit"   => $note_produit,
    "date"           => date("Y-m-d")
];

$notations[] = $nouvelle_notation;

// On réécrit le fichier
file_put_contents('../data/notations.json', json_encode($notations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// On redirige vers le profil
header('Location: ../profil.php');
exit();
?>
