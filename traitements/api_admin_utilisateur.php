<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['ok' => false, 'message' => 'Non connecte']);
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
    echo json_encode(['ok' => false, 'message' => 'Acces refuse']);
    exit();
}

$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$message = 'Utilisateur introuvable';
$prochaineAction = 'bloquer';
$statut = '';
$trouve = false;

if ($userId === (int) $admin['id']) {
    echo json_encode(['ok' => false, 'message' => 'Action impossible sur votre compte']);
    exit();
}

foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] === $userId) {
        $trouve = true;

        if ($action === 'bloquer') {
            $utilisateur['statut_compte'] = 'bloque';
            $message = 'Utilisateur bloque';
            $prochaineAction = 'debloquer';
            ajouter_incident('admin', 'Compte bloque par un admin', $utilisateur['login'], $utilisateur['id']);
        } else {
            $utilisateur['statut_compte'] = 'actif';
            $message = 'Utilisateur debloque';
            $prochaineAction = 'bloquer';
            ajouter_incident('admin', 'Compte debloque par un admin', $utilisateur['login'], $utilisateur['id']);
        }

        $statut = $utilisateur['statut_compte'];
        break;
    }
}

if (!$trouve) {
    echo json_encode(['ok' => false, 'message' => 'Utilisateur introuvable']);
    exit();
}

ecrire_json('utilisateurs.json', $utilisateurs);
echo json_encode(['ok' => true, 'message' => $message, 'statut' => $statut, 'action' => $prochaineAction]);
