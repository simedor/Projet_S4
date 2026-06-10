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

$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$action = isset($_POST['action_admin']) ? trim($_POST['action_admin']) : '';
$fidelite = isset($_POST['fidelite']) ? trim($_POST['fidelite']) : 'Standard';
$retour = '../utilisateur.php?id=' . $userId;

if ($userId <= 0) {
    header('Location: ../administrateur.php');
    exit();
}

foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] !== $userId) {
        continue;
    }

    if ($action === 'bloquer') {
        if ($userId === (int) $admin['id']) {
            header('Location: ../administrateur.php');
            exit();
        }

        $utilisateur['statut_compte'] = 'bloque';
        ajouter_incident('admin', 'Compte bloque par un admin', $utilisateur['login'], $utilisateur['id']);
        ecrire_json('utilisateurs.json', $utilisateurs);
        header('Location: ' . $retour . '&admin=statut');
        exit();
    }

    if ($action === 'debloquer') {
        if ($userId === (int) $admin['id']) {
            header('Location: ../administrateur.php');
            exit();
        }

        $utilisateur['statut_compte'] = 'actif';
        ajouter_incident('admin', 'Compte debloque par un admin', $utilisateur['login'], $utilisateur['id']);
        ecrire_json('utilisateurs.json', $utilisateurs);
        header('Location: ' . $retour . '&admin=statut');
        exit();
    }

    if ($action === 'fidelite') {
        if (!in_array($fidelite, ['Standard', 'Premium', 'VIP'], true)) {
            $fidelite = 'Standard';
        }

        $utilisateur['fidelite'] = $fidelite;
        $utilisateur['remise'] = remise_fidelite($fidelite);
        ajouter_incident('admin', 'Fidelite client modifiee', $utilisateur['login'], $utilisateur['id']);
        ecrire_json('utilisateurs.json', $utilisateurs);
        header('Location: ' . $retour . '&admin=fidelite');
        exit();
    }
}
unset($utilisateur);

header('Location: ../administrateur.php');
exit();
