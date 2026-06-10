<?php
require_once __DIR__ . '/../Includes/fonctions.php';

// 1. VÉRIFICATION GLOBALE : Tous les champs obligatoires sont-ils présents et non vides ?
$champs = ['nom', 'prenom', 'login', 'email', 'mot_de_passe', 'naissance', 'civilite', 'adresse', 'telephone'];

foreach ($champs as $champ) {
    if (!isset($_POST[$champ]) || trim($_POST[$champ]) === '') {
        header('Location: ../inscription.php?erreur=champs_manquants');
        exit();
    }
}

$email = trim($_POST['email']);
$telephone = trim($_POST['telephone']);
$naissance = trim($_POST['naissance']);
$motDePasse = trim($_POST['mot_de_passe']);
$infosComplementaires = isset($_POST['infos_complementaires']) ? trim($_POST['infos_complementaires']) : '';

// 2. CONTRÔLE SÉCURITÉ PHP (Empêche de tricher en modifiant le Javascript)
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{10}$/', $telephone) || strlen($motDePasse) < 4) {
    header('Location: ../inscription.php?erreur=champs_manquants');
    exit();
}

// 3. VÉRIFICATION D'ÂGE (Minimum 13 ans via calcul du timestamp)
$timestampNaissance = strtotime($naissance);
if ($timestampNaissance === false || $timestampNaissance > strtotime('-13 years')) {
    header('Location: ../inscription.php?erreur=champs_manquants');
    exit();
}

// 4. RECHERCHE DE DOUBLONS ET CALCUL DU NOUVEL ID
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

    // Auto-incrémentation : trouve l'ID le plus grand et fait +1
    if ((int) $utilisateur['id'] >= $nouvelId) {
        $nouvelId = (int) $utilisateur['id'] + 1;
    }
}

// 5. CRÉATION DU COMPTE AVEC LES VALEURS PAR DÉFAUT
$listeUtilisateurs[] = [
    'id' => $nouvelId,
    'login' => trim($_POST['login']),
    'mdp' => trim($_POST['mot_de_passe']),
    'role' => 'client', // Rôle de base
    'nom' => trim($_POST['nom']),
    'prenom' => trim($_POST['prenom']),
    'email' => trim($_POST['email']),
    'pseudo' => trim($_POST['login']),
    'naissance' => trim($_POST['naissance']),
    'adresse' => trim($_POST['adresse']),
    'telephone' => trim($_POST['telephone']),
    'infos_complementaires' => $infosComplementaires,
    'civilite' => trim($_POST['civilite']),
    'date_inscription' => date('Y-m-d'),
    'derniere_connexion' => date('Y-m-d H:i'),
    'statut_compte' => 'actif',
    'fidelite' => 'Standard',
    'remise' => 0,
    'disponible' => false,
    'avoir' => 0
];

ecrire_json('utilisateurs.json', $listeUtilisateurs); // Sauvegarde BDD
ajouter_incident('inscription', 'Nouveau compte cree', trim($_POST['login']), $nouvelId);

// 6. CONNEXION AUTOMATIQUE
$_SESSION['utilisateur_id'] = $nouvelId;
$_SESSION['panier'] = []; // Panier vierge pour démarrer

header('Location: ../profil.php');
exit();