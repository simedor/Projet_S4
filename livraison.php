<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;
$utilisateurs = lire_json('utilisateurs.json');

foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

if ($utilisateur === null || $utilisateur['role'] !== 'livreur') {
    header('Location: accueil.php');
    exit();
}

$mesCommandes = [];
$toutesLesCommandes = lire_json('commandes.json');

foreach ($toutesLesCommandes as $commande) {
    if ((int) $commande['livreur_id'] === (int) $utilisateur['id']) {
        $mesCommandes[] = $commande;
    }
}

usort($mesCommandes, function ($a, $b) {
    return strcmp($b['date_commande'], $a['date_commande']);
});

$commandesActives = [];
$commandesFermees = [];

foreach ($mesCommandes as $commande) {
    if ($commande['statut_commande'] === 'en_livraison') {
        $commandesActives[] = $commande;
    } else {
        $commandesFermees[] = $commande;
    }
}

$titre_page = 'Livraison';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Mes livraisons</h2>
    <p>Livreur connecte : <?php echo h($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></p>
</section>

<section class="bloc_page">
    <h2>Livraisons en cours</h2>

    <?php if (empty($commandesActives)) : ?>
        <p class="info">Aucune livraison en cours.</p>
    <?php else : ?>
        <div class="grille_cartes">
            <?php foreach ($commandesActives as $commande) : ?>
                <article class="carte_resume">
                    <h3>Commande #<?php echo (int) $commande['id']; ?></h3>
                    <p><strong>Client :</strong> <?php echo h($commande['client_nom']); ?></p>
                    <p><strong>Adresse :</strong> <?php echo h($commande['adresse']); ?></p>
                    <p><strong>Telephone :</strong> <?php echo h($commande['telephone']); ?></p>
                    <p><strong>Total :</strong> <?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</p>
                    <p><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir le detail</a></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="bloc_page">
    <h2>Historique des livraisons attribuees</h2>

    <?php if (empty($commandesFermees)) : ?>
        <p class="info">Pas encore d'historique.</p>
    <?php else : ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Statut</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandesFermees as $commande) : ?>
                    <tr>
                        <td>#<?php echo (int) $commande['id']; ?></td>
                        <td><?php echo h($commande['client_nom']); ?></td>
                        <td><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></td>
                        <td><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
