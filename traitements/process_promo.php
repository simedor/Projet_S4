<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;

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

$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($action === 'supprimer') {
    unset($_SESSION['code_promo']);
    header('Location: ../panier.php?promo=supprime');
    exit();
}

$code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';
$promo = trouver_code_promo($code);

if ($promo === null) {
    header('Location: ../panier.php?promo=invalide');
    exit();
}

$_SESSION['code_promo'] = $promo['code'];
header('Location: ../panier.php?promo=ok');
exit();
