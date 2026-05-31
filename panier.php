<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateurs = lire_json('utilisateurs.json');
$utilisateur = null;

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

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$panier = $_SESSION['panier'];
$total = 0;

foreach ($panier as $article) {
    $total += $article['prix'] * $article['quantite'];
}

$erreurs = [
    'panier_vide' => 'Votre panier est vide.',
    'paiement_refuse' => 'Le paiement CYBank a ete refuse.',
    'champs_manquants' => 'Merci de remplir les informations de commande et de paiement.'
];

$messageErreur = '';

if (isset($_GET['erreur']) && isset($erreurs[$_GET['erreur']])) {
    $messageErreur = $erreurs[$_GET['erreur']];
}

$titre_page = 'Panier';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Mon panier</h2>

    <?php if ($messageErreur !== '') : ?>
        <p class="alerte"><?php echo h($messageErreur); ?></p>
    <?php endif; ?>

    <?php if (empty($panier)) : ?>
        <p class="info">Votre panier est vide. <a href="presentation.php">Retour a la carte</a></p>
    <?php else : ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix</th>
                    <th>Quantite</th>
                    <th>Sous-total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panier as $article) : ?>
                    <tr>
                        <td><?php echo h($article['nom']); ?></td>
                        <td><?php echo number_format($article['prix'], 2, ',', ' '); ?> EUR</td>
                        <td>
                            <form action="traitements/process_panier.php" method="POST" class="ligne_action">
                                <input type="hidden" name="action" value="modifier">
                                <input type="hidden" name="nom" value="<?php echo h($article['nom']); ?>">
                                <input type="number" name="quantite" min="0" max="20" value="<?php echo (int) $article['quantite']; ?>">
                                <button type="submit">Mettre a jour</button>
                            </form>
                        </td>
                        <td><?php echo number_format($article['prix'] * $article['quantite'], 2, ',', ' '); ?> EUR</td>
                        <td>
                            <form action="traitements/process_panier.php" method="POST">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="nom" value="<?php echo h($article['nom']); ?>">
                                <button type="submit" class="bouton_danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><?php echo number_format($total, 2, ',', ' '); ?> EUR</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <form action="traitements/process_panier.php" method="POST" class="alignement_droite">
            <input type="hidden" name="action" value="vider">
            <button type="submit" class="bouton_secondaire">Vider le panier</button>
        </form>

        <div class="deux_colonnes">
            <div class="encadre">
                <h3>Infos client</h3>
                <p><strong>Nom :</strong> <?php echo h($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></p>
                <p><strong>Adresse :</strong> <?php echo h($utilisateur['adresse']); ?></p>
                <p><strong>Telephone :</strong> <?php echo h($utilisateur['telephone']); ?></p>
            </div>

            <form action="traitements/process_commande.php" method="POST" class="formulaire">
                <h3>Valider la commande</h3>

                <div>
                    <label>Mode</label>
                    <div class="ligne_radio">
                        <label><input type="radio" name="mode_retrait" value="livraison" checked> Livraison</label>
                        <label><input type="radio" name="mode_retrait" value="a_emporter"> A emporter</label>
                    </div>
                </div>

                <div>
                    <label>Quand ?</label>
                    <div class="ligne_radio">
                        <label><input type="radio" name="type_livraison" value="immediate" checked> Preparation immediate</label>
                        <label><input type="radio" name="type_livraison" value="differee"> Plus tard</label>
                    </div>
                </div>

                <div id="bloc_creneau" class="champ_cache">
                    <label for="creneau">Date et heure souhaitees</label>
                    <input type="datetime-local" name="creneau" id="creneau">
                </div>

                <h3>Paiement</h3>

                <div>
                    <label for="nom_carte">Nom sur la carte</label>
                    <input type="text" name="nom_carte" id="nom_carte" required>
                </div>

                <div>
                    <label for="numero_carte">Numero de carte</label>
                    <input type="text" name="numero_carte" id="numero_carte" required>
                </div>

                <div>
                    <label for="expiration">Expiration</label>
                    <input type="month" name="expiration" id="expiration" required>
                </div>

                <div>
                    <label for="cvv">CVV</label>
                    <input type="password" name="cvv" id="cvv" maxlength="4" required>
                </div>

                <button type="submit">Payer et commander</button>
            </form>
        </div>
    <?php endif; ?>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
