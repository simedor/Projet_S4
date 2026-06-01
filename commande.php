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

$titre_page = 'Commandes restaurant';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Gestion des commandes</h2>

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
                        <select class="select_livreur" data-commande-id="<?php echo (int) $commande['id']; ?>">
                            <option value="">Choisir</option>
                            <?php foreach ($livreurs as $livreur) : ?>
                                <option value="<?php echo (int) $livreur['id']; ?>" <?php echo (int) $commande['livreur_id'] === (int) $livreur['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($livreur['prenom'] . ' ' . $livreur['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="cell_actions">
                        <?php if ($commande['statut_commande'] === 'a_preparer') : ?>
                            <button type="button" class="btn-commande-resto" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="preparer">Passer en preparation</button>
                        <?php elseif ($commande['statut_commande'] === 'en_preparation') : ?>
                            <button type="button" class="btn-commande-resto" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="prete">Marquer prete</button>
                        <?php elseif ($commande['statut_commande'] === 'prete') : ?>
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

    <p id="message_commande_resto"></p>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
