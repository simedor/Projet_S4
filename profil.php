<?php
require_once __DIR__ . '/Includes/fonctions.php';

// 1. SÉCURITÉ : Redirection si l'utilisateur tente d'accéder sans être connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$utilisateur = null;
$utilisateurs = lire_json('utilisateurs.json');
$commandesClient = [];
$commandesActives = [];

// Chargement des données de l'utilisateur
foreach ($utilisateurs as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $utilisateur = $unUtilisateur;
        break;
    }
}

// Sécurité supplémentaire : déconnexion immédiate si le compte a été bloqué
if ($utilisateur === null || $utilisateur['statut_compte'] === 'bloque') {
    session_destroy();
    header('Location: connexion.php?erreur=compte_bloque');
    exit();
}

// 2. RÉCUPÉRATION DES COMMANDES (Clients uniquement)
if ($utilisateur['role'] === 'client') {
    foreach (lire_json('commandes.json') as $commande) {
        if ((int) $commande['client_id'] === (int) $utilisateur['id']) {
            $commandesClient[] = $commande; // Ajout à l'historique global

            // Séparation des commandes encore en cours de traitement
            if ($commande['statut_commande'] !== 'livree' && $commande['statut_commande'] !== 'abandonnee') {
                $commandesActives[] = $commande;
            }
        }
    }

    // Tri personnalisé de l'historique par date décroissante
    usort($commandesClient, function ($a, $b) {
        return strcmp($b['date_commande'], $a['date_commande']);
    });
}

$titre_page = 'Profil';
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
            <p><strong>Nom :</strong> <span id="profil_nom"><?php echo h($utilisateur['nom']); ?></span></p>
            <p><strong>Prenom :</strong> <span id="profil_prenom"><?php echo h($utilisateur['prenom']); ?></span></p>
            <p><strong>Login :</strong> <?php echo h($utilisateur['login']); ?></p>
            <p><strong>Role :</strong> <?php echo h($utilisateur['role']); ?></p>
            <p><strong>Email :</strong> <span id="profil_email"><?php echo h($utilisateur['email']); ?></span></p>
            <p><strong>Adresse :</strong> <span id="profil_adresse"><?php echo h($utilisateur['adresse']); ?></span></p>
            <p><strong>Telephone :</strong> <span id="profil_telephone"><?php echo h($utilisateur['telephone']); ?></span></p>
            <p><strong>Infos complementaires :</strong> <span id="profil_infos_complementaires"><?php echo h(isset($utilisateur['infos_complementaires']) && $utilisateur['infos_complementaires'] !== '' ? $utilisateur['infos_complementaires'] : 'Aucune'); ?></span></p>
            <?php if ($utilisateur['role'] === 'client') : ?>
                <p><strong>Statut fidelite :</strong> <span id="profil_fidelite"><?php echo h(isset($utilisateur['fidelite']) ? $utilisateur['fidelite'] : 'Standard'); ?></span></p>
                <p><strong>Remise actuelle :</strong> <span id="profil_remise"><?php echo remise_fidelite(isset($utilisateur['fidelite']) ? $utilisateur['fidelite'] : 'Standard'); ?></span> %</p>
            <?php endif; ?>
            <button type="button" id="btn_modifier_profil">&#9998; Modifier mes informations</button>
        </div>

        <div class="encadre">
            <form id="form_profil" class="formulaire js-validate-form cache" novalidate>
                <h3>Modifier</h3>
                <div>
                    <label for="edit_nom">Nom</label>
                    <input type="text" id="edit_nom" name="nom" value="<?php echo h($utilisateur['nom']); ?>" data-rule="texte" autocomplete="family-name" required>
                    <small class="erreur_champ"></small>
                </div>
                <div>
                    <label for="edit_prenom">Prenom</label>
                    <input type="text" id="edit_prenom" name="prenom" value="<?php echo h($utilisateur['prenom']); ?>" data-rule="texte" autocomplete="given-name" required>
                    <small class="erreur_champ"></small>
                </div>
                <div>
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" value="<?php echo h($utilisateur['email']); ?>" maxlength="60" data-rule="email" autocomplete="email" required>
                    <small class="compteur" data-for="edit_email">0 / 60</small>
                    <small class="erreur_champ"></small>
                </div>
                <div>
                    <label for="edit_adresse">Adresse</label>
                    <input type="text" id="edit_adresse" name="adresse" value="<?php echo h($utilisateur['adresse']); ?>" maxlength="120" data-rule="adresse" autocomplete="street-address" required>
                    <small class="erreur_champ"></small>
                </div>
                <div>
                    <label for="edit_telephone">Telephone</label>
                    <input type="text" id="edit_telephone" name="telephone" value="<?php echo h($utilisateur['telephone']); ?>" maxlength="10" data-rule="telephone" autocomplete="tel" required>
                    <small class="compteur" data-for="edit_telephone">0 / 10</small>
                    <small class="erreur_champ"></small>
                </div>
                <div>
                    <label for="edit_infos_complementaires">Informations complementaires</label>
                    <textarea id="edit_infos_complementaires" name="infos_complementaires" rows="3" maxlength="150"><?php echo h(isset($utilisateur['infos_complementaires']) ? $utilisateur['infos_complementaires'] : ''); ?></textarea>
                    <small class="compteur" data-for="edit_infos_complementaires">0 / 150</small>
                </div>
                <div class="ligne_action">
                    <button type="submit">Enregistrer</button>
                    <button type="button" class="bouton_secondaire" id="btn_annuler_profil">Annuler</button>
                </div>
                <p id="message_profil" class="zone_message" aria-live="polite"></p>
            </form>
        </div>
    </div>
</section>

<?php if ($utilisateur['role'] === 'client') : ?>
    <section class="bloc_page">
        <h2>Commandes en cours</h2>
        <?php if (empty($commandesActives)) : ?>
            <p class="info">Aucune commande en cours.</p>
        <?php else : ?>
            <div class="grille_cartes">
                <?php foreach ($commandesActives as $commande) : ?>
                    <article class="carte_resume">
                        <h3>Commande #<?php echo (int) $commande['id']; ?></h3>
                        <p><strong>Statut :</strong> <?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></p>
                        <p><strong>Total :</strong> <?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</p>
                        <p><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir le detail</a></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="bloc_page">
        <h2>Historique</h2>
        <?php if (empty($commandesClient)) : ?>
            <p class="info">Aucune commande.</p>
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
                        <?php
                        // On vérifie si le client a déjà noté cette commande dans notations.json
                        $dejaNotee = false;
                        foreach (lire_json('notations.json') as $notation) {
                            if ((int) $notation['commande_id'] === (int) $commande['id'] && (int) $notation['client_id'] === (int) $utilisateur['id']) {
                                $dejaNotee = true;
                                break;
                            }
                        }
                        ?>
                        <tr>
                            <td>#<?php echo (int) $commande['id']; ?></td>
                            <td><?php echo h($commande['date_commande']); ?></td>
                            <td><?php echo h($commande['produit']); ?></td>
                            <td><?php echo number_format($commande['total'], 2, ',', ' '); ?> EUR</td>
                            <td><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></td>
                            <td>
                                <a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Detail</a>
                                <?php if ($commande['statut_commande'] === 'livree' && $commande['mode_retrait'] !== 'a_emporter' && !$dejaNotee) : ?>
                                    | <a href="notation.php?id=<?php echo (int) $commande['id']; ?>">Noter</a>
                                <?php endif; ?>
                                | <a href="traitements/process_recommander.php?id=<?php echo (int) $commande['id']; ?>">Recommander</a>
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