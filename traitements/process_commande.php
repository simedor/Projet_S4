<?php
session_start();

// Si personne n'est connecté, on redirige
if (!isset($_SESSION['id'])) {
    header('Location: ../connexion.php');
    exit();
}

// Si le panier est vide, on redirige
if (!isset($_SESSION['panier']) || count($_SESSION['panier']) == 0) {
    header('Location: ../panier.php');
    exit();
}

// --- CALCUL DU TOTAL ---
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'];
}

// --- RECUPERATION DES INFOS DE LIVRAISON ---
$type_livraison = $_POST['type_livraison'];
$heure_livraison = isset($_POST['heure_livraison']) ? $_POST['heure_livraison'] : null;

// --- APPEL API CYBANK ---
// ⚠️ Remplacer l'URL et les paramètres par ceux fournis par votre chargé de TD
$url_cybank = "https://URL_DE_CYBANK_A_REMPLACER/paiement";

// On prépare les données à envoyer à CYBank
$donnees_paiement = [
    "montant"     => $total,
    "client_id"   => $_SESSION['id'],
    "description" => "Commande CY Pizza"
    // Ajouter ici les autres paramètres demandés par CYBank
];

// On encode les données en JSON pour les envoyer
$options = [
    "http" => [
        "method"  => "POST",
        "header"  => "Content-Type: application/json",
        "content" => json_encode($donnees_paiement)
    ]
];

$contexte  = stream_context_create($options);
$reponse   = file_get_contents($url_cybank, false, $contexte);
$resultat  = json_decode($reponse, true);

// --- VERIFICATION DU PAIEMENT ---
// ⚠️ Adapter la condition selon ce que renvoie vraiment CYBank
if ($resultat == null || $resultat['statut'] != 'accepte') {
    // Paiement refusé → on redirige avec une erreur
    header('Location: ../panier.php?erreur=paiement_refuse');
    exit();
}

// --- ENREGISTREMENT DE LA COMMANDE ---
// On lit le fichier commandes existant
$data      = file_get_contents('../data/commandes.json');
$commandes = json_decode($data, true);

// On calcule le nouvel id
$nouvel_id = 1;
foreach ($commandes as $commande) {
    if ($commande['id'] >= $nouvel_id) {
        $nouvel_id = $commande['id'] + 1;
    }
}

// On construit la liste des produits commandés
$produits = [];
foreach ($_SESSION['panier'] as $item) {
    $produits[] = $item['nom'];
}

// On lit les infos du client depuis utilisateurs.json
$data_users   = file_get_contents('../data/utilisateurs.json');
$utilisateurs = json_decode($data_users, true);

$utilisateur_connecte = null;
foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['id'] == $_SESSION['id']) {
        $utilisateur_connecte = $utilisateur;
        break;
    }
}

// On crée la nouvelle commande
$nouvelle_commande = [
    "id"             => $nouvel_id,
    "client"         => $_SESSION['prenom'],
    "produit"        => implode(', ', $produits), // Ex: "Reine, Vegan"
    "adresse"        => $utilisateur_connecte['adresse'],
    "telephone"      => $utilisateur_connecte['telephone'],
    "statut"         => "preparation",
    "livreur"        => null,
    "date"           => date("Y-m-d"),
    "type_livraison" => $type_livraison,
    "heure_livraison"=> $heure_livraison,
    "total"          => $total
];

// On l'ajoute et on réécrit le fichier
$commandes[] = $nouvelle_commande;
file_put_contents('../data/commandes.json', json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// On ajoute l'id de la commande dans le profil du client
foreach ($utilisateurs as &$utilisateur) {
    if ($utilisateur['id'] == $_SESSION['id']) {
        $utilisateur['commandes'][] = $nouvel_id;
        break;
    }
}
file_put_contents('../data/utilisateurs.json', json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// On vide le panier
$_SESSION['panier'] = [];

// On redirige vers le profil avec un message de succès
header('Location: ../profil.php?commande=ok');
exit();
?>
