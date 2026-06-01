<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';
$regime = isset($_GET['regime']) ? trim($_GET['regime']) : '';
$gout = isset($_GET['gout']) ? trim($_GET['gout']) : '';
$resultats = [];

foreach (lire_json('plats.json') as $plat) {
    $garder = true;

    if ($recherche !== '' && stripos($plat['nom'] . ' ' . $plat['description'], $recherche) === false) {
        $garder = false;
    }

    if ($categorie !== '' && $plat['categorie'] !== $categorie) {
        $garder = false;
    }

    if ($regime !== '' && $plat['regime'] !== $regime) {
        $garder = false;
    }

    if ($gout !== '' && $plat['gout'] !== $gout) {
        $garder = false;
    }

    if ($garder) {
        $resultats[] = $plat;
    }
}

echo json_encode(['plats' => $resultats], JSON_UNESCAPED_UNICODE);
