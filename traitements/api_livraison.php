<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['ok' => false, 'message' => 'Non connecte']);
    exit();
}

$utilisateurs = lire_json('utilisateurs.json');
$livreur = null;

foreach ($utilisateurs as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $livreur = $utilisateur;
        break;
    }
}

if ($livreur === null || $livreur['role'] !== 'livreur') {
    echo json_encode(['ok' => false, 'message' => 'Acces refuse']);
    exit();
}

$commandeId = isset($_POST['commande_id']) ? (int) $_POST['commande_id'] : 0;
$commandes = lire_json('commandes.json');
$statut = '';
$message = 'Commande introuvable';

foreach ($commandes as &$commande) {
    if ((int) $commande['id'] === $commandeId && (int) $commande['livreur_id'] === (int) $livreur['id']) {
        $commande['statut_commande'] = 'livree';
        $statut = 'Livree';
        $message = 'Livraison terminee';
        break;
    }
}

foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] === (int) $livreur['id']) {
        $utilisateur['disponible'] = true;
        break;
    }
}

ecrire_json('commandes.json', $commandes);
ecrire_json('utilisateurs.json', $utilisateurs);

echo json_encode(['ok' => true, 'message' => $message, 'statut' => $statut]);
