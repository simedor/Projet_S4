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

if ($utilisateur === null || $utilisateur['role'] !== 'client') {
    header('Location: accueil.php');
    exit();
}

$commandes = lire_json('commandes.json');
foreach ($commandes as $uneCommande) {
    if ((int) $uneCommande['id'] === $commandeId) {
        $commande = $uneCommande;
        break;
    }
}

if ($commande === null || (int) $commande['client_id'] !== (int) $utilisateur['id'] || $commande['statut_commande'] !== 'livree') {
    header('Location: profil.php');
    exit();
}

foreach (lire_json('notations.json') as $notation) {
    if ((int) $notation['commande_id'] === $commandeId && (int) $notation['client_id'] === (int) $utilisateur['id']) {
        header('Location: profil.php');
        exit();
    }
}

$titre_page = 'Notation';
include 'Includes/header.php';
?>

<section class="bloc_page bloc_formulaire">
    <h2>Noter la commande #<?php echo (int) $commande['id']; ?></h2>
    <p>Commande concernee : <?php echo h($commande['produit']); ?></p>

    <form action="traitements/process_notation.php" method="POST" class="formulaire">
        <input type="hidden" name="commande_id" value="<?php echo (int) $commande['id']; ?>">

        <div>
            <label for="note_livraison">Note livraison (sur 5)</label>
            <select name="note_livraison" id="note_livraison" required>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </div>

        <div>
            <label for="note_produit">Note produit (sur 5)</label>
            <select name="note_produit" id="note_produit" required>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </div>

        <div>
            <label for="commentaire">Petit commentaire</label>
            <textarea name="commentaire" id="commentaire" rows="4"></textarea>
        </div>

        <button type="submit">Envoyer la note</button>
    </form>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
