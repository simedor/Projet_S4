<?php
require_once __DIR__ . '/../Includes/fonctions.php';

$champs = ['nom', 'prenom', 'login', 'email', 'mot_de_passe', 'naissance', 'civilite', 'adresse', 'telephone'];

foreach ($champs as $champ) {
    if (!isset($_POST[$champ]) || trim($_POST[$champ]) === '') {
        header('Location: ../inscription.php?erreur=champs_manquants');
        exit();
    }
}

$listeUtilisateurs = lire_json('utilisateurs.json');
$nouvelId = 1;

foreach ($listeUtilisateurs as $utilisateur) {
    if ($utilisateur['login'] === trim($_POST['login'])) {
        header('Location: ../inscription.php?erreur=login_existant');
        exit();
    }

    if ($utilisateur['email'] === trim($_POST['email'])) {
        header('Location: ../inscription.php?erreur=email_existant');
        exit();
    }

    if ((int) $utilisateur['id'] >= $nouvelId) {
        $nouvelId = (int) $utilisateur['id'] + 1;
    }
}

$listeUtilisateurs[] = [
    'id' => $nouvelId,
    'login' => trim($_POST['login']),
    'mdp' => trim($_POST['mot_de_passe']),
    'role' => 'client',
    'nom' => trim($_POST['nom']),
    'prenom' => trim($_POST['prenom']),
    'email' => trim($_POST['email']),
    'pseudo' => trim($_POST['login']),
    'naissance' => trim($_POST['naissance']),
    'adresse' => trim($_POST['adresse']),
    'telephone' => trim($_POST['telephone']),
    'civilite' => trim($_POST['civilite']),
    'date_inscription' => date('Y-m-d'),
    'derniere_connexion' => date('Y-m-d H:i'),
    'statut_compte' => 'actif',
    'fidelite' => 'Standard',
    'remise' => 0,
    'disponible' => false,
    'avoir' => 0
];

ecrire_json('utilisateurs.json', $listeUtilisateurs);

$_SESSION['utilisateur_id'] = $nouvelId;
$_SESSION['panier'] = [];

header('Location: ../profil.php');
exit();
