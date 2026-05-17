<?php
session_start();

$login = $_POST['identifiant'];
$mdp   = $_POST['mot_de_passe'];

// On lit le fichier utilisateurs
$data        = file_get_contents('../data/utilisateurs.json');
$utilisateurs = json_decode($data, true);

// On cherche l'utilisateur avec ce login
$utilisateur_trouve = null;
foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['login'] == $login) {
        $utilisateur_trouve = $utilisateur;
        break; // On s'arrête dès qu'on l'a trouvé
    }
}

// Si pas trouvé OU mauvais mot de passe → erreur
if ($utilisateur_trouve == null || $utilisateur_trouve['mdp'] != $mdp) {
    header('Location: ../connexion.php?erreur=identifiants_incorrects');
    exit();
}

// Si le compte est bloqué → erreur
if ($utilisateur_trouve['statut'] == 'bloque') {
    header('Location: ../connexion.php?erreur=compte_bloque');
    exit();
}

// On sauvegarde les infos de l'utilisateur en session
$_SESSION['id']     = $utilisateur_trouve['id'];
$_SESSION['login']  = $utilisateur_trouve['login'];
$_SESSION['nom']    = $utilisateur_trouve['nom'];
$_SESSION['prenom'] = $utilisateur_trouve['prenom'];
$_SESSION['role']   = $utilisateur_trouve['role'];

// On redirige selon le rôle
if ($utilisateur_trouve['role'] == 'admin') {
    header('Location: ../administrateur.php');
} elseif ($utilisateur_trouve['role'] == 'restaurateur') {
    header('Location: ../commande.php');
} elseif ($utilisateur_trouve['role'] == 'livreur') {
    header('Location: ../livraison.php');
} else {
    // Client → accueil
    header('Location: ../accueil.php');
}
exit();
?>
