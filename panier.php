<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;
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
    'paiement_refuse' => 'Le paiement a ete refuse.',
    'champs_manquants' => 'Merci de remplir tous les champs.'
];

$messageErreur = '';
if (isset($_GET['erreur']) && isset($erreurs[$_GET['erreur']])) {
    $messageErreur = $erreurs[$_GET['erreur']];
}

$messageSucces = '';
if (isset($_GET['recommande']) && $_GET['recommande'] === 'ok') {
    $messageSucces = 'L ancienne commande a ete remise dans votre panier.';
}

$titre_page = 'Panier';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Mon panier</h2>

    <?php if ($messageErreur !== '') : ?>
        <p class="alerte" role="alert"><?php echo h($messageErreur); ?></p>
    <?php endif; ?>

    <?php if ($messageSucces !== '') : ?>
        <p class="succes"><?php echo h($messageSucces); ?></p>
    <?php endif; ?>

    <?php if (empty($panier)) : ?>
        <p class="info">Votre panier est vide.</p>
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
                            <form action="traitements/process_panier.php" method="POST" class="ligne_action js-validate-form" novalidate>
                                <input type="hidden" name="action" value="modifier">
                                <input type="hidden" name="nom" value="<?php echo h($article['nom']); ?>">
                                <input type="number" name="quantite" min="0" max="20" value="<?php echo (int) $article['quantite']; ?>" data-rule="quantite" required>
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
        </table>

        <form action="traitements/process_panier.php" method="POST" class="alignement_droite">
            <input type="hidden" name="action" value="vider">
            <button type="submit" class="bouton_secondaire">Vider le panier</button>
        </form>

        <form action="traitements/process_commande.php" method="POST" class="formulaire js-validate-form" id="form_commande" novalidate>
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
                    <label><input type="radio" name="type_livraison" value="immediate" checked> Maintenant</label>
                    <label><input type="radio" name="type_livraison" value="differee"> Plus tard</label>
                </div>
            </div>

            <div id="bloc_creneau" class="cache">
                <label for="creneau">Date et heure</label>
                <input type="datetime-local" name="creneau" id="creneau" data-rule="datetime">
                <small class="erreur_champ"></small>
            </div>

            <div id="bloc_infos_livraison">
                <div>
                    <label for="interphone">Code interphone</label>
                    <input type="text" name="interphone" id="interphone" maxlength="30" placeholder="Ex : B203 ou 45A">
                    <small class="compteur" data-for="interphone">0 / 30</small>
                </div>

                <div>
                    <label for="etage">Etage</label>
                    <input type="text" name="etage" id="etage" maxlength="20" placeholder="Ex : 3e etage">
                    <small class="compteur" data-for="etage">0 / 20</small>
                </div>

                <div>
                    <label for="commentaire_livraison">Commentaire de livraison</label>
                    <textarea name="commentaire_livraison" id="commentaire_livraison" rows="3" maxlength="150" placeholder="Ex : appeler en arrivant"></textarea>
                    <small class="compteur" data-for="commentaire_livraison">0 / 150</small>
                </div>
            </div>

            <div>
                <label for="nom_carte">Nom sur la carte</label>
                <input type="text" name="nom_carte" id="nom_carte" maxlength="40" data-rule="texte" autocomplete="cc-name" required>
                <small class="erreur_champ"></small>
            </div>

            <div>
                <label for="numero_carte">Numero de carte</label>
                <input type="text" name="numero_carte" id="numero_carte" maxlength="16" data-rule="carte" autocomplete="cc-number" required>
                <small class="compteur" data-for="numero_carte">0 / 16</small>
                <small class="erreur_champ"></small>
            </div>

            <div>
                <label for="expiration">Expiration</label>
                <input type="month" name="expiration" id="expiration" data-rule="expiration" autocomplete="cc-exp" required>
                <small class="erreur_champ"></small>
            </div>

            <div>
                <label for="cvv">CVV</label>
                <div class="champ_mdp">
                    <input type="password" name="cvv" id="cvv" maxlength="4" data-rule="cvv" autocomplete="cc-csc" required>
                    <button type="button" class="toggle-password" data-target="cvv" aria-label="Afficher le CVV">Afficher</button>
                </div>
                <small class="compteur" data-for="cvv">0 / 4</small>
                <small class="erreur_champ"></small>
            </div>

            <button type="submit">Payer et commander</button>
        </form>
    <?php endif; ?>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
