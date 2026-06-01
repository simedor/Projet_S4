<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$livreur = null;
$commandes = [];

foreach (lire_json('utilisateurs.json') as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $livreur = $unUtilisateur;
        break;
    }
}

if ($livreur === null || $livreur['role'] !== 'livreur') {
    header('Location: accueil.php');
    exit();
}

foreach (lire_json('commandes.json') as $commande) {
    if ((int) $commande['livreur_id'] === (int) $livreur['id']) {
        $commandes[] = $commande;
    }
}

$titre_page = 'Livraison';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Mes livraisons</h2>
    <div class="grille_cartes">
        <?php foreach ($commandes as $commande) : ?>
            <article class="carte_resume" id="carte_livraison_<?php echo (int) $commande['id']; ?>">
                <h3>Commande #<?php echo (int) $commande['id']; ?></h3>
                <p><strong>Client :</strong> <?php echo h($commande['client_nom']); ?></p>
                <p><strong>Adresse :</strong> <?php echo h($commande['adresse']); ?></p>
                <p><strong>Statut :</strong> <span class="statut_livraison"><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></span></p>
                <p><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir</a></p>
                <?php if ($commande['statut_commande'] === 'en_livraison') : ?>
                    <button type="button" class="btn-livraison" data-commande-id="<?php echo (int) $commande['id']; ?>">Marquer livree</button>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <p id="message_livraison"></p>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
