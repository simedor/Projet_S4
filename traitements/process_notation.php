<?php
require_once __DIR__ . '/../Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php?erreur=connexion_requise');
    exit();
}

$commandeId = isset($_POST['commande_id']) ? (int) $_POST['commande_id'] : 0;
$noteLivraison = isset($_POST['note_livraison']) ? (int) $_POST['note_livraison'] : 0;
$noteProduit = isset($_POST['note_produit']) ? (int) $_POST['note_produit'] : 0;
$commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
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

$commande = null;

foreach (lire_json('commandes.json') as $uneCommande) {
    if ((int) $uneCommande['id'] === $commandeId) {
        $commande = $uneCommande;
        break;
    }
}

if ($commande === null || (int) $commande['client_id'] !== (int) $utilisateur['id']) {
    header('Location: ../profil.php');
    exit();
}

if ($commande['statut_commande'] !== 'livree') {
    header('Location: ../profil.php');
    exit();
}

$listeNotations = lire_json('notations.json');

foreach ($listeNotations as $notation) {
    if ((int) $notation['commande_id'] === $commandeId && (int) $notation['client_id'] === (int) $utilisateur['id']) {
        header('Location: ../profil.php');
        exit();
    }
}

$listeNotations[] = [
    'commande_id' => $commandeId,
    'client_id' => $utilisateur['id'],
    'note_livraison' => $noteLivraison,
    'note_produit' => $noteProduit,
    'commentaire' => $commentaire,
    'date' => date('Y-m-d')
];

ecrire_json('notations.json', $listeNotations);

header('Location: ../profil.php?notation=ok');
exit();
