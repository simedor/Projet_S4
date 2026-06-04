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
