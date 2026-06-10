<?php
require_once __DIR__ . '/Includes/fonctions.php';

$plats = lire_json('plats.json');
$utilisateur = null;
$estClient = false;
$rechercheInitiale = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$typeInitial = isset($_GET['type']) ? trim($_GET['type']) : '';
$types = [];
$platsAffiches = [];
$statsCommandes = statistiques_plats_commandes();

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
    $plat['popularite'] = isset($statsCommandes[$plat['nom']]) ? (int) $statsCommandes[$plat['nom']] : 0;
    $typePlat = isset($plat['type']) ? $plat['type'] : '';

    if ($typePlat !== '' && !in_array($typePlat, $types, true)) {
        $types[] = $typePlat;
    }

    $okRecherche = $rechercheInitiale === '' || stripos($plat['nom'] . ' ' . $plat['description'], $rechercheInitiale) !== false;
    $okType = $typeInitial === '' || $typePlat === $typeInitial;

    if ($okRecherche && $okType) {
        $platsAffiches[] = $plat;
    }
}

sort($types);

$titre_page = 'Carte';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <div class="ligne_titre">
        <h2>Toute la carte</h2>
        <div class="actions_outils">
            <button type="button" id="btn_menu_aleatoire">Choisir pour moi</button>
        </div>
    </div>

    <form id="form_filtres_plats" class="formulaire filtre_ligne" action="presentation.php" method="GET">
        <div>
            <label for="recherche">Recherche</label>
            <input type="search" id="recherche" name="recherche" placeholder="Ex : Reine" value="<?php echo h($rechercheInitiale); ?>">
        </div>

        <div>
            <label for="type">Type</label>
            <select name="type" id="type">
                <option value="">Tous</option>
                <?php foreach ($types as $type) : ?>
                    <option value="<?php echo h($type); ?>" <?php echo $typeInitial === $type ? 'selected' : ''; ?>><?php echo h(ucfirst($type)); ?></option>
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

        <div>
            <label>&nbsp;</label>
            <button type="submit">Filtrer</button>
        </div>
    </form>
</section>

<section class="bloc_page cache" id="bloc_aleatoire">
    <h2>Suggestion aleatoire</h2>
    <div id="resultat_aleatoire"></div>
</section>

<section class="bloc_page">
    <p id="resultat_plats_info"><?php echo count($platsAffiches); ?> element(s)</p>
    <div id="liste_plats" class="grille_cartes"></div>
</section>

<script>
window.platsInitiaux = <?php echo json_encode($platsAffiches, JSON_UNESCAPED_UNICODE); ?>;
window.estClient = <?php echo $estClient ? 'true' : 'false'; ?>;
</script>
</main>
<script src="script.js?v=carte-type-3"></script>
</body>
</html>
