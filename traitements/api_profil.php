<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['ok' => false, 'message' => 'Non connecte']);
    exit();
}

$utilisateurs = lire_json('utilisateurs.json');
$utilisateurMisAJour = null;
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
$telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
$infosComplementaires = isset($_POST['infos_complementaires']) ? trim($_POST['infos_complementaires']) : '';

if ($nom === '' || $prenom === '' || $email === '' || $adresse === '' || $telephone === '') {
    echo json_encode(['ok' => false, 'message' => 'Champs manquants']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'message' => 'Email invalide']);
    exit();
}

if (!preg_match('/^\d{10}$/', $telephone)) {
    echo json_encode(['ok' => false, 'message' => 'Telephone invalide']);
    exit();
}

if (strlen($adresse) < 5) {
    echo json_encode(['ok' => false, 'message' => 'Adresse trop courte']);
    exit();
}

foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['email'] === $email && (int) $utilisateur['id'] !== (int) $_SESSION['utilisateur_id']) {
        echo json_encode(['ok' => false, 'message' => 'Email deja utilise']);
        exit();
    }
}

foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur['nom'] = $nom;
        $utilisateur['prenom'] = $prenom;
        $utilisateur['email'] = $email;
        $utilisateur['adresse'] = $adresse;
        $utilisateur['telephone'] = $telephone;
        $utilisateur['infos_complementaires'] = $infosComplementaires;
        $utilisateurMisAJour = $utilisateur;
        break;
    }
}

ecrire_json('utilisateurs.json', $utilisateurs);
ajouter_incident('profil', 'Profil modifie', $utilisateurMisAJour['login'], $utilisateurMisAJour['id']);
echo json_encode(['ok' => true, 'utilisateur' => $utilisateurMisAJour]);
