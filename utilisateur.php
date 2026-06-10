<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$adminConnecte = null;
$listeUtilisateurs = lire_json('utilisateurs.json');

foreach ($listeUtilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $adminConnecte = $unUtilisateur;
        break;
    }
}

if ($adminConnecte === null || $adminConnecte['role'] !== 'admin') {
    header('Location: accueil.php');
    exit();
}

$utilisateurId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$ficheUtilisateur = null;

foreach ($listeUtilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === $utilisateurId) {
        $ficheUtilisateur = $unUtilisateur;
        break;
    }
}

if ($ficheUtilisateur === null) {
    header('Location: administrateur.php');
    exit();
}

$historique = [];
$listeCommandes = lire_json('commandes.json');
$messageAdmin = '';
$fideliteUtilisateur = isset($ficheUtilisateur['fidelite']) ? $ficheUtilisateur['fidelite'] : 'Standard';
$remiseUtilisateur = remise_fidelite($fideliteUtilisateur);

foreach ($listeCommandes as $commande) {
    if ((int) $commande['client_id'] === $utilisateurId) {
        $historique[] = $commande;
    }
}

if (isset($_GET['admin'])) {
    if ($_GET['admin'] === 'statut') {
        $messageAdmin = 'Le statut du compte a ete mis a jour.';
    } elseif ($_GET['admin'] === 'fidelite') {
        $messageAdmin = 'La fidelite a ete mise a jour.';
    }
}

$titre_page = 'Profil utilisateur';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Profil utilisateur</h2>

    <div class="deux_colonnes">
        <div class="encadre">
            <p><strong>Nom :</strong> <?php echo h($ficheUtilisateur['nom']); ?></p>
            <p><strong>Prenom :</strong> <?php echo h($ficheUtilisateur['prenom']); ?></p>
            <p><strong>Login :</strong> <?php echo h($ficheUtilisateur['login']); ?></p>
            <p><strong>Role :</strong> <?php echo h($ficheUtilisateur['role']); ?></p>
            <p><strong>Email :</strong> <?php echo h($ficheUtilisateur['email']); ?></p>
            <p><strong>Adresse :</strong> <?php echo h($ficheUtilisateur['adresse']); ?></p>
            <p><strong>Telephone :</strong> <?php echo h($ficheUtilisateur['telephone']); ?></p>
            <p><strong>Derniere connexion :</strong> <?php echo h($ficheUtilisateur['derniere_connexion']); ?></p>
        </div>

        <div class="encadre">
            <h3>Actions admin</h3>

            <?php if ($messageAdmin !== '') : ?>
                <p class="succes"><?php echo h($messageAdmin); ?></p>
            <?php endif; ?>

            <form action="traitements/process_utilisateur_admin.php" method="POST" class="formulaire">
                <input type="hidden" name="user_id" value="<?php echo (int) $ficheUtilisateur['id']; ?>">

                <div class="ligne_action">
                    <?php if ($ficheUtilisateur['id'] !== $adminConnecte['id']) : ?>
                        <?php if ($ficheUtilisateur['statut_compte'] === 'bloque') : ?>
                            <button type="submit" name="action_admin" value="debloquer">Debloquer</button>
                        <?php else : ?>
                            <button type="submit" name="action_admin" value="bloquer">Bloquer</button>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="info">Impossible de bloquer son propre compte.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="statut_compte">Etat du compte</label>
                    <input type="text" id="statut_compte" value="<?php echo h($ficheUtilisateur['statut_compte']); ?>" disabled>
                </div>
            </form>

            <form action="traitements/process_utilisateur_admin.php" method="POST" class="formulaire">
                <input type="hidden" name="user_id" value="<?php echo (int) $ficheUtilisateur['id']; ?>">
                <input type="hidden" name="action_admin" value="fidelite">

                <div>
                    <label for="fidelite">Statut fidelite</label>
                    <select id="fidelite" name="fidelite">
                        <option value="Standard" <?php echo $fideliteUtilisateur === 'Standard' ? 'selected' : ''; ?>>Standard</option>
                        <option value="Premium" <?php echo $fideliteUtilisateur === 'Premium' ? 'selected' : ''; ?>>Premium</option>
                        <option value="VIP" <?php echo $fideliteUtilisateur === 'VIP' ? 'selected' : ''; ?>>VIP</option>
                    </select>
                </div>

                <div>
                    <label for="remise">Niveau de remise</label>
                    <input type="number" id="remise" value="<?php echo $remiseUtilisateur; ?>" disabled>
                </div>

                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </div>
</section>

<?php if ($ficheUtilisateur['role'] === 'client') : ?>
    <section class="bloc_page">
        <h2>Historique client</h2>

        <?php if (empty($historique)) : ?>
            <p class="info">Ce client n'a pas encore commande.</p>
        <?php else : ?>
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $commande) : ?>
                        <tr>
                            <td>#<?php echo (int) $commande['id']; ?></td>
                            <td><?php echo h($commande['date_commande']); ?></td>
                            <td><?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</td>
                            <td><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></td>
                            <td><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir</a></td>
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
