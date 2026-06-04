<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['ok' => false, 'message' => 'Non connecte']);
    exit();
}

$restaurateur = null;
$utilisateurs = lire_json('utilisateurs.json');

foreach ($utilisateurs as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $restaurateur = $utilisateur;
        break;
    }
}

if ($restaurateur === null || $restaurateur['role'] !== 'restaurateur') {
    echo json_encode(['ok' => false, 'message' => 'Acces refuse']);
    exit();
}

$commandeId = isset($_POST['commande_id']) ? (int) $_POST['commande_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$livreurId = isset($_POST['livreur_id']) ? (int) $_POST['livreur_id'] : 0;
$commandes = lire_json('commandes.json');
$message = 'Commande introuvable';
$prochaineAction = '';
$statut = '';
$trouve = false;
$ok = false;

foreach ($commandes as &$commande) {
    if ((int) $commande['id'] === $commandeId) {
        $trouve = true;

        if ($action === 'preparer' && $commande['statut_commande'] === 'a_preparer') {
            $commande['statut_commande'] = 'en_preparation';
            $message = 'Commande passee en preparation';
            $prochaineAction = 'prete';
            $ok = true;
            ajouter_incident('commande', 'Commande passee en preparation', '', $commande['client_id']);
        } elseif ($action === 'prete' && $commande['statut_commande'] === 'en_preparation') {
            $commande['statut_commande'] = 'prete';
            $message = 'Commande marquee prete';
            $prochaineAction = $commande['mode_retrait'] === 'livraison' ? 'assigner' : '';
            $ok = true;
            ajouter_incident('commande', 'Commande marquee prete', '', $commande['client_id']);
        } elseif ($action === 'assigner' && $commande['statut_commande'] === 'prete') {
            if ($commande['mode_retrait'] !== 'livraison') {
                echo json_encode(['ok' => false, 'message' => 'Cette commande ne doit pas etre assignee a un livreur']);
                exit();
            }

            if ($livreurId === 0) {
                echo json_encode(['ok' => false, 'message' => 'Choisissez un livreur']);
                exit();
            }

            $livreurTrouve = false;

            foreach ($utilisateurs as &$livreur) {
                if ((int) $livreur['id'] === $livreurId && $livreur['role'] === 'livreur') {
                    $livreurTrouve = true;

                    if (empty($livreur['disponible'])) {
                        echo json_encode(['ok' => false, 'message' => 'Ce livreur est deja occupe']);
                        exit();
                    }

                    $commande['livreur_id'] = $livreur['id'];
                    $commande['livreur_nom'] = $livreur['prenom'] . ' ' . $livreur['nom'];
                    $commande['statut_commande'] = 'en_livraison';
                    $livreur['disponible'] = false;
                    $message = 'Commande assignee';
                    $prochaineAction = '';
                    $ok = true;
                    ajouter_incident('commande', 'Commande assignee a un livreur', $livreur['login'], $commande['client_id']);
                    break;
                }
            }

            if (!$livreurTrouve) {
                echo json_encode(['ok' => false, 'message' => 'Livreur introuvable']);
                exit();
            }
        } else {
            $message = 'Action impossible pour ce statut';
        }

        $statut = $commande['statut_commande'];
        break;
    }
}

if (!$trouve) {
    echo json_encode(['ok' => false, 'message' => 'Commande introuvable']);
    exit();
}

if (!$ok) {
    echo json_encode(['ok' => false, 'message' => $message]);
    exit();
}

ecrire_json('commandes.json', $commandes);
ecrire_json('utilisateurs.json', $utilisateurs);

echo json_encode([
    'ok' => $ok,
    'message' => $message,
    'statut' => $statut,
    'statut_libelle' => ucfirst(str_replace('_', ' ', $statut)),
    'prochaine_action' => $prochaineAction
]);
