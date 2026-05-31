<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$commandeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$commande = null;
$utilisateur = null;

$utilisateurs = lire_json('utilisateurs.json');
foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

$commandes = lire_json('commandes.json');
foreach ($commandes as $uneCommande) {
    if ((int) $uneCommande['id'] === $commandeId) {
        $commande = $uneCommande;
        break;
    }
}

if ($commande === null || $utilisateur === null) {
    header('Location: profil.php');
    exit();
}

$aLeDroit = false;

if ($utilisateur['role'] === 'admin' || $utilisateur['role'] === 'restaurateur') {
    $aLeDroit = true;
}

if ($utilisateur['role'] === 'client' && (int) $commande['client_id'] === (int) $utilisateur['id']) {
    $aLeDroit = true;
}

if ($utilisateur['role'] === 'livreur' && (int) $commande['livreur_id'] === (int) $utilisateur['id']) {
    $aLeDroit = true;
}

if (!$aLeDroit) {
    header('Location: profil.php');
    exit();
}

$titre_page = 'Detail commande';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Commande #<?php echo (int) $commande['id']; ?></h2>

    <div class="deux_colonnes">
        <div class="encadre">
            <p><strong>Client :</strong> <?php echo h($commande['client_nom']); ?></p>
            <p><strong>Telephone :</strong> <?php echo h($commande['telephone']); ?></p>
            <p><strong>Adresse :</strong> <?php echo h($commande['adresse']); ?></p>
            <p><strong>Mode :</strong> <?php echo h($commande['mode_retrait']); ?></p>
            <p><strong>Creneau :</strong> <?php echo h($commande['creneau']); ?></p>
        </div>

        <div class="encadre">
            <p><strong>Statut commande :</strong> <?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></p>
            <p><strong>Statut paiement :</strong> <?php echo h($commande['statut_paiement']); ?></p>
            <p><strong>Livreur :</strong> <?php echo $commande['livreur_nom'] !== '' ? h($commande['livreur_nom']) : 'Non attribue'; ?></p>
            <p><strong>Total :</strong> <?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</p>
            <p><strong>Date :</strong> <?php echo h($commande['date_commande']); ?></p>
        </div>
    </div>
</section>

<section class="bloc_page">
    <h2>Produits commandes</h2>

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

<?php if ($utilisateur['role'] === 'restaurateur') : ?>
    <section class="bloc_page">
        <h2>Gestion restaurateur</h2>

        <form class="formulaire">
            <div>
                <label for="statut_commande">Changer le statut</label>
                <select id="statut_commande" disabled>
                    <option <?php echo $commande['statut_commande'] === 'a_preparer' ? 'selected' : ''; ?>>A preparer</option>
                    <option <?php echo $commande['statut_commande'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option <?php echo $commande['statut_commande'] === 'en_preparation' ? 'selected' : ''; ?>>En preparation</option>
                    <option <?php echo $commande['statut_commande'] === 'en_livraison' ? 'selected' : ''; ?>>En livraison</option>
                    <option <?php echo $commande['statut_commande'] === 'livree' ? 'selected' : ''; ?>>Livree</option>
                    <option <?php echo $commande['statut_commande'] === 'abandonnee' ? 'selected' : ''; ?>>Abandonnee</option>
                </select>
            </div>

            <div>
                <label for="livreur">Attribuer un livreur</label>
                <select id="livreur" disabled>
                    <option><?php echo $commande['livreur_nom'] !== '' ? h($commande['livreur_nom']) : 'Choisir un livreur'; ?></option>
                    <?php foreach ($utilisateurs as $livreur) : ?>
                        <?php if ($livreur['role'] === 'livreur' && !empty($livreur['disponible'])) : ?>
                            <option><?php echo h($livreur['prenom'] . ' ' . $livreur['nom']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="button" disabled>Enregistrer</button>
        </form>
    </section>
<?php endif; ?>

<?php if ($utilisateur['role'] === 'livreur') : ?>
    <section class="bloc_page">
        <h2>Actions livreur</h2>

        <?php if ($commande['statut_commande'] === 'en_livraison') : ?>
            <div class="ligne_action">
                <form action="traitements/process_livraison.php" method="POST">
                    <input type="hidden" name="commande_id" value="<?php echo (int) $commande['id']; ?>">
                    <input type="hidden" name="action" value="livree">
                    <button type="submit">Marquer comme livree</button>
                </form>

                <form action="traitements/process_livraison.php" method="POST">
                    <input type="hidden" name="commande_id" value="<?php echo (int) $commande['id']; ?>">
                    <input type="hidden" name="action" value="abandonnee">
                    <button type="submit" class="bouton_danger">Adresse introuvable / abandon</button>
                </form>
            </div>
        <?php else : ?>
            <p class="info">Cette commande n'est plus modifiable par le livreur.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php
$dejaNotee = false;

foreach (lire_json('notations.json') as $notation) {
    if ((int) $notation['commande_id'] === (int) $commande['id'] && (int) $notation['client_id'] === (int) $utilisateur['id']) {
        $dejaNotee = true;
        break;
    }
}
?>
<?php if ($utilisateur['role'] === 'client' && $commande['statut_commande'] === 'livree' && !$dejaNotee) : ?>
    <section class="bloc_page">
        <p><a class="bouton_action" href="notation.php?id=<?php echo (int) $commande['id']; ?>">Noter cette commande</a></p>
    </section>
<?php endif; ?>

</main>
<script src="script.js"></script>
</body>
</html>
