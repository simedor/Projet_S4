<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$restaurateur = null;
$utilisateurs = lire_json('utilisateurs.json');

foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $restaurateur = $unUtilisateur;
        break;
    }
}

if ($restaurateur === null || $restaurateur['role'] !== 'restaurateur') {
    header('Location: accueil.php');
    exit();
}

$groupes = [
    'a_preparer' => [],
    'en_attente' => [],
    'en_preparation' => [],
    'en_livraison' => [],
    'livree' => [],
    'abandonnee' => []
];

foreach (lire_json('commandes.json') as $commande) {
    $statut = $commande['statut_commande'];

    if (!isset($groupes[$statut])) {
        $groupes[$statut] = [];
    }

    $groupes[$statut][] = $commande;
}

$titre_page = 'Commandes restaurant';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Liste detaillee des commandes</h2>
    <p class="info">Le detail des commandes est disponible ci-dessous.</p>
</section>

<?php foreach ($groupes as $statut => $liste) : ?>
    <section class="bloc_page">
        <h2><?php echo h(ucfirst(str_replace('_', ' ', $statut))); ?> (<?php echo count($liste); ?>)</h2>

        <?php if (empty($liste)) : ?>
            <p class="info">Aucune commande dans cette categorie.</p>
        <?php else : ?>
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Numero</th>
                        <th>Client</th>
                        <th>Produits</th>
                        <th>Mode</th>
                        <th>Creneau</th>
                        <th>Livreur</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($liste as $commande) : ?>
                        <tr>
                            <td>#<?php echo (int) $commande['id']; ?></td>
                            <td><?php echo h($commande['client_nom']); ?></td>
                            <td><?php echo h($commande['produit']); ?></td>
                            <td><?php echo h($commande['mode_retrait']); ?></td>
                            <td><?php echo h($commande['creneau']); ?></td>
                            <td><?php echo $commande['livreur_nom'] !== '' ? h($commande['livreur_nom']) : 'Non attribue'; ?></td>
                            <td><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

</main>
<script src="script.js"></script>
</body>
</html>
