<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$admin = null;
$utilisateurs = lire_json('utilisateurs.json');

foreach ($utilisateurs as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $admin = $utilisateur;
        break;
    }
}

if ($admin === null || $admin['role'] !== 'admin') {
    header('Location: ../accueil.php');
    exit();
}

$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';
$codesPromo = lire_json('codes_promo.json');

if (!preg_match('/^[A-Z0-9_-]{3,20}$/', $code)) {
    header('Location: ../administrateur.php?promo=invalide');
    exit();
}

if ($action === 'supprimer') {
    $nouvelleListe = [];

    foreach ($codesPromo as $promo) {
        if (!isset($promo['code']) || strtoupper(trim($promo['code'])) !== $code) {
            $nouvelleListe[] = $promo;
        }
    }

    ecrire_json('codes_promo.json', array_values($nouvelleListe));
    ajouter_incident('admin', 'Code promo supprime', $admin['login'], $admin['id']);
    header('Location: ../administrateur.php?promo=supprime');
    exit();
}

$reduction = isset($_POST['reduction']) ? (int) $_POST['reduction'] : 0;

if ($action !== 'ajouter' || $reduction < 1 || $reduction > 90) {
    header('Location: ../administrateur.php?promo=invalide');
    exit();
}

foreach ($codesPromo as $promo) {
    if (isset($promo['code']) && strtoupper(trim($promo['code'])) === $code) {
        header('Location: ../administrateur.php?promo=existe');
        exit();
    }
}

$codesPromo[] = [
    'code' => $code,
    'reduction' => $reduction,
    'actif' => true,
    'date_creation' => date('Y-m-d H:i')
];

ecrire_json('codes_promo.json', $codesPromo);
ajouter_incident('admin', 'Code promo ajoute', $admin['login'], $admin['id']);
header('Location: ../administrateur.php?promo=ajoute');
exit();
