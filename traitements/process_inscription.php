<?php
require_once __DIR__ . '/../Includes/fonctions.php';

$champs = ['nom', 'prenom', 'login', 'email', 'mot_de_passe', 'naissance', 'civilite', 'adresse', 'telephone'];

foreach ($champs as $champ) {
    if (!isset($_POST[$champ]) || trim($_POST[$champ]) === '') {
        header('Location: ../inscription.php?erreur=champs_manquants');
        exit();
    }
}

$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$login = trim($_POST['login']);
$email = trim($_POST['email']);
$motDePasse = trim($_POST['mot_de_passe']);
$naissance = trim($_POST['naissance']);
$civilite = trim($_POST['civilite']);
$adresse = trim($_POST['adresse']);
$telephone = trim($_POST['telephone']);

$listeUtilisateurs = lire_json('utilisateurs.json');
$nouvelId = 1;

foreach ($listeUtilisateurs as $utilisateur) {
    if ($utilisateur['login'] === $login) {
        header('Location: ../inscription.php?erreur=login_existant');
        exit();
    }

    if ($utilisateur['email'] === $email) {
        header('Location: ../inscription.php?erreur=email_existant');
        exit();
    }

    if ((int) $utilisateur['id'] >= $nouvelId) {
        $nouvelId = (int) $utilisateur['id'] + 1;
    }
}

$nouvelUtilisateur = [
    'id' => $nouvelId,
    'login' => $login,
    'mdp' => $motDePasse,
    'role' => 'client',
    'nom' => $nom,
    'prenom' => $prenom,
    'email' => $email,
    'pseudo' => $login,
    'naissance' => $naissance,
    'adresse' => $adresse,
    'telephone' => $telephone,
    'civilite' => $civilite,
    'date_inscription' => date('Y-m-d'),
    'derniere_connexion' => date('Y-m-d H:i'),
    'statut_compte' => 'actif',
    'fidelite' => 'Standard',
    'remise' => 0,
    'disponible' => false
];

$listeUtilisateurs[] = $nouvelUtilisateur;
ecrire_json('utilisateurs.json', $listeUtilisateurs);

$_SESSION['utilisateur_id'] = $nouvelUtilisateur['id'];
$_SESSION['panier'] = [];

header('Location: ../profil.php');
exit();
