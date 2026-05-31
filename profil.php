<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;
$utilisateurs = lire_json('utilisateurs.json');

foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

if ($utilisateur === null) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$titre_page = 'Profil';
$commandesClient = [];
$commandesActives = [];

if ($utilisateur['role'] === 'client') {
    $toutesLesCommandes = lire_json('commandes.json');

    foreach ($toutesLesCommandes as $commande) {
        if ((int) $commande['client_id'] === (int) $utilisateur['id']) {
            $commandesClient[] = $commande;

            if ($commande['statut_commande'] !== 'livree' && $commande['statut_commande'] !== 'abandonnee') {
                $commandesActives[] = $commande;
            }
        }
    }

    usort($commandesClient, function ($a, $b) {
        return strcmp($b['date_commande'], $a['date_commande']);
    });

    usort($commandesActives, function ($a, $b) {
        return strcmp($b['date_commande'], $a['date_commande']);
    });
}

include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Mon profil</h2>

    <?php if (isset($_GET['commande']) && $_GET['commande'] === 'ok') : ?>
        <p class="succes">Votre commande a bien ete enregistree.</p>
    <?php endif; ?>

    <?php if (isset($_GET['notation']) && $_GET['notation'] === 'ok') : ?>
        <p class="succes">Votre note a bien ete enregistree.</p>
    <?php endif; ?>

    <div class="deux_colonnes">
        <div class="encadre">
            <h3>Informations</h3>
            <p><strong>Nom :</strong> <?php echo h($utilisateur['nom']); ?></p>
            <p><strong>Prenom :</strong> <?php echo h($utilisateur['prenom']); ?></p>
            <p><strong>Login :</strong> <?php echo h($utilisateur['login']); ?></p>
            <p><strong>Role :</strong> <?php echo h($utilisateur['role']); ?></p>
            <p><strong>Email :</strong> <?php echo h($utilisateur['email']); ?></p>
            <p><strong>Adresse :</strong> <?php echo h($utilisateur['adresse']); ?></p>
            <p><strong>Telephone :</strong> <?php echo h($utilisateur['telephone']); ?></p>
            <p><strong>Fidelite :</strong> <?php echo h($utilisateur['fidelite']); ?></p>
            <p><strong>Remise affichee :</strong> <?php echo (int) $utilisateur['remise']; ?>%</p>
        </div>

        <div class="encadre">
            <h3>Acces rapide</h3>
            <?php if ($utilisateur['role'] === 'client') : ?>
                <p>Les informations du profil sont visibles ici.</p>
            <?php elseif ($utilisateur['role'] === 'admin') : ?>
                <p><a href="administrateur.php">Acceder a la gestion des utilisateurs</a></p>
            <?php elseif ($utilisateur['role'] === 'restaurateur') : ?>
                <p><a href="commande.php">Acceder au suivi des commandes</a></p>
            <?php elseif ($utilisateur['role'] === 'livreur') : ?>
                <p><a href="livraison.php">Acceder a vos livraisons</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($utilisateur['role'] === 'client') : ?>
    <section class="bloc_page">
        <h2>Commande en cours</h2>

        <?php if (empty($commandesActives)) : ?>
            <p class="info">Aucune commande en cours pour le moment.</p>
        <?php else : ?>
            <div class="grille_cartes">
                <?php foreach ($commandesActives as $commande) : ?>
                    <article class="carte_resume">
                        <h3>Commande #<?php echo (int) $commande['id']; ?></h3>
                        <p><strong>Statut :</strong> <span class="badge"><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></span></p>
                        <p><strong>Total :</strong> <?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</p>
                        <p><strong>Creneau :</strong> <?php echo h($commande['creneau']); ?></p>
                        <p><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir le detail</a></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="bloc_page">
        <h2>Historique des commandes</h2>

        <?php if (empty($commandesClient)) : ?>
            <p class="info">Vous n'avez pas encore de commande.</p>
        <?php else : ?>
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Numero</th>
                        <th>Date</th>
                        <th>Produits</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandesClient as $commande) : ?>
                        <tr>
                            <td>#<?php echo (int) $commande['id']; ?></td>
                            <td><?php echo h($commande['date_commande']); ?></td>
                            <td><?php echo h($commande['produit']); ?></td>
                            <td><?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</td>
                            <td><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></td>
                            <td>
                                <a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Detail</a>
                                <?php
                                $dejaNotee = false;
                                $notations = lire_json('notations.json');

                                foreach ($notations as $notation) {
                                    if ((int) $notation['commande_id'] === (int) $commande['id'] && (int) $notation['client_id'] === (int) $utilisateur['id']) {
                                        $dejaNotee = true;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($commande['statut_commande'] === 'livree' && !$dejaNotee) : ?>
                                    | <a href="notation.php?id=<?php echo (int) $commande['id']; ?>">Noter</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
<?php endif; ?>

</main>
<script src="script.js"></script>
</body>
</html>
