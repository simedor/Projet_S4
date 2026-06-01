<?php
require_once __DIR__ . '/Includes/fonctions.php';

$plats = lire_json('plats.json');
$utilisateur = null;
$estClient = false;
$categories = [];
$regimes = [];
$gouts = [];

if (isset($_SESSION['utilisateur_id'])) {
    foreach (lire_json('utilisateurs.json') as $unUtilisateur) {
        if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
            $utilisateur = $unUtilisateur;
            break;
        }
    }

    if ($utilisateur !== null && $utilisateur['role'] === 'client') {
        $estClient = true;
    }
}

foreach ($plats as $plat) {
    if (!in_array($plat['categorie'], $categories, true)) {
        $categories[] = $plat['categorie'];
    }

    if (!in_array($plat['regime'], $regimes, true)) {
        $regimes[] = $plat['regime'];
    }

    if (!in_array($plat['gout'], $gouts, true)) {
        $gouts[] = $plat['gout'];
    }
}

sort($categories);
sort($regimes);
sort($gouts);

$titre_page = 'Carte';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Toute la carte</h2>

    <form id="form_filtres_plats" class="formulaire filtre_ligne">
        <div>
            <label for="recherche">Recherche</label>
            <input type="search" id="recherche" name="recherche" placeholder="Ex : Reine">
        </div>

        <div>
            <label for="categorie">Categorie</label>
            <select name="categorie" id="categorie">
                <option value="">Toutes</option>
                <?php foreach ($categories as $categorie) : ?>
                    <option value="<?php echo h($categorie); ?>"><?php echo h($categorie); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="regime">Regime</label>
            <select name="regime" id="regime">
                <option value="">Tous</option>
                <?php foreach ($regimes as $regime) : ?>
                    <option value="<?php echo h($regime); ?>"><?php echo h($regime); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="gout">Gout</label>
            <select name="gout" id="gout">
                <option value="">Tous</option>
                <?php foreach ($gouts as $gout) : ?>
                    <option value="<?php echo h($gout); ?>"><?php echo h($gout); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="tri">Tri</label>
            <select name="tri" id="tri">
                <option value="">Aucun</option>
                <option value="prix_asc">Prix croissant</option>
                <option value="prix_desc">Prix decroissant</option>
                <option value="popularite">Les plus commandes</option>
                <option value="nom">Nom A-Z</option>
            </select>
        </div>
    </form>
</section>

<section class="bloc_page">
    <p id="resultat_plats_info"><?php echo count($plats); ?> element(s)</p>
    <div id="liste_plats" class="grille_cartes"></div>
</section>

<script>
window.platsInitiaux = <?php echo json_encode($plats, JSON_UNESCAPED_UNICODE); ?>;
window.estClient = <?php echo $estClient ? 'true' : 'false'; ?>;
</script>
</main>
<script src="script.js"></script>
</body>
</html>
