<?php
require_once __DIR__ . '/Includes/fonctions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$livreur = null;
$commandes = [];

foreach (lire_json('utilisateurs.json') as $unUtilisateur) {
    if ((int) $unUtilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $livreur = $unUtilisateur;
        break;
    }
}

if ($livreur === null || $livreur['role'] !== 'livreur') {
    header('Location: accueil.php');
    exit();
}

foreach (lire_json('commandes.json') as $commande) {
    if ((int) $commande['livreur_id'] === (int) $livreur['id']) {
        $commandes[] = $commande;
    }
}

$titre_page = 'Livraison';
include 'Includes/header.php';
?>

<section class="bloc_page">
    <h2>Mes livraisons</h2>
    <?php if (empty($commandes)) : ?>
        <p class="info">Aucune commande attribuee pour le moment.</p>
    <?php else : ?>
        <div class="grille_cartes">
            <?php foreach ($commandes as $commande) : ?>
                <article class="carte_resume" id="carte_livraison_<?php echo (int) $commande['id']; ?>">
                    <h3>Commande #<?php echo (int) $commande['id']; ?></h3>
                    <p><strong>Client :</strong> <?php echo h($commande['client_nom']); ?></p>
                    <p><strong>Adresse :</strong> <?php echo h($commande['adresse']); ?></p>
                    <p><strong>Telephone :</strong> <?php echo h(isset($commande['telephone']) ? $commande['telephone'] : 'Non renseigne'); ?></p>
                    <p><strong>Interphone :</strong> <?php echo h(isset($commande['interphone']) && $commande['interphone'] !== '' ? $commande['interphone'] : 'Non renseigne'); ?></p>
                    <p><strong>Etage :</strong> <?php echo h(isset($commande['etage']) && $commande['etage'] !== '' ? $commande['etage'] : 'Non renseigne'); ?></p>
                    <p><strong>Commentaire :</strong> <?php echo h(isset($commande['commentaire_livraison']) && $commande['commentaire_livraison'] !== '' ? $commande['commentaire_livraison'] : 'Aucun'); ?></p>
                    <p><strong>Statut :</strong> <span class="statut_livraison"><?php echo h(ucfirst(str_replace('_', ' ', $commande['statut_commande']))); ?></span></p>
                    <p><a href="detail_commande.php?id=<?php echo (int) $commande['id']; ?>">Voir</a></p>
                    <p class="ligne_action">
                        <a class="bouton_secondaire" target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($commande['adresse']); ?>">Maps</a>
                        <a class="bouton_secondaire" target="_blank" rel="noopener noreferrer" href="https://waze.com/ul?q=<?php echo urlencode($commande['adresse']); ?>">Waze</a>
                    </p>
                    <?php if ($commande['statut_commande'] === 'en_livraison') : ?>
                        <div class="ligne_action zone_actions_livraison">
                            <button type="button" class="btn-livraison" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="livree">Marquer livree</button>
                            <button type="button" class="btn-livraison bouton_danger" data-commande-id="<?php echo (int) $commande['id']; ?>" data-action="abandonnee">Marquer abandonnee</button>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p id="message_livraison" class="zone_message" aria-live="polite"></p>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
