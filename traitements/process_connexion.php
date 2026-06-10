<?php
require_once __DIR__ . '/../Includes/fonctions.php';

// 1. RÉCUPÉRATION ET NETTOYAGE DES DONNÉES (trim enlève les espaces vides)
$login = isset($_POST['identifiant']) ? trim($_POST['identifiant']) : '';
$motDePasse = isset($_POST['mot_de_passe']) ? trim($_POST['mot_de_passe']) : '';
$utilisateurTrouve = null;
$utilisateurs = lire_json('utilisateurs.json');

// 2. RECHERCHE DE L'UTILISATEUR
foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['login'] === $login) {
        $utilisateurTrouve = $utilisateur;
        break; // Arrête la boucle dès qu'il est trouvé pour optimiser
    }
}

// 3. SÉCURITÉ : MAUVAIS IDENTIFIANTS
// Rejette si l'utilisateur n'existe pas OU si le mot de passe est faux
if ($utilisateurTrouve === null || $utilisateurTrouve['mdp'] !== $motDePasse) {
    ajouter_incident('connexion', 'Mauvais identifiants', $login); // Log l'erreur
    header('Location: ../connexion.php?erreur=identifiants_incorrects');
    exit();
}

// 4. SÉCURITÉ : COMPTE BLOQUÉ OU DÉSACTIVÉ
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

// 5. CONNEXION RÉUSSIE : Initialisation de la session
$_SESSION['utilisateur_id'] = $utilisateurTrouve['id'];

// Initialise un panier vide s'il n'existe pas encore
if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// 6. MISE À JOUR DE LA DATE DE DERNIÈRE CONNEXION
// Le '&' est crucial : il permet de modifier le tableau d'origine par référence
foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] === (int) $utilisateurTrouve['id']) {
        $utilisateur['derniere_connexion'] = date('Y-m-d H:i');
        break;
    }
}

ecrire_json('utilisateurs.json', $utilisateurs); // Sauvegarde dans le JSON
ajouter_incident('connexion', 'Connexion reussie', $login, $utilisateurTrouve['id']);

// 7. REDIRECTION SELON LE RÔLE
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

// Par défaut pour un client : direction le profil
header('Location: ../profil.php');
exit();