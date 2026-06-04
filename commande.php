<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$restaurateur = null;
$utilisateurs = lire_json('utilisateurs.json');
$livreurs = [];
$commandes = lire_json('commandes.json');
$plats = lire_json('plats.json');
$platEnEdition = null;
$statsPlats = [];
$statsDuos = [];

foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $restaurateur = $unUtilisateur;
    }

    if ($unUtilisateur['role'] === 'livreur') {
        $livreurs[] = $unUtilisateur;
    }
}

if ($restaurateur === null || $restaurateur['role'] !== 'restaurateur') {
    header('Location: accueil.php');
    exit();
}

if (isset($_GET['edit'])) {
    $nomEdition = trim($_GET['edit']);

    foreach ($plats as $plat) {
        if ($plat['nom'] === $nomEdition) {
            $platEnEdition = $plat;
            break;
        }
    }
}

foreach ($commandes as $commande) {
    if (!isset($commande['lignes']) || !is_array($commande['lignes'])) {
        continue;
    }

    $nomsCommande = [];

    foreach ($commande['lignes'] as $ligne) {
        $nomPlat = $ligne['nom'];
        $quantite = isset($ligne['quantite']) ? (int) $ligne['quantite'] : 0;

        if (!isset($statsPlats[$nomPlat])) {
            $statsPlats[$nomPlat] = 0;
        }

        $statsPlats[$nomPlat] += $quantite;
        $nomsCommande[] = $nomPlat;
    }

    $nomsCommande = array_values(array_unique($nomsCommande));
    sort($nomsCommande);

    for ($i = 0; $i < count($nomsCommande); $i++) {
        for ($j = $i + 1; $j < count($nomsCommande); $j++) {
            $cle = $nomsCommande[$i] . ' + ' . $nomsCommande[$j];

            if (!isset($statsDuos[$cle])) {
                $statsDuos[$cle] = 0;
            }

            $statsDuos[$cle]++;
        }
    }
}

arsort($statsPlats);
arsort($statsDuos);

$statsPlats = array_slice($statsPlats, 0, 5, true);
$statsDuos = array_slice($statsDuos, 0, 5, true);

$titre_page = 'Commandes restaurant';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Gestion des commandes</h2>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'ajoute') : ?>
        <p class="succes">Le plat a bien ete ajoute.</p>
    <?php endif; ?>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'modifie') : ?>
        <p class="succes">Le plat a bien ete modifie.</p>
    <?php endif; ?>

    <?php if (isset($_GET['plat']) && $_GET['plat'] === 'supprime') : ?>
        <p class="succes">Le plat a bien ete supprime.</p>
    <?php endif; ?>

    <table class="tableau">
        <thead>
            <tr>
                <th>Numero</th>
                <th>Client</th>
                <th>Produits</th>
                <th>Statut</th>
                <th>Livreur</th>
                <th>Action</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $commande) : ?>
                <tr id="ligne_commande_<?php echo (int) $commande['id']; ?>">
                    <td>#<?php echo (int) $commande['id']; ?></td>
                    <td><?php echo h($commande['client_nom']); ?></td>
                    <td><?php echo h($commande['produit']); ?></td>
                    <td class="statut_commande"><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></td>
                    <td>
                        <?php if ($commande['mode_retrait'] === 'livraison') : ?>
                            <select class="select_livreur" data-commande-id="<?php echo (int) $commande['id']; ?>">
                                <option value="">Choisir</option>
                                <?php foreach ($livreurs as $livreur) : ?>
                                    <option value="<?php echo (int) $livreur['id']; ?>" <?php echo (int) $commande['livreur_id'] === (int) $livreur['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($livreur['prenom'] . ' ' . $livreur['nom']); ?><?php echo !empty($livreur['disponible']) ? '' : ' (occupe)'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="cell_actions">
                        <?php if ($commande['statut_commande'] === 'a_preparer') : ?>
                            <button type="button" class="btn-commande-resto" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="preparer">Passer en preparation</button>
                        <?php elseif ($commande['statut_commande'] === 'en_preparation') : ?>
                            <button type="button" class="btn-commande-resto" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="prete">Marquer prete</button>
                        <?php elseif ($commande['statut_commande'] === 'prete' && $commande['mode_retrait'] === 'livraison') : ?>
                            <button type="button" class="btn-commande-resto" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="assigner">Assigner au livreur</button>
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p id="message_commande_resto" class="zone_message" aria-live="polite"></p>
</section>

<section class="bloc_page">
    <h2>Statistiques rapides</h2>

    <div class="deux_colonnes">
        <div class="encadre">
            <h3>Plats les plus commandes</h3>
            <?php if (empty($statsPlats)) : ?>
                <p class="info">Pas assez de commandes pour faire des stats.</p>
            <?php else : ?>
                <ul class="petite_liste">
                    <?php foreach ($statsPlats as $nomPlat => $quantite) : ?>
                        <li><?php echo h($nomPlat); ?> : <?php echo (int) $quantite; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="encadre">
            <h3>Produits souvent achetes ensemble</h3>
            <?php if (empty($statsDuos)) : ?>
                <p class="info">Pas encore assez de commandes mixtes.</p>
            <?php else : ?>
                <ul class="petite_liste">
                    <?php foreach ($statsDuos as $duo => $nombre) : ?>
                        <li><?php echo h($duo); ?> : <?php echo (int) $nombre; ?> commande(s)</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="bloc_page">
    <h2><?php echo $platEnEdition !== null ? 'Modifier un plat' : 'Ajouter un plat'; ?></h2>

    <form action="traitements/process_plat.php" method="POST" class="formulaire">
        <input type="hidden" name="action" value="<?php echo $platEnEdition !== null ? 'modifier' : 'ajouter'; ?>">
        <input type="hidden" name="nom_original" value="<?php echo $platEnEdition !== null ? h($platEnEdition['nom']) : ''; ?>">

        <div>
            <label for="plat_nom">Nom</label>
            <input type="text" name="nom" id="plat_nom" maxlength="40" value="<?php echo $platEnEdition !== null ? h($platEnEdition['nom']) : ''; ?>" required>
        </div>

        <div>
            <label for="plat_description">Description</label>
            <textarea name="description" id="plat_description" rows="3" required><?php echo $platEnEdition !== null ? h($platEnEdition['description']) : ''; ?></textarea>
        </div>

        <div>
            <label for="plat_prix">Prix</label>
            <input type="number" step="0.5" min="1" name="prix" id="plat_prix" value="<?php echo $platEnEdition !== null ? h($platEnEdition['prix']) : ''; ?>" required>
        </div>

        <div>
            <label for="plat_image">Image</label>
            <input type="text" name="image" id="plat_image" value="<?php echo $platEnEdition !== null ? h($platEnEdition['image']) : 'Images/Reine.png'; ?>" required>
        </div>

        <div>
            <label for="plat_categorie">Categorie</label>
            <input type="text" name="categorie" id="plat_categorie" value="<?php echo $platEnEdition !== null ? h($platEnEdition['categorie']) : ''; ?>" required>
        </div>

        <div>
            <label for="plat_type">Type</label>
            <select name="type" id="plat_type">
                <option value="pizza" <?php echo $platEnEdition !== null && $platEnEdition['type'] === 'pizza' ? 'selected' : ''; ?>>Pizza</option>
                <option value="menu" <?php echo $platEnEdition !== null && $platEnEdition['type'] === 'menu' ? 'selected' : ''; ?>>Menu</option>
            </select>
        </div>

        <div>
            <label for="plat_regime">Regime</label>
            <input type="text" name="regime" id="plat_regime" value="<?php echo $platEnEdition !== null ? h($platEnEdition['regime']) : ''; ?>" required>
        </div>

        <div>
            <label for="plat_gout">Gout</label>
            <input type="text" name="gout" id="plat_gout" value="<?php echo $platEnEdition !== null ? h($platEnEdition['gout']) : ''; ?>" required>
        </div>

        <div class="ligne_radio">
            <label><input type="checkbox" name="plat_du_jour" value="1" <?php echo $platEnEdition !== null && !empty($platEnEdition['plat_du_jour']) ? 'checked' : ''; ?>> Plat du jour</label>
            <label><input type="checkbox" name="best_seller" value="1" <?php echo $platEnEdition !== null && !empty($platEnEdition['best_seller']) ? 'checked' : ''; ?>> Best seller</label>
        </div>

        <div class="ligne_action">
            <button type="submit"><?php echo $platEnEdition !== null ? 'Enregistrer les changements' : 'Ajouter le plat'; ?></button>
            <?php if ($platEnEdition !== null) : ?>
                <a class="bouton_secondaire" href="commande.php">Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="bloc_page">
    <h2>Liste des plats et menus</h2>

    <table class="tableau">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Categorie</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plats as $plat) : ?>
                <tr>
                    <td><?php echo h($plat['nom']); ?></td>
                    <td><?php echo h($plat['type']); ?></td>
                    <td><?php echo number_format($plat['prix'], 2, ',', ' '); ?> EUR</td>
                    <td><?php echo h($plat['categorie']); ?></td>
                    <td>
                        <a href="commande.php?edit=<?php echo urlencode($plat['nom']); ?>">Modifier</a>
                        <form action="traitements/process_plat.php" method="POST" class="ligne_action">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="nom_original" value="<?php echo h($plat['nom']); ?>">
                            <button type="submit" class="bouton_danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
