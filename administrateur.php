<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$admin = null;
$utilisateurs = lire_json('utilisateurs.json');
$commandes = lire_json('commandes.json');

foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $admin = $unUtilisateur;
        break;
    }
}

if ($admin === null || $admin['role'] !== 'admin') {
    header('Location: accueil.php');
    exit();
}

$titre_page = 'Administration';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Gestion des utilisateurs</h2>

    <table class="tableau">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Login</th>
                <th>Role</th>
                <th>Statut</th>
                <th>Commandes</th>
                <th>Action</th>
                <th>Profil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $unUtilisateur) : ?>
                <?php
                $nbCommandes = 0;
                foreach ($commandes as $commande) {
                    if ((int) $commande['client_id'] === (int) $unUtilisateur['id']) {
                        $nbCommandes++;
                    }
                }
                ?>
                <tr id="ligne_user_<?php echo (int) $unUtilisateur['id']; ?>">
                    <td><?php echo h($unUtilisateur['prenom'] . ' ' . $unUtilisateur['nom']); ?></td>
                    <td><?php echo h($unUtilisateur['login']); ?></td>
                    <td><?php echo h($unUtilisateur['role']); ?></td>
                    <td class="statut_user"><?php echo h($unUtilisateur['statut_compte']); ?></td>
                    <td><?php echo $nbCommandes; ?></td>
                    <td>
                        <?php if ($unUtilisateur['id'] !== $admin['id']) : ?>
                            <button type="button" class="btn-admin-user" data-user-id="<?php echo (int) $unUtilisateur['id']; ?>" data-action="<?php echo $unUtilisateur['statut_compte'] === 'bloque' ? 'debloquer' : 'bloquer'; ?>">
                                <?php echo $unUtilisateur['statut_compte'] === 'bloque' ? 'Debloquer' : 'Bloquer'; ?>
                            </button>
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><a href="utilisateur.php?id=<?php echo (int) $unUtilisateur['id']; ?>">Voir</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p id="message_admin"></p>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
