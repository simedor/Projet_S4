<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$restaurateur = null;

foreach (lire_json('utilisateurs.json') as $utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $restaurateur = $utilisateur;
        break;
    }
}

if ($restaurateur === null || $restaurateur['role'] !== 'restaurateur') {
    header('Location: accueil.php');
    exit();
}

$nomEdition = isset($_GET['edit']) ? trim($_GET['edit']) : '';
$platEnEdition = null;

foreach (lire_json('plats.json') as $plat) {
    if ($plat['nom'] === $nomEdition) {
        $platEnEdition = $plat;
        break;
    }
}

if ($platEnEdition === null) {
    header('Location: commande.php');
    exit();
}

$titre_page = 'Modifier un plat';
include 'Includes/header.php';
?>

<section class="bloc_page bloc_formulaire">
    <h2>Modifier un plat</h2>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'image_absente') : ?>
        <p class="erreur">Choisis une image.</p>
    <?php endif; ?>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'image_invalide') : ?>
        <p class="erreur">Le format d'image n'est pas accepte.</p>
    <?php endif; ?>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'image_upload') : ?>
        <p class="erreur">L'image n'a pas pu etre envoyee.</p>
    <?php endif; ?>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'incomplet') : ?>
        <p class="erreur">Remplis tous les champs du plat.</p>
    <?php endif; ?>

    <form action="traitements/process_plat.php" method="POST" class="formulaire" enctype="multipart/form-data">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="nom_original" value="<?php echo h($platEnEdition['nom']); ?>">

        <div>
            <label for="plat_nom">Nom</label>
            <input type="text" name="nom" id="plat_nom" maxlength="40" value="<?php echo h($platEnEdition['nom']); ?>" required>
        </div>

        <div>
            <label for="plat_description">Description</label>
            <textarea name="description" id="plat_description" rows="3" required><?php echo h($platEnEdition['description']); ?></textarea>
        </div>

        <div>
            <label for="plat_prix">Prix</label>
            <input type="number" step="0.5" min="1" name="prix" id="plat_prix" value="<?php echo h($platEnEdition['prix']); ?>" required>
        </div>

        <div>
            <label for="plat_image_fichier">Changer l'image</label>
            <input type="file" name="image_fichier" id="plat_image_fichier" accept=".png,.jpg,.jpeg,.webp">
        </div>

        <div>
            <label for="plat_type">Type</label>
            <select name="type" id="plat_type">
                <option value="pizza" <?php echo $platEnEdition['type'] === 'pizza' ? 'selected' : ''; ?>>Pizza</option>
                <option value="menu" <?php echo $platEnEdition['type'] === 'menu' ? 'selected' : ''; ?>>Menu</option>
                <option value="boisson" <?php echo $platEnEdition['type'] === 'boisson' ? 'selected' : ''; ?>>Boisson</option>
                <option value="accompagnement" <?php echo $platEnEdition['type'] === 'accompagnement' ? 'selected' : ''; ?>>Accompagnement</option>
            </select>
        </div>

        <div class="ligne_radio">
            <label><input type="checkbox" name="plat_du_jour" value="1" <?php echo !empty($platEnEdition['plat_du_jour']) ? 'checked' : ''; ?>> Plat du jour</label>
            <label><input type="checkbox" name="best_seller" value="1" <?php echo !empty($platEnEdition['best_seller']) ? 'checked' : ''; ?>> Best seller</label>
        </div>

        <div class="ligne_action">
            <button type="submit">Enregistrer les changements</button>
            <a class="bouton_secondaire" href="commande.php">Retour</a>
        </div>
    </form>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
