<?php
session_start();
 
$nom       = $_POST['nom'];
$prenom    = $_POST['prenom'];
$email     = $_POST['email'];
$civilite  = $_POST['civilite'];
$adresse   = $_POST['adresse'];
$telephone = $_POST['telephone'];
$mdp       = $_POST['mot_de_passe'];
 
// On lit le fichier utilisateurs
$data         = file_get_contents('../data/utilisateurs.json');
$utilisateurs = json_decode($data, true);
 
// On vérifie si l'email existe déjà
foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['email'] == $email) {
        header('Location: ../inscription.php?erreur=email_existant');
        exit();
    }
}
 
// On calcule le nouvel id (le plus grand id + 1)
$nouvel_id = 1;
foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur['id'] >= $nouvel_id) {
        $nouvel_id = $utilisateur['id'] + 1;
    }
}
 
// On crée le nouvel utilisateur
$nouvel_utilisateur = [
    "id"               => $nouvel_id,
    "nom"              => $nom,
    "prenom"           => $prenom,
    "email"            => $email,
    "login"            => $email,
    "mdp"              => $mdp,
    "role"             => "client",
    "civilite"         => $civilite,
    "adresse"          => $adresse,
    "telephone"        => $telephone,
    "date_inscription" => date("Y-m-d"),
    "statut"           => "actif",
    "fidelite"         => "standard",
    "commandes"        => []
];
 
// On l'ajoute au tableau et on réécrit le fichier
$utilisateurs[] = $nouvel_utilisateur;
file_put_contents('../data/utilisateurs.json', json_encode($utilisateurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
 
// On connecte directement l'utilisateur
$_SESSION['id']     = $nouvel_utilisateur['id'];
$_SESSION['login']  = $nouvel_utilisateur['login'];
$_SESSION['nom']    = $nouvel_utilisateur['nom'];
$_SESSION['prenom'] = $nouvel_utilisateur['prenom'];
$_SESSION['role']   = $nouvel_utilisateur['role'];
 
// On redirige vers l'accueil
header('Location: ../accueil.php');
exit();
?>
 
