<?php
require_once __DIR__ . '/Includes/cybank.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: connexion.php?erreur=connexion_requise');
    exit();
}

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$paiement = $token !== '' ? cybank_trouver_paiement($token) : null;

if ($paiement === null) {
    header('Location: panier.php?erreur=paiement_refuse');
    exit();
}

if ((int) $paiement['client_id'] !== (int) $_SESSION['utilisateur_id']) {
    header('Location: accueil.php');
    exit();
}

$titre_page = 'Paiement CYBank';
include 'Includes/header.php';
?>

<section class="bloc_page bloc_formulaire">
    <h2>Redirection vers CYBank</h2>
    <p>Votre paiement va etre envoye vers l interface CYBank.</p>
    <p><strong>Montant :</strong> <?php echo h($paiement['montant']); ?> EUR</p>

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
