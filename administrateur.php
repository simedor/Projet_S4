<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$admin = null;
$utilisateurs = lire_json('utilisateurs.json');
$commandes = lire_json('commandes.json');
$incidents = lire_json('incidents.json');
$codesPromo = lire_json('codes_promo.json');
$messagePromo = '';

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

if (isset($_GET['promo'])) {
    if ($_GET['promo'] === 'ajoute') {
        $messagePromo = 'Le code promo a bien ete ajoute.';
    } elseif ($_GET['promo'] === 'supprime') {
        $messagePromo = 'Le code promo a bien ete supprime.';
    } elseif ($_GET['promo'] === 'existe') {
        $messagePromo = 'Ce code promo existe deja.';
    } elseif ($_GET['promo'] === 'invalide') {
        $messagePromo = 'Le code promo est invalide.';
    }
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

    <p id="message_admin" class="zone_message" aria-live="polite"></p>
</section>

<section class="bloc_page">
    <h2>Codes promo</h2>

    <?php if ($messagePromo !== '') : ?>
        <p class="<?php echo isset($_GET['promo']) && in_array($_GET['promo'], ['ajoute', 'supprime'], true) ? 'succes' : 'alerte'; ?>"><?php echo h($messagePromo); ?></p>
    <?php endif; ?>

    <form action="traitements/process_code_promo.php" method="POST" class="formulaire bloc_formulaire">
        <input type="hidden" name="action" value="ajouter">

        <div>
            <label for="promo_code">Code promo</label>
            <input type="text" name="code" id="promo_code" maxlength="20" placeholder="Ex : PIZZA10" required>
        </div>

        <div>
            <label for="promo_reduction">Reduction (%)</label>
            <input type="number" name="reduction" id="promo_reduction" min="1" max="90" required>
        </div>

        <button type="submit">Creer le code promo</button>
    </form>

    <?php if (empty($codesPromo)) : ?>
        <p class="info">Aucun code promo pour le moment.</p>
    <?php else : ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Reduction</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($codesPromo as $promo) : ?>
                    <tr>
                        <td><?php echo h($promo['code']); ?></td>
                        <td><?php echo (int) $promo['reduction']; ?> %</td>
                        <td><?php echo h(isset($promo['date_creation']) ? $promo['date_creation'] : ''); ?></td>
                        <td>
                            <form action="traitements/process_code_promo.php" method="POST">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="code" value="<?php echo h($promo['code']); ?>">
                                <button type="submit" class="bouton_danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<section class="bloc_page">
    <h2>Derniers incidents</h2>

    <?php if (empty($incidents)) : ?>
        <p class="info">Aucun incident enregistre pour le moment.</p>
    <?php else : ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Login</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse(array_slice($incidents, -12)) as $incident) : ?>
                    <tr>
                        <td><?php echo h($incident['date']); ?></td>
                        <td><?php echo h($incident['type']); ?></td>
                        <td><?php echo h($incident['login']); ?></td>
                        <td><?php echo h($incident['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
