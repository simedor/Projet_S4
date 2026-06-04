<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$restaurateur = null;

foreach (lire_json('utilisateurs.json') as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $restaurateur = $utilisateur;
        break;
    }
}

if ($restaurateur === null || $restaurateur['role'] !== 'restaurateur') {
    header('Location: ../accueil.php');
    exit();
}

$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$plats = lire_json('plats.json');
$nomOriginal = isset($_POST['nom_original']) ? trim($_POST['nom_original']) : '';

function redirection_plat($action, $nomOriginal, $etat = '')
{
    $suffixe = $etat !== '' ? '?plat=' . $etat : '';

    if ($action === 'modifier') {
        header('Location: ../modifier_plat.php?edit=' . urlencode($nomOriginal) . ($etat !== '' ? '&plat=' . urlencode($etat) : ''));
        exit();
    }

    header('Location: ../commande.php' . $suffixe);
    exit();
}

if ($action === 'supprimer') {
    foreach ($plats as $index => $plat) {
        if ($plat['nom'] === $nomOriginal) {
            unset($plats[$index]);
            $plats = array_values($plats);
            ecrire_json('plats.json', $plats);
            ajouter_incident('restaurant', 'Plat supprime', $restaurateur['login'], $restaurateur['id']);
            header('Location: ../commande.php?plat=supprime');
            exit();
        }
    }

    header('Location: ../commande.php');
    exit();
}

$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$prix = isset($_POST['prix']) ? (float) $_POST['prix'] : 0;
$categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : 'pizza';
$regime = isset($_POST['regime']) ? trim($_POST['regime']) : '';
$gout = isset($_POST['gout']) ? trim($_POST['gout']) : '';
$platDuJour = isset($_POST['plat_du_jour']);
$bestSeller = isset($_POST['best_seller']);
$imageFichier = isset($_FILES['image_fichier']) ? $_FILES['image_fichier'] : null;
$imageFinale = '';
$imageEnvoyee = false;

if ($imageFichier !== null && isset($imageFichier['error']) && $imageFichier['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($imageFichier['error'] !== UPLOAD_ERR_OK) {
        redirection_plat($action, $nomOriginal, 'image_upload');
    }

    $nomFichier = isset($imageFichier['name']) ? $imageFichier['name'] : '';
    $extension = strtolower(pathinfo($nomFichier, PATHINFO_EXTENSION));
    $extensionsAutorisees = ['png', 'jpg', 'jpeg', 'webp'];

    if (!in_array($extension, $extensionsAutorisees, true)) {
        redirection_plat($action, $nomOriginal, 'image_invalide');
    }

    $nomNettoye = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nomFichier);
    $nomFinal = date('YmdHis') . '_' . $nomNettoye;
    $cheminDestination = __DIR__ . '/../Images/' . $nomFinal;

    if (!move_uploaded_file($imageFichier['tmp_name'], $cheminDestination)) {
        redirection_plat($action, $nomOriginal, 'image_upload');
    }

    $imageFinale = 'Images/' . $nomFinal;
    $imageEnvoyee = true;
}

if ($action === 'modifier' && $imageFinale === '') {
    foreach ($plats as $plat) {
        if ($plat['nom'] === $nomOriginal) {
            $imageFinale = isset($plat['image']) ? $plat['image'] : '';
            break;
        }
    }
}

if ($nom === '' || $description === '' || $prix <= 0 || $imageFinale === '' || $categorie === '' || $regime === '' || $gout === '') {
    redirection_plat($action, $nomOriginal, $action === 'ajouter' ? 'image_absente' : 'incomplet');
}

if ($action === 'ajouter') {
    foreach ($plats as $plat) {
        if ($plat['nom'] === $nom) {
            header('Location: ../commande.php');
            exit();
        }
    }

    if ($platDuJour) {
        foreach ($plats as &$plat) {
            $plat['plat_du_jour'] = false;
        }
        unset($plat);
    }

    $plats[] = [
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'image' => $imageFinale,
        'categorie' => $categorie,
        'type' => $type,
        'regime' => $regime,
        'gout' => $gout,
        'popularite' => 0,
        'plat_du_jour' => $platDuJour,
        'best_seller' => $bestSeller
    ];

    ecrire_json('plats.json', $plats);
    ajouter_incident('restaurant', 'Plat ajoute', $restaurateur['login'], $restaurateur['id']);
    header('Location: ../commande.php?plat=ajoute');
    exit();
}

if ($action === 'modifier') {
    if ($platDuJour) {
        foreach ($plats as &$platExistant) {
            $platExistant['plat_du_jour'] = false;
        }
        unset($platExistant);
    }

    $platModifie = false;

    foreach ($plats as &$plat) {
        if ($plat['nom'] === $nomOriginal) {
            $popularite = isset($plat['popularite']) ? (int) $plat['popularite'] : 0;
            $plat['nom'] = $nom;
            $plat['description'] = $description;
            $plat['prix'] = $prix;
            $plat['image'] = $imageFinale;
            $plat['categorie'] = $categorie;
            $plat['type'] = $type;
            $plat['regime'] = $regime;
            $plat['gout'] = $gout;
            $plat['popularite'] = $popularite;
            $plat['plat_du_jour'] = $platDuJour;
            $plat['best_seller'] = $bestSeller;
            $platModifie = true;
            break;
        }
    }
    unset($plat);

    if (!$platModifie) {
        header('Location: ../commande.php');
        exit();
    }

    ecrire_json('plats.json', $plats);
    ajouter_incident('restaurant', 'Plat modifie', $restaurateur['login'], $restaurateur['id']);
    header('Location: ../commande.php?plat=modifie');
    exit();
}

header('Location: ../commande.php');
exit();
