<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;
$commandeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$commandeTrouvee = null;
$plats = lire_json('plats.json');
$imagesParNom = [];

foreach (lire_json('utilisateurs.json') as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

if ($utilisateur === null || $utilisateur['role'] !== 'client') {
    header('Location: ../accueil.php');
    exit();
}

foreach ($plats as $plat) {
    $imagesParNom[$plat['nom']] = $plat['image'];
}

foreach (lire_json('commandes.json') as $commande) {
    if ((int) $commande['id'] === $commandeId && (int) $commande['client_id'] === (int) $utilisateur['id']) {
        $commandeTrouvee = $commande;
        break;
    }
}

if ($commandeTrouvee === null || !isset($commandeTrouvee['lignes']) || !is_array($commandeTrouvee['lignes'])) {
    header('Location: ../profil.php');
    exit();
}

$_SESSION['panier'] = [];

foreach ($commandeTrouvee['lignes'] as $ligne) {
    if (!isset($ligne['nom'])) {
        continue;
    }

    $nom = $ligne['nom'];
    $_SESSION['panier'][$nom] = [
        'nom' => $nom,
        'prix' => isset($ligne['prix']) ? (float) $ligne['prix'] : 0,
        'image' => isset($imagesParNom[$nom]) ? $imagesParNom[$nom] : 'Images/Reine.png',
        'quantite' => isset($ligne['quantite']) ? (int) $ligne['quantite'] : 1
    ];
}

ajouter_incident('commande', 'Ancienne commande remise dans le panier', $utilisateur['login'], $utilisateur['id']);
header('Location: ../panier.php?recommande=ok');
exit();
