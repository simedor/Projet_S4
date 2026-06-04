<?php
require_once __DIR__ . '/../Includes/fonctions.php';

$login = isset($_POST['identifiant']) ? trim($_POST['identifiant']) : '';
$motDePasse = isset($_POST['mot_de_passe']) ? trim($_POST['mot_de_passe']) : '';
$utilisateurTrouve = null;
$utilisateurs = lire_json('utilisateurs.json');

foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['login'] === $login) {
        $utilisateurTrouve = $utilisateur;
        break;
    }
}

if ($utilisateurTrouve === null || $utilisateurTrouve['mdp'] !== $motDePasse) {
    ajouter_incident('connexion', 'Mauvais identifiants', $login);
    header('Location: ../connexion.php?erreur=identifiants_incorrects');
    exit();
}

if ($utilisateurTrouve['statut_compte'] === 'bloque') {
    ajouter_incident('connexion', 'Tentative de connexion sur compte bloque', $login, $utilisateurTrouve['id']);
    header('Location: ../connexion.php?erreur=compte_bloque');
    exit();
}

if ($utilisateurTrouve['statut_compte'] === 'desactive') {
    ajouter_incident('connexion', 'Tentative de connexion sur compte desactive', $login, $utilisateurTrouve['id']);
    header('Location: ../connexion.php?erreur=compte_desactive');
    exit();
}

$_SESSION['utilisateur_id'] = $utilisateurTrouve['id'];

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] === (int) $utilisateurTrouve['id']) {
        $utilisateur['derniere_connexion'] = date('Y-m-d H:i');
        break;
    }
}

ecrire_json('utilisateurs.json', $utilisateurs);
ajouter_incident('connexion', 'Connexion reussie', $login, $utilisateurTrouve['id']);

if ($utilisateurTrouve['role'] === 'admin') {
    header('Location: ../administrateur.php');
    exit();
}

if ($utilisateurTrouve['role'] === 'restaurateur') {
    header('Location: ../commande.php');
    exit();
}

if ($utilisateurTrouve['role'] === 'livreur') {
    header('Location: ../livraison.php');
    exit();
}

header('Location: ../profil.php');
exit();
