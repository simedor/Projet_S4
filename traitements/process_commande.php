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

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$panier = $_SESSION['panier'];

if (empty($panier)) {
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

$numeroCarteNettoye = preg_replace('/\D/', '', $numeroCarte);

if (strlen($numeroCarteNettoye) < 12 || strlen($cvv) < 3) {
    header('Location: ../panier.php?erreur=paiement_refuse');
    exit();
}

$total = 0;
$nouvellesLignes = [];
$produits = [];

foreach ($panier as $article) {
    $sousTotal = $article['prix'] * $article['quantite'];
    $total += $sousTotal;

    $nouvellesLignes[] = [
        'nom' => $article['nom'],
        'prix' => (float) $article['prix'],
        'quantite' => (int) $article['quantite'],
        'sous_total' => $sousTotal
    ];

    $produits[] = $article['nom'] . ' x' . $article['quantite'];
}

$listeCommandes = lire_json('commandes.json');
$nouvelId = 1;

foreach ($listeCommandes as $commande) {
    if ((int) $commande['id'] >= $nouvelId) {
        $nouvelId = (int) $commande['id'] + 1;
    }
}

$listeCommandes[] = [
    'id' => $nouvelId,
    'client_id' => $utilisateur['id'],
    'client_nom' => $utilisateur['prenom'] . ' ' . $utilisateur['nom'],
    'produit' => implode(', ', $produits),
    'lignes' => $nouvellesLignes,
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
    'total' => $total
];

ecrire_json('commandes.json', $listeCommandes);
$_SESSION['panier'] = [];

header('Location: ../profil.php?commande=ok');
exit();
