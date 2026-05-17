<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: ../connexion.php');
    exit();
}

// Si le panier n'existe pas encore, on le crée vide
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$action = $_GET['action'];

// AJOUTER un plat au panier
if ($action == 'ajouter') {
    $nom  = $_GET['nom'];
    $prix = $_GET['prix'];

    $_SESSION['panier'][] = [
        'nom'  => $nom,
        'prix' => $prix
    ];

    // On retourne à la page présentation
    header('Location: ../presentation.php');
    exit();
}

// SUPPRIMER un plat du panier
if ($action == 'supprimer') {
    $index = $_GET['index'];

    // On supprime l'élément à cet index
    array_splice($_SESSION['panier'], $index, 1);

    // On retourne au panier
    header('Location: ../panier.php');
    exit();
}
?>
