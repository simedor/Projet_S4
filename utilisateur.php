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

foreach ($listeCommandes as $commande) {
    if ((int) $commande['client_id'] === $utilisateurId) {
        $historique[] = $commande;
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

            <form class="formulaire">
                <div class="ligne_action">
                    <button type="button" disabled>Bloquer</button>
                    <button type="button" disabled class="bouton_secondaire">Desactiver</button>
                </div>

                <div>
                    <label for="statut_compte">Etat du compte</label>
                    <select id="statut_compte" disabled>
                        <option <?php echo $ficheUtilisateur['statut_compte'] === 'actif' ? 'selected' : ''; ?>>Actif</option>
                        <option <?php echo $ficheUtilisateur['statut_compte'] === 'bloque' ? 'selected' : ''; ?>>Bloque</option>
                        <option <?php echo $ficheUtilisateur['statut_compte'] === 'desactive' ? 'selected' : ''; ?>>Desactive</option>
                    </select>
                </div>

                <div>
                    <label for="fidelite">Statut fidelite</label>
                    <select id="fidelite" disabled>
                        <option <?php echo $ficheUtilisateur['fidelite'] === 'Standard' ? 'selected' : ''; ?>>Standard</option>
                        <option <?php echo $ficheUtilisateur['fidelite'] === 'Premium' ? 'selected' : ''; ?>>Premium</option>
                        <option <?php echo $ficheUtilisateur['fidelite'] === 'VIP' ? 'selected' : ''; ?>>VIP</option>
                    </select>
                </div>

                <div>
                    <label for="remise">Niveau de remise</label>
                    <input type="number" id="remise" value="<?php echo (int) $ficheUtilisateur['remise']; ?>" disabled>
                </div>

                <button type="button" disabled>Enregistrer</button>
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
