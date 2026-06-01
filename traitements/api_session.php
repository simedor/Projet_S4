<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['statut' => 'deconnecte']);
    exit();
}

foreach (lire_json('utilisateurs.json') as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        if ($utilisateur['statut_compte'] === 'bloque') {
            session_destroy();
            echo json_encode(['statut' => 'bloque']);
            exit();
        }

        echo json_encode(['statut' => 'ok']);
        exit();
    }
}

echo json_encode(['statut' => 'deconnecte']);
