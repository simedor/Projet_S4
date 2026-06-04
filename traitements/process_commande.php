<?php
require_once __DIR__ . '/../Includes/cybank.php';

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

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier']) || count($_SESSION['panier']) === 0) {
    header('Location: ../panier.php?erreur=panier_vide');
    exit();
}

$modeRetrait = isset($_POST['mode_retrait']) ? trim($_POST['mode_retrait']) : '';
$typeLivraison = isset($_POST['type_livraison']) ? trim($_POST['type_livraison']) : '';
$creneau = isset($_POST['creneau']) ? trim($_POST['creneau']) : '';
$interphone = isset($_POST['interphone']) ? trim($_POST['interphone']) : '';
$etage = isset($_POST['etage']) ? trim($_POST['etage']) : '';
$commentaireLivraison = isset($_POST['commentaire_livraison']) ? trim($_POST['commentaire_livraison']) : '';

if ($modeRetrait === '' || $typeLivraison === '') {
    header('Location: ../panier.php?erreur=champs_manquants');
    exit();
}

if (!in_array($modeRetrait, ['livraison', 'a_emporter'], true) || !in_array($typeLivraison, ['immediate', 'differee'], true)) {
    header('Location: ../panier.php?erreur=champs_manquants');
    exit();
}

if ($typeLivraison === 'differee' && $creneau === '') {
    header('Location: ../panier.php?erreur=champs_manquants');
    exit();
}

$panier = $_SESSION['panier'];
$total = 0;
$lignes = [];
$resume = [];

foreach ($panier as $article) {
    $sousTotal = $article['prix'] * $article['quantite'];
    $total += $sousTotal;
    $lignes[] = [
        'nom' => $article['nom'],
        'prix' => $article['prix'],
        'quantite' => $article['quantite'],
        'sous_total' => $sousTotal
    ];
    $resume[] = $article['nom'] . ' x' . $article['quantite'];
}

$paiement = cybank_creer_paiement('nouvelle_commande', $total, [
    'client_id' => $utilisateur['id'],
    'client_nom' => $utilisateur['prenom'] . ' ' . $utilisateur['nom'],
    'produit' => implode(', ', $resume),
    'lignes' => $lignes,
    'adresse' => $modeRetrait === 'a_emporter' ? 'Retrait au restaurant' : $utilisateur['adresse'],
    'telephone' => $utilisateur['telephone'],
    'interphone' => $modeRetrait === 'livraison' ? $interphone : '',
    'etage' => $modeRetrait === 'livraison' ? $etage : '',
    'commentaire_livraison' => $modeRetrait === 'livraison' ? $commentaireLivraison : '',
    'mode_retrait' => $modeRetrait,
    'type_livraison' => $typeLivraison,
    'creneau' => $typeLivraison === 'differee' ? $creneau : 'Maintenant'
]);

if ($paiement === null) {
    header('Location: ../panier.php?erreur=cybank_indisponible');
    exit();
}

ajouter_incident('paiement', 'Preparation du paiement CYBank', $utilisateur['login'], $utilisateur['id']);
header('Location: ../paiement_cybank.php?token=' . urlencode($paiement['token']));
exit();
