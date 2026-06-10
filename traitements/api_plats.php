<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$resultats = [];
$statsCommandes = statistiques_plats_commandes();

foreach (lire_json('plats.json') as $plat) {
    $garder = true;
    $plat['popularite'] = isset($statsCommandes[$plat['nom']]) ? (int) $statsCommandes[$plat['nom']] : 0;

    if ($recherche !== '' && stripos($plat['nom'] . ' ' . $plat['description'], $recherche) === false) {
        $garder = false;
    }

    if ($type !== '' && (!isset($plat['type']) || $plat['type'] !== $type)) {
        $garder = false;
    }

    if ($garder) {
        $resultats[] = $plat;
    }
}

echo json_encode(['plats' => $resultats], JSON_UNESCAPED_UNICODE);
