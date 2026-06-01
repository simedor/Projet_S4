<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$client = null;

foreach (lire_json('utilisateurs.json') as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $client = $utilisateur;
        break;
    }
}

if ($client === null || $client['role'] !== 'client') {
    header('Location: ../accueil.php');
    exit();
}

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$panier = $_SESSION['panier'];

if ($action === 'ajouter') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $quantite = isset($_POST['quantite']) ? (int) $_POST['quantite'] : 1;
    $platTrouve = null;

    foreach (lire_json('plats.json') as $plat) {
        if ($plat['nom'] === $nom) {
            $platTrouve = $plat;
            break;
        }
    }

    if ($platTrouve !== null) {
        if (isset($panier[$nom])) {
            $panier[$nom]['quantite'] += max(1, $quantite);
        } else {
            $panier[$nom] = [
                'nom' => $platTrouve['nom'],
                'prix' => $platTrouve['prix'],
                'image' => $platTrouve['image'],
                'quantite' => max(1, $quantite)
            ];
        }
    }

    $_SESSION['panier'] = $panier;
    header('Location: ../presentation.php');
    exit();
}

if ($action === 'modifier') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $quantite = isset($_POST['quantite']) ? (int) $_POST['quantite'] : 0;

    if (isset($panier[$nom])) {
        if ($quantite <= 0) {
            unset($panier[$nom]);
        } else {
            $panier[$nom]['quantite'] = $quantite;
        }
    }

    $_SESSION['panier'] = $panier;
    header('Location: ../panier.php');
    exit();
}

if ($action === 'supprimer') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';

    if (isset($panier[$nom])) {
        unset($panier[$nom]);
    }

    $_SESSION['panier'] = $panier;
    header('Location: ../panier.php');
    exit();
}

if ($action === 'vider') {
    $_SESSION['panier'] = [];
    header('Location: ../panier.php');
    exit();
}

header('Location: ../panier.php');
exit();
