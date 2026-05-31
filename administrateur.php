<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateurConnecte = null;
$listeUtilisateursPage = lire_json('utilisateurs.json');

foreach ($listeUtilisateursPage as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateurConnecte = $unUtilisateur;
        break;
    }
}

if ($utilisateurConnecte === null || $utilisateurConnecte['role'] !== 'admin') {
    header('Location: accueil.php');
    exit();
}

$filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'tous';
$utilisateursListe = lire_json('utilisateurs.json');
$commandesListe = lire_json('commandes.json');
$utilisateursAffiches = [];

foreach ($utilisateursListe as $unUtilisateur) {
    $nombreCommandes = 0;

    foreach ($commandesListe as $commande) {
        if ((int) $commande['client_id'] === (int) $unUtilisateur['id']) {
            $nombreCommandes++;
        }
    }

    $unUtilisateur['nombre_commandes'] = $nombreCommandes;

    if ($filtre === 'clients' && $unUtilisateur['role'] !== 'client') {
        continue;
    }

    if ($filtre === 'commandes' && $nombreCommandes === 0) {
        continue;
    }

    $utilisateursAffiches[] = $unUtilisateur;
}

$titre_page = 'Administration';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Gestion des utilisateurs</h2>

    <form method="GET" class="formulaire filtre_ligne">
        <div>
            <label for="filtre">Afficher</label>
            <select name="filtre" id="filtre">
                <option value="tous" <?php echo $filtre === 'tous' ? 'selected' : ''; ?>>Tous</option>
                <option value="clients" <?php echo $filtre === 'clients' ? 'selected' : ''; ?>>Clients seulement</option>
                <option value="commandes" <?php echo $filtre === 'commandes' ? 'selected' : ''; ?>>Ayant deja commande</option>
            </select>
        </div>

        <div class="zone_boutons">
            <button type="submit">Valider</button>
        </div>
    </form>

    <table class="tableau">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Login</th>
                <th>Role</th>
                <th>Statut</th>
                <th>Fidelite</th>
                <th>Remise</th>
                <th>Commandes</th>
                <th>Profil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateursAffiches as $unUtilisateur) : ?>
                <tr>
                    <td><?php echo h($unUtilisateur['prenom'] . ' ' . $unUtilisateur['nom']); ?></td>
                    <td><?php echo h($unUtilisateur['login']); ?></td>
                    <td><?php echo h($unUtilisateur['role']); ?></td>
                    <td><?php echo h(ucfirst($unUtilisateur['statut_compte'])); ?></td>
                    <td><?php echo h($unUtilisateur['fidelite']); ?></td>
                    <td><?php echo (int) $unUtilisateur['remise']; ?>%</td>
                    <td><?php echo (int) $unUtilisateur['nombre_commandes']; ?></td>
                    <td><a href="utilisateur.php?id=<?php echo (int) $unUtilisateur['id']; ?>">Voir</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
