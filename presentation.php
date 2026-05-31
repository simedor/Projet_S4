<?php
require_once __DIR__ . '/Includes/fonctions.php';

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';

$plats = lire_json('plats.json');
$categories = [];
$types = [];
$elementsFiltres = [];

foreach ($plats as $plat) {
    if (!in_array($plat['categorie'], $categories, true)) {
        $categories[] = $plat['categorie'];
    }

    if (!in_array($plat['type'], $types, true)) {
        $types[] = $plat['type'];
    }

    $aAfficher = true;

    if ($recherche !== '' && stripos($plat['nom'] . ' ' . $plat['description'], $recherche) === false) {
        $aAfficher = false;
    }

    if ($categorie !== '' && $plat['categorie'] !== $categorie) {
        $aAfficher = false;
    }

    if ($type !== '' && $plat['type'] !== $type) {
        $aAfficher = false;
    }

    if ($aAfficher) {
        $elementsFiltres[] = $plat;
    }
}

sort($categories);
sort($types);

$titre_page = 'Carte';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Toute la carte</h2>

    <form method="GET" class="formulaire filtre_ligne">
        <div>
            <label for="recherche">Recherche</label>
            <input type="search" name="recherche" id="recherche" value="<?php echo h($recherche); ?>" placeholder="Nom ou description">
        </div>

        <div>
            <label for="categorie">Categorie</label>
            <select name="categorie" id="categorie">
                <option value="">Toutes</option>
                <?php foreach ($categories as $uneCategorie) : ?>
                    <option value="<?php echo h($uneCategorie); ?>" <?php echo $categorie === $uneCategorie ? 'selected' : ''; ?>>
                        <?php echo h($uneCategorie); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="type">Type</label>
            <select name="type" id="type">
                <option value="">Tous</option>
                <?php foreach ($types as $unType) : ?>
                    <option value="<?php echo h($unType); ?>" <?php echo $type === $unType ? 'selected' : ''; ?>>
                        <?php echo h($unType); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="zone_boutons">
            <button type="submit">Filtrer</button>
            <a class="bouton_secondaire" href="presentation.php">Reset</a>
        </div>
    </form>
</section>

<section class="bloc_page">
    <p><?php echo count($elementsFiltres); ?> element(s) affiche(s).</p>

    <div class="grille_cartes">
        <?php foreach ($elementsFiltres as $plat) : ?>
            <article class="carte_plat">
                <img src="<?php echo h(image_plat($plat['image'])); ?>" alt="<?php echo h($plat['nom']); ?>">
                <div>
                    <span class="badge"><?php echo h($plat['type']); ?></span>
                    <h3><?php echo h($plat['nom']); ?></h3>
                    <p><?php echo h($plat['description']); ?></p>
                    <p><strong><?php echo number_format($plat['prix'], 2, ',', ' '); ?> EUR</strong></p>
                    <p>Categorie : <?php echo h($plat['categorie']); ?></p>

                    <?php if (isset($_SESSION['utilisateur_id'])) : ?>
                        <?php
                        $estClient = false;
                        $listeUtilisateurs = lire_json('utilisateurs.json');

                        foreach ($listeUtilisateurs as $unUtilisateur) {
                            if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id'] && $unUtilisateur['role'] === 'client') {
                                $estClient = true;
                                break;
                            }
                        }
                        ?>
                    <?php if ($estClient) : ?>
                        <form action="traitements/process_panier.php" method="POST" class="petit_formulaire">
                            <input type="hidden" name="action" value="ajouter">
                            <input type="hidden" name="nom" value="<?php echo h($plat['nom']); ?>">
                            <label for="qte_<?php echo md5($plat['nom']); ?>">Quantite</label>
                            <input type="number" min="1" max="20" value="1" name="quantite" id="qte_<?php echo md5($plat['nom']); ?>">
                            <button type="submit">Ajouter au panier</button>
                        </form>
                    <?php else : ?>
                        <p>Cette page est reservee aux comptes clients pour commander.</p>
                    <?php endif; ?>
                    <?php else : ?>
                        <p><a href="connexion.php">Connectez-vous</a> avec un compte client pour commander.</p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
