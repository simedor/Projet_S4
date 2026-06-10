<?php
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DOSSIER_DATA', __DIR__ . '/../data');

function lire_json($nomFichier)
{
    $chemin = DOSSIER_DATA . '/' . $nomFichier;

    if (!file_exists($chemin)) {
        return [];
    }

    $contenu = file_get_contents($chemin);
    $donnees = json_decode($contenu, true);

    if (!is_array($donnees)) {
        return [];
    }

    return $donnees;
}

function ecrire_json($nomFichier, $donnees)
{
    $chemin = DOSSIER_DATA . '/' . $nomFichier;
    file_put_contents($chemin, json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function ajouter_incident($type, $message, $login = '', $utilisateurId = 0)
{
    $incidents = lire_json('incidents.json');
    $incidents[] = [
        'date' => date('Y-m-d H:i:s'),
        'type' => $type,
        'message' => $message,
        'login' => $login,
        'utilisateur_id' => (int) $utilisateurId
    ];

    if (count($incidents) > 200) {
        $incidents = array_slice($incidents, -200);
    }

    ecrire_json('incidents.json', $incidents);
}

function h($texte)
{
    return htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');
}

function image_plat($image)
{
    if (!empty($image) && file_exists(__DIR__ . '/../' . $image)) {
        return $image;
    }

    return 'Images/Reine.png';
}

function trouver_code_promo($code)
{
    $codeRecherche = strtoupper(trim((string) $code));

    if ($codeRecherche === '') {
        return null;
    }

    foreach (lire_json('codes_promo.json') as $promo) {
        $promoCode = isset($promo['code']) ? strtoupper(trim((string) $promo['code'])) : '';
        $actif = !isset($promo['actif']) || $promo['actif'];

        if ($promoCode === $codeRecherche && $actif) {
            return $promo;
        }
    }

    return null;
}

function calculer_reduction_promo($total, $pourcentage)
{
    $montant = (float) $total;
    $reduction = (int) $pourcentage;

    if ($montant <= 0 || $reduction <= 0) {
        return 0;
    }

    return round($montant * $reduction / 100, 2);
}

function remise_fidelite($fidelite)
{
    if ($fidelite === 'VIP') {
        return 10;
    }

    if ($fidelite === 'Premium') {
        return 5;
    }

    return 0;
}

function statistiques_plats_commandes()
{
    $stats = [];

    foreach (lire_json('commandes.json') as $commande) {
        if (isset($commande['statut_commande']) && $commande['statut_commande'] === 'abandonnee') {
            continue;
        }

        if (!isset($commande['lignes']) || !is_array($commande['lignes'])) {
            continue;
        }

        foreach ($commande['lignes'] as $ligne) {
            if (!isset($ligne['nom'])) {
                continue;
            }

            $nomPlat = $ligne['nom'];
            $quantite = isset($ligne['quantite']) ? (int) $ligne['quantite'] : 0;

            if (!isset($stats[$nomPlat])) {
                $stats[$nomPlat] = 0;
            }

            $stats[$nomPlat] += $quantite;
        }
    }

    arsort($stats);
    return $stats;
}

function calculer_total_commande_apres_remises($totalBrut, $commande)
{
    $total = (float) $totalBrut;
    $remiseFidelite = 0;
    $remisePromo = 0;
    $pourcentageFidelite = isset($commande['pourcentage_fidelite']) ? (int) $commande['pourcentage_fidelite'] : 0;
    $pourcentagePromo = isset($commande['pourcentage_promo']) ? (int) $commande['pourcentage_promo'] : 0;

    if ($pourcentageFidelite <= 0 && isset($commande['fidelite']) && $commande['fidelite'] !== '') {
        $pourcentageFidelite = remise_fidelite($commande['fidelite']);
    }

    if ($pourcentageFidelite > 0) {
        $remiseFidelite = calculer_reduction_promo($total, $pourcentageFidelite);
        $total = max(0, $total - $remiseFidelite);
    }

    if (isset($commande['code_promo']) && $commande['code_promo'] !== '' && $pourcentagePromo > 0) {
        $remisePromo = calculer_reduction_promo($total, $pourcentagePromo);
        $total = max(0, $total - $remisePromo);
    }

    return [
        'total' => round($total, 2),
        'remise_fidelite' => $remiseFidelite,
        'remise_promo' => $remisePromo,
        'pourcentage_fidelite' => $pourcentageFidelite,
        'pourcentage_promo' => $pourcentagePromo
    ];
}
