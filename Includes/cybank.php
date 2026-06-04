<?php
require_once __DIR__ . '/fonctions.php';
require_once __DIR__ . '/../getapikey.php';

define('CYBANK_VENDEUR', 'MEF-2_F');
define('CYBANK_URL', 'https://www.plateforme-smc.fr/cybank/index.php');

function cybank_racine_site()
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
    $segments = explode('/', trim($script, '/'));

    if (count($segments) > 0 && $segments[0] !== '') {
        return '/' . $segments[0];
    }

    return '';
}

function cybank_url_site($chemin)
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

    return $scheme . '://' . $host . cybank_racine_site() . '/' . ltrim($chemin, '/');
}

function cybank_nouvelle_transaction()
{
    return strtoupper(substr(date('YmdHis') . md5(uniqid('', true)), 0, 20));
}

function cybank_cle_api_valide()
{
    $apiKey = getAPIKey(CYBANK_VENDEUR);

    if (!preg_match('/^[0-9a-zA-Z]{15}$/', $apiKey)) {
        return '';
    }

    return $apiKey;
}

function cybank_creer_paiement($typeOperation, $montant, $donnees)
{
    $apiKey = cybank_cle_api_valide();

    if ($apiKey === '') {
        return null;
    }

    $montantFormate = number_format((float) $montant, 2, '.', '');
    $transaction = cybank_nouvelle_transaction();
    $token = substr(md5($transaction . microtime(true)), 0, 20);
    $retour = cybank_url_site('retour_paiement.php?token=' . $token);
    $control = md5($apiKey . '#' . $transaction . '#' . $montantFormate . '#' . CYBANK_VENDEUR . '#' . $retour . '#');

    $paiements = lire_json('paiements_attente.json');
    $paiements[] = [
        'token' => $token,
        'transaction' => $transaction,
        'montant' => $montantFormate,
        'vendeur' => CYBANK_VENDEUR,
        'retour' => $retour,
        'control' => $control,
        'type_operation' => $typeOperation,
        'client_id' => isset($_SESSION['utilisateur_id']) ? (int) $_SESSION['utilisateur_id'] : 0,
        'date_creation' => date('Y-m-d H:i:s'),
        'donnees' => $donnees
    ];

    ecrire_json('paiements_attente.json', $paiements);

    return [
        'token' => $token,
        'transaction' => $transaction,
        'montant' => $montantFormate,
        'vendeur' => CYBANK_VENDEUR,
        'retour' => $retour,
        'control' => $control
    ];
}

function cybank_trouver_paiement($token)
{
    foreach (lire_json('paiements_attente.json') as $paiement) {
        if (isset($paiement['token']) && $paiement['token'] === $token) {
            return $paiement;
        }
    }

    return null;
}

function cybank_supprimer_paiement($token)
{
    $paiements = lire_json('paiements_attente.json');
    $nouvelleListe = [];

    foreach ($paiements as $paiement) {
        if (!isset($paiement['token']) || $paiement['token'] !== $token) {
            $nouvelleListe[] = $paiement;
        }
    }

    ecrire_json('paiements_attente.json', $nouvelleListe);
}

function cybank_control_retour_valide($transaction, $montant, $vendeur, $statut, $control)
{
    $apiKey = cybank_cle_api_valide();

    if ($apiKey === '') {
        return false;
    }

    $attendu = md5($apiKey . '#' . $transaction . '#' . $montant . '#' . $vendeur . '#' . $statut . '#');

    return $attendu === $control;
}
