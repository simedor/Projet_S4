<?php
require_once __DIR__ . '/Includes/fonctions.php';

$plats = lire_json('plats.json');
$platDuJour = null;
$bestSellers = [];

foreach ($plats as $plat) {
    if (!empty($plat['plat_du_jour'])) {
        $platDuJour = $plat;
    }

    if (!empty($plat['best_seller'])) {
        $bestSellers[] = $plat;
    }
}

$titre_page = 'Accueil';
include 'Includes/header.php';
?>

<section class="hero">
    <div>
        <h2>Bienvenue sur CY Pizza</h2>
        <p>Consultez la carte, ajoutez vos articles au panier et suivez vos commandes.</p>
        <a class="bouton_action" href="presentation.php">Voir la carte</a>
    </div>
</section>

<?php if ($platDuJour !== null) : ?>
    <section class="bloc_page">
        <h2>Plat du jour</h2>
        <article class="carte_plat carte_grande">
            <img src="<?php echo h(image_plat($platDuJour['image'])); ?>" alt="<?php echo h($platDuJour['nom']); ?>">
            <div>
                <h3><?php echo h($platDuJour['nom']); ?></h3>
                <p><?php echo h($platDuJour['description']); ?></p>
                <p><strong><?php echo number_format($platDuJour['prix'], 2, ',', ' '); ?> EUR</strong></p>
                <p>Categorie : <?php echo h($platDuJour['categorie']); ?></p>
            </div>
        </article>
    </section>
<?php endif; ?>

<section class="bloc_page">
    <h2>Nos best sellers</h2>

    <div class="grille_cartes">
        <?php foreach ($bestSellers as $plat) : ?>
            <article class="carte_plat">
                <img src="<?php echo h(image_plat($plat['image'])); ?>" alt="<?php echo h($plat['nom']); ?>">
                <div>
                    <h3><?php echo h($plat['nom']); ?></h3>
                    <p><?php echo h($plat['description']); ?></p>
                    <p><strong><?php echo number_format($plat['prix'], 2, ',', ' '); ?> EUR</strong></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
