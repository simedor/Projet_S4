<?php
require_once __DIR__ . '/Includes/cybank.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$paiement = $token !== '' ? cybank_trouver_paiement($token) : null;
$donneesPaiement = [];
$commandeOrigine = null;

if ($paiement === null) {
    header('Location: panier.php?erreur=paiement_refuse');
    exit();
}

if ((int) $paiement['client_id'] !== (int) $_SESSION['utilisateur_id']) {
    header('Location: accueil.php');
    exit();
}

$donneesPaiement = isset($paiement['donnees']) && is_array($paiement['donnees']) ? $paiement['donnees'] : [];

if ($paiement['type_operation'] === 'modification_commande' && isset($donneesPaiement['commande_id'])) {
    foreach (lire_json('commandes.json') as $commande) {
        if ((int) $commande['id'] === (int) $donneesPaiement['commande_id']) {
            $commandeOrigine = $commande;
            break;
        }
    }
}

$titre_page = 'Paiement CYBank';
include 'Includes/header.php';
?>

<section class="bloc_page bloc_formulaire">
    <h2>Redirection vers CYBank</h2>
    <p>Votre paiement va etre envoye vers l interface CYBank.</p>

    <div class="encadre">
        <?php if ($paiement['type_operation'] === 'modification_commande') : ?>
            <h3>Recapitulatif de la modification</h3>
            <?php if ($commandeOrigine !== null) : ?>
                <p><strong>Ancien total paye :</strong> <?php echo number_format((float) $commandeOrigine['total'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <?php if (isset($donneesPaiement['total_avant_remise'])) : ?>
                <p><strong>Nouveau total avant remise :</strong> <?php echo number_format((float) $donneesPaiement['total_avant_remise'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <?php if (isset($donneesPaiement['montant_remise_fidelite']) && (float) $donneesPaiement['montant_remise_fidelite'] > 0) : ?>
                <p><strong>Remise fidelite :</strong> -<?php echo number_format((float) $donneesPaiement['montant_remise_fidelite'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <?php if (isset($donneesPaiement['montant_remise']) && (float) $donneesPaiement['montant_remise'] > 0) : ?>
                <p><strong>Code promo :</strong> -<?php echo number_format((float) $donneesPaiement['montant_remise'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <?php if (isset($donneesPaiement['nouveau_total'])) : ?>
                <p><strong>Nouveau total apres remise :</strong> <?php echo number_format((float) $donneesPaiement['nouveau_total'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <p><strong>Montant a payer maintenant :</strong> <?php echo h($paiement['montant']); ?> EUR</p>
        <?php else : ?>
            <h3>Recapitulatif de la commande</h3>
            <?php if (isset($donneesPaiement['total_avant_remise'])) : ?>
                <p><strong>Total avant remise :</strong> <?php echo number_format((float) $donneesPaiement['total_avant_remise'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <?php if (isset($donneesPaiement['montant_remise_fidelite']) && (float) $donneesPaiement['montant_remise_fidelite'] > 0) : ?>
                <p><strong>Remise fidelite :</strong> -<?php echo number_format((float) $donneesPaiement['montant_remise_fidelite'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <?php if (isset($donneesPaiement['montant_remise']) && (float) $donneesPaiement['montant_remise'] > 0) : ?>
                <p><strong>Code promo :</strong> -<?php echo number_format((float) $donneesPaiement['montant_remise'], 2, ',', ' '); ?> EUR</p>
            <?php endif; ?>
            <p><strong>Montant a payer :</strong> <?php echo h($paiement['montant']); ?> EUR</p>
        <?php endif; ?>
    </div>

    <div class="encadre">
        <h3>Carte de test</h3>
        <p>Numero : <code>5555 1234 5678 9000</code></p>
        <p>CVV : <code>555</code></p>
        <p>Titulaire et expiration : n importe quelle valeur</p>
    </div>

    <form id="form_cybank" action="<?php echo h(CYBANK_URL); ?>" method="POST" class="formulaire">
        <input type="hidden" name="transaction" value="<?php echo h($paiement['transaction']); ?>">
        <input type="hidden" name="montant" value="<?php echo h($paiement['montant']); ?>">
        <input type="hidden" name="vendeur" value="<?php echo h($paiement['vendeur']); ?>">
        <input type="hidden" name="retour" value="<?php echo h($paiement['retour']); ?>">
        <input type="hidden" name="control" value="<?php echo h($paiement['control']); ?>">
        <button type="submit">Aller sur CYBank</button>
    </form>
</section>

<script>
setTimeout(function () {
    var form = document.getElementById('form_cybank');
    if (form) {
        form.submit();
    }
}, 700);
</script>
</main>
</body>
</html>
