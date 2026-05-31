<?php
require_once __DIR__ . '/fonctions.php';

$titrePage = isset($titre_page) ? $titre_page : 'CY Pizza';
$utilisateur = null;
$nombrePanier = 0;
$utilisateurs = lire_json('utilisateurs.json');

if (isset($_SESSION['utilisateur_id'])) {
    foreach ($utilisateurs as $unUtilisateur) {
        if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
            $utilisateur = $unUtilisateur;
            break;
        }
    }
}

if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $article) {
        $nombrePanier += (int) $article['quantite'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($titrePage); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="entete">
        <div class="logo_zone">
            <h1>CY Pizza</h1>
        </div>

        <nav id="menu_principal">
            <ul>
                <li><a href="accueil.php">Accueil</a></li>
                <li><a href="presentation.php">Carte</a></li>

                <?php if ($utilisateur !== null && $utilisateur['role'] === 'client') : ?>
                    <li><a href="panier.php">Panier (<?php echo $nombrePanier; ?>)</a></li>
                <?php endif; ?>

                <?php if ($utilisateur !== null && $utilisateur['role'] === 'restaurateur') : ?>
                    <li><a href="commande.php">Commandes</a></li>
                <?php endif; ?>

                <?php if ($utilisateur !== null && $utilisateur['role'] === 'livreur') : ?>
                    <li><a href="livraison.php">Livraison</a></li>
                <?php endif; ?>

                <?php if ($utilisateur !== null && $utilisateur['role'] === 'admin') : ?>
                    <li><a href="administrateur.php">Admin</a></li>
                <?php endif; ?>

                <?php if ($utilisateur !== null) : ?>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="traitements/process_deconnexion.php">Deconnexion</a></li>
                <?php else : ?>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>
                <?php endif; ?>

                <li><a href="#" id="btn_theme">Theme</a></li>
            </ul>
        </nav>
    </header>

    <main class="contenu_page">
