<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;
$commande = null;
$commandeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

foreach (lire_json('utilisateurs.json') as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

if ($utilisateur === null || $utilisateur['role'] !== 'client') {
    header('Location: accueil.php');
    exit();
}

foreach (lire_json('commandes.json') as $uneCommande) {
    if ((int) $uneCommande['id'] === $commandeId) {
        $commande = $uneCommande;
        break;
    }
}

if ($commande === null || (int) $commande['client_id'] !== (int) $utilisateur['id'] || $commande['statut_commande'] !== 'livree' || $commande['mode_retrait'] === 'a_emporter') {
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
    <p>Commande : <?php echo h($commande['produit']); ?></p>

    <form action="traitements/process_notation.php" method="POST" class="formulaire js-validate-form" id="form_notation" novalidate>
        <input type="hidden" name="commande_id" value="<?php echo (int) $commande['id']; ?>">

        <div>
            <label for="note_livraison">Note livraison</label>
            <select name="note_livraison" id="note_livraison" required>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </div>

        <div>
            <label for="note_produit">Note produit</label>
            <select name="note_produit" id="note_produit" required>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </div>

        <div>
            <label for="commentaire">Commentaire</label>
            <textarea name="commentaire" id="commentaire" maxlength="150"></textarea>
            <small class="compteur" data-for="commentaire">0 / 150</small>
        </div>

        <button type="submit">Envoyer</button>
    </form>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
