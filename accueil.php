<?php
require_once __DIR__ . '/Includes/fonctions.php';

$plats = lire_json('plats.json');
$commandes = lire_json('commandes.json');
$platDuJour = null;
$bestSellers = [];
$platsParNom = [];
$statsCommandes = [];
$platsPopulaires = [];

foreach ($plats as $plat) {
    $platsParNom[$plat['nom']] = $plat;

    if (!empty($plat['plat_du_jour'])) {
        $platDuJour = $plat;
    }

    if (!empty($plat['best_seller'])) {
        $bestSellers[] = $plat;
    }
}

foreach ($commandes as $commande) {
    if (!isset($commande['lignes']) || !is_array($commande['lignes'])) {
        continue;
    }

    foreach ($commande['lignes'] as $ligne) {
        $nomPlat = $ligne['nom'];
        $quantite = isset($ligne['quantite']) ? (int) $ligne['quantite'] : 0;

        if (!isset($statsCommandes[$nomPlat])) {
            $statsCommandes[$nomPlat] = 0;
        }

        $statsCommandes[$nomPlat] += $quantite;
    }
}

arsort($statsCommandes);

foreach ($statsCommandes as $nomPlat => $quantite) {
    if (isset($platsParNom[$nomPlat])) {
        $plat = $platsParNom[$nomPlat];
        $plat['total_commandes'] = $quantite;
        $platsPopulaires[] = $plat;
    }

    if (count($platsPopulaires) >= 3) {
        break;
    }
}

$titre_page = 'Accueil';
include 'Includes/header.php';
?>

<section class="hero">
    <div>
        <h2>Bienvenue sur CY Pizza</h2>
        <p>Decouvrez la carte et commandez en ligne.</p>
        <form action="presentation.php" method="GET" class="barre_recherche_accueil">
            <label for="recherche_accueil">Rechercher un plat</label>
            <div class="ligne_action">
                <input type="search" id="recherche_accueil" name="recherche" placeholder="Ex : Reine">
                <button type="submit">Rechercher</button>
            </div>
        </form>
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
            </div>
        </article>
    </section>
<?php endif; ?>

<?php if (!empty($platsPopulaires)) : ?>
    <section class="bloc_page">
        <h2>Les plus commandes en ce moment</h2>
        <div class="grille_cartes">
            <?php foreach ($platsPopulaires as $plat) : ?>
                <article class="carte_plat">
                    <img src="<?php echo h(image_plat($plat['image'])); ?>" alt="<?php echo h($plat['nom']); ?>">
                    <div>
                        <h3><?php echo h($plat['nom']); ?></h3>
                        <p><?php echo h($plat['description']); ?></p>
                        <p><strong><?php echo number_format($plat['prix'], 2, ',', ' '); ?> EUR</strong></p>
                        <p><?php echo (int) $plat['total_commandes']; ?> fois dans les commandes tests</p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
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
