<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$commandeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$utilisateur = null;
$commande = null;
$utilisateurs = lire_json('utilisateurs.json');
$plats = lire_json('plats.json');

foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

foreach (lire_json('commandes.json') as $uneCommande) {
    if ((int) $uneCommande['id'] === $commandeId) {
        $commande = $uneCommande;
        break;
    }
}

if ($utilisateur === null || $commande === null) {
    header('Location: profil.php');
    exit();
}

$autorise = false;

if ($utilisateur['role'] === 'admin' || $utilisateur['role'] === 'restaurateur') {
    $autorise = true;
}

if ($utilisateur['role'] === 'client' && (int) $commande['client_id'] === (int) $utilisateur['id']) {
    $autorise = true;
}

if ($utilisateur['role'] === 'livreur' && (int) $commande['livreur_id'] === (int) $utilisateur['id']) {
    $autorise = true;
}

if (!$autorise) {
    header('Location: profil.php');
    exit();
}

$modifiableClient = $utilisateur['role'] === 'client' && in_array($commande['statut_commande'], ['a_preparer', 'en_attente'], true);
$titre_page = 'Detail commande';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Commande #<?php echo (int) $commande['id']; ?></h2>

    <?php if (isset($_GET['modif']) && $_GET['modif'] === 'ok') : ?>
        <p class="succes">La commande a bien ete modifiee apres paiement CYBank.</p>
    <?php endif; ?>

    <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'paiement_refuse') : ?>
        <p class="alerte">Le paiement CYBank a ete refuse. La commande n a pas ete modifiee.</p>
    <?php endif; ?>

    <div class="deux_colonnes">
        <div class="encadre">
            <p><strong>Client :</strong> <?php echo h($commande['client_nom']); ?></p>
            <p><strong>Adresse :</strong> <?php echo h($commande['adresse']); ?></p>
            <p><strong>Telephone :</strong> <?php echo h(isset($commande['telephone']) ? $commande['telephone'] : 'Non renseigne'); ?></p>
            <p><strong>Interphone :</strong> <?php echo h(isset($commande['interphone']) && $commande['interphone'] !== '' ? $commande['interphone'] : 'Non renseigne'); ?></p>
            <p><strong>Etage :</strong> <?php echo h(isset($commande['etage']) && $commande['etage'] !== '' ? $commande['etage'] : 'Non renseigne'); ?></p>
            <p><strong>Commentaire livraison :</strong> <?php echo h(isset($commande['commentaire_livraison']) && $commande['commentaire_livraison'] !== '' ? $commande['commentaire_livraison'] : 'Aucun'); ?></p>
            <p><strong>Mode :</strong> <?php echo h($commande['mode_retrait']); ?></p>
            <p><strong>Creneau :</strong> <?php echo h($commande['creneau']); ?></p>
        </div>
        <div class="encadre">
            <p><strong>Statut :</strong> <?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></p>
            <p><strong>Paiement :</strong> <?php echo h($commande['statut_paiement']); ?></p>
            <p><strong>Livreur :</strong> <?php echo $commande['livreur_nom'] !== '' ? h($commande['livreur_nom']) : 'Non attribue'; ?></p>
            <p><strong>Total :</strong> <span id="commande_total_affiche"><?php echo number_format($commande['total'], 2, ',', ' '); ?></span> EUR</p>
            <?php if (isset($commande['code_promo']) && $commande['code_promo'] !== '') : ?>
                <p><strong>Code promo :</strong> <?php echo h($commande['code_promo']); ?> (<?php echo (int) $commande['pourcentage_promo']; ?> %)</p>
                <p><strong>Reduction :</strong> -<?php echo number_format((float) $commande['montant_remise'], 2, ',', ' '); ?> EUR</p>
                <p><strong>Total avant remise :</strong> <?php echo number_format((float) $commande['total_avant_remise'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="bloc_page">
    <h2>Lignes de commande</h2>

    <table class="tableau">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantite</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commande['lignes'] as $ligne) : ?>
                <tr>
                    <td><?php echo h($ligne['nom']); ?></td>
                    <td><?php echo number_format($ligne['prix'], 2, ',', ' '); ?> EUR</td>
                    <td><?php echo (int) $ligne['quantite']; ?></td>
                    <td><?php echo number_format($ligne['sous_total'], 2, ',', ' '); ?> EUR</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php if ($modifiableClient) : ?>
    <section class="bloc_page">
        <h2>Modifier la commande</h2>

        <form id="form_modif_commande_client" class="formulaire js-validate-form" novalidate>
            <input type="hidden" name="commande_id" value="<?php echo (int) $commande['id']; ?>">

            <?php foreach ($commande['lignes'] as $index => $ligne) : ?>
                <div class="ligne_commande_edit">
                    <label><?php echo h($ligne['nom']); ?> (<?php echo number_format($ligne['prix'], 2, ',', ' '); ?> EUR)</label>
                    <input type="hidden" name="noms[]" value="<?php echo h($ligne['nom']); ?>">
                    <input type="hidden" class="prix_ligne" value="<?php echo h($ligne['prix']); ?>">
                    <input type="number" name="quantites[]" value="<?php echo (int) $ligne['quantite']; ?>" min="0" max="20" class="js-qte-commande" data-rule="quantite" required>
                    <small class="erreur_champ"></small>
                </div>
            <?php endforeach; ?>

            <div>
                <label for="ajout_nom">Ajouter un produit</label>
                <select name="ajout_nom" id="ajout_nom">
                    <option value="">Aucun</option>
                    <?php foreach ($plats as $plat) : ?>
                        <option value="<?php echo h($plat['nom']); ?>" data-prix="<?php echo h($plat['prix']); ?>">
                            <?php echo h($plat['nom']); ?> - <?php echo number_format($plat['prix'], 2, ',', ' '); ?> EUR
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="ajout_quantite">Quantite a ajouter</label>
                <input type="number" name="ajout_quantite" id="ajout_quantite" value="1" min="1" max="20" data-rule="quantite">
                <small class="erreur_champ"></small>
            </div>

            <div class="encadre">
                <p><strong>Nouveau total :</strong> <span id="nouveau_total"><?php echo number_format($commande['total'], 2, ',', ' '); ?></span> EUR</p>
                <p><strong>Difference :</strong> <span id="difference_total">0,00</span> EUR</p>
            </div>

            <div id="bloc_paiement_complement" class="cache encadre">
                <h3>Paiement complementaire</h3>
                <p>Si la commande coute plus cher, vous serez redirige vers CYBank pour payer la difference.</p>
            </div>

            <button type="submit">Enregistrer la modification</button>
            <p id="message_modif_commande" class="zone_message" aria-live="polite"></p>
        </form>
    </section>
<?php endif; ?>

<?php if ($utilisateur['role'] === 'client' && $commande['statut_commande'] === 'livree' && $commande['mode_retrait'] !== 'a_emporter') : ?>
    <?php
    $dejaNotee = false;
    foreach (lire_json('notations.json') as $notation) {
        if ((int) $notation['commande_id'] === (int) $commande['id'] && (int) $notation['client_id'] === (int) $utilisateur['id']) {
            $dejaNotee = true;
            break;
        }
    }
    ?>
    <?php if (!$dejaNotee) : ?>
        <section class="bloc_page">
            <a class="bouton_action" href="notation.php?id=<?php echo (int) $commande['id']; ?>">Noter cette commande</a>
        </section>
    <?php endif; ?>
<?php endif; ?>

<script>
window.commandeCourante = <?php echo json_encode($commande, JSON_UNESCAPED_UNICODE); ?>;
</script>
</main>
<script src="script.js"></script>
</body>
</html>
