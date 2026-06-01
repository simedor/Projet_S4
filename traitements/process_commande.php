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

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier']) || count($_SESSION['panier']) === 0) {
    header('Location: ../panier.php?erreur=panier_vide');
    exit();
}

$modeRetrait = isset($_POST['mode_retrait']) ? trim($_POST['mode_retrait']) : '';
$typeLivraison = isset($_POST['type_livraison']) ? trim($_POST['type_livraison']) : '';
$creneau = isset($_POST['creneau']) ? trim($_POST['creneau']) : '';
$nomCarte = isset($_POST['nom_carte']) ? trim($_POST['nom_carte']) : '';
$numeroCarte = isset($_POST['numero_carte']) ? trim($_POST['numero_carte']) : '';
$expiration = isset($_POST['expiration']) ? trim($_POST['expiration']) : '';
$cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';

if ($modeRetrait === '' || $typeLivraison === '' || $nomCarte === '' || $numeroCarte === '' || $expiration === '' || $cvv === '') {
    header('Location: ../panier.php?erreur=champs_manquants');
    exit();
}

if ($typeLivraison === 'differee' && $creneau === '') {
    header('Location: ../panier.php?erreur=champs_manquants');
    exit();
}

$numeroCarte = preg_replace('/\D/', '', $numeroCarte);

if (strlen($numeroCarte) < 12 || strlen($cvv) < 3) {
    header('Location: ../panier.php?erreur=paiement_refuse');
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

$commandes = lire_json('commandes.json');
$nouvelId = 1;

foreach ($commandes as $commande) {
    if ((int) $commande['id'] >= $nouvelId) {
        $nouvelId = (int) $commande['id'] + 1;
    }
}

$commandes[] = [
    'id' => $nouvelId,
    'client_id' => $utilisateur['id'],
    'client_nom' => $utilisateur['prenom'] . ' ' . $utilisateur['nom'],
    'produit' => implode(', ', $resume),
    'lignes' => $lignes,
    'adresse' => $modeRetrait === 'a_emporter' ? 'Retrait au restaurant' : $utilisateur['adresse'],
    'telephone' => $utilisateur['telephone'],
    'mode_retrait' => $modeRetrait,
    'type_livraison' => $typeLivraison,
    'creneau' => $typeLivraison === 'differee' ? $creneau : 'Maintenant',
    'statut_paiement' => 'paye',
    'statut_commande' => $typeLivraison === 'differee' ? 'en_attente' : 'a_preparer',
    'livreur_id' => 0,
    'livreur_nom' => '',
    'date_commande' => date('Y-m-d H:i'),
    'total' => $total,
    'paiements' => [
        [
            'date' => date('Y-m-d H:i'),
            'montant' => $total,
            'type' => 'initial'
        ]
    ]
];

ecrire_json('commandes.json', $commandes);
$_SESSION['panier'] = [];

header('Location: ../profil.php?commande=ok');
exit();
