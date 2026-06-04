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
$action = isset($_POST['action']) ? trim($_POST['action']) : 'livree';
$commandes = lire_json('commandes.json');
$statut = '';
$message = 'Commande introuvable';
$ok = false;

foreach ($commandes as &$commande) {
    if ((int) $commande['id'] === $commandeId && (int) $commande['livreur_id'] === (int) $livreur['id']) {
        if ($commande['statut_commande'] !== 'en_livraison') {
            echo json_encode(['ok' => false, 'message' => 'Cette commande ne peut pas etre validee']);
            exit();
        }

        if ($action === 'abandonnee') {
            $commande['statut_commande'] = 'abandonnee';
            $statut = 'Abandonnee';
            $message = 'Livraison marquee abandonnee';
            ajouter_incident('livraison', 'Commande abandonnee', $livreur['login'], $commande['client_id']);
        } else {
            $commande['statut_commande'] = 'livree';
            $statut = 'Livree';
            $message = 'Livraison terminee';
            ajouter_incident('livraison', 'Commande livree', $livreur['login'], $commande['client_id']);
        }

        $ok = true;
        break;
    }
}

if (!$ok) {
    echo json_encode(['ok' => false, 'message' => $message]);
    exit();
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
