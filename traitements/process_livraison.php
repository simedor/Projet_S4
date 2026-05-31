<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$commandeId = isset($_POST['commande_id']) ? (int) $_POST['commande_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$utilisateur = null;
$listeUtilisateurs = lire_json('utilisateurs.json');

foreach ($listeUtilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

if ($utilisateur === null || $utilisateur['role'] !== 'livreur') {
    header('Location: ../accueil.php');
    exit();
}

$listeCommandes = lire_json('commandes.json');

foreach ($listeCommandes as &$commande) {
    if ((int) $commande['id'] === $commandeId && (int) $commande['livreur_id'] === (int) $utilisateur['id']) {
        if ($action === 'livree') {
            $commande['statut_commande'] = 'livree';
        }

        if ($action === 'abandonnee') {
            $commande['statut_commande'] = 'abandonnee';
        }
    }
}

ecrire_json('commandes.json', $listeCommandes);

foreach ($listeUtilisateurs as &$unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $utilisateur['id']) {
        $unUtilisateur['disponible'] = true;
    }
}

ecrire_json('utilisateurs.json', $listeUtilisateurs);

header('Location: ../livraison.php');
exit();
