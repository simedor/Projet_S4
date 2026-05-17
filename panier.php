<?php
session_start();

// Si personne n'est connecté, on redirige
if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit();
}

// Si le panier n'existe pas encore, on le crée vide
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>CY Pizza</h1>
    <nav id="menu_principal">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="presentation.php">Présentation</a></li>
            <li><a href="connexion.php">Connexion</a></li>
            <li><a href="inscription.php">Inscription</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="panier.php">Panier (<?php echo count($_SESSION['panier']); ?>)</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>Mon Panier</h2>

   <?php
if (isset($_GET['erreur']) && $_GET['erreur'] == 'paiement_refuse') {
    echo '<div class="alerte">Paiement refusé. Veuillez réessayer.</div>';
}
?>

    <?php if (count($_SESSION['panier']) == 0): ?>
        <p>Votre panier est vide. <a href="presentation.php">Voir la carte</a></p>
    <?php else: ?>

        <table border="1">
            <tr>
                <th>Plat</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>
            <?php foreach ($_SESSION['panier'] as $index => $item): ?>
            <tr>
                <td><?php echo $item['nom']; ?></td>
                <td><?php echo $item['prix']; ?> €</td>
                <td>
                    <a href="traitements/process_panier.php?action=supprimer&index=<?php echo $index; ?>">
                        <button type="button">Supprimer</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong><?php echo number_format($total, 2); ?> €</strong></td>
                <td></td>
            </tr>
        </table>

        <br>

        <!-- Choix commande immédiate ou différée (demandé par le cahier des charges) -->
        <form action="traitements/process_commande.php" method="POST">
            <p><strong>Quand souhaitez-vous être livré ?</strong></p>
            <input type="radio" name="type_livraison" id="immediate" value="immediate" checked>
            <label for="immediate">Maintenant</label>
            <br>
            <input type="radio" name="type_livraison" id="differee" value="differee">
            <label for="differee">Plus tard</label>
            <br>
            <!-- Ce champ apparait uniquement si "Plus tard" est sélectionné -->
            <div id="champ_heure" style="display:none;">
                <label for="heure_livraison">Choisir une heure :</label>
                <input type="datetime-local" name="heure_livraison" id="heure_livraison">
            </div>
            <br>
            <button type="submit">Passer la commande et payer</button>
        </form>

    <?php endif; ?>
</main>

<script>
// On affiche le champ heure uniquement si "Plus tard" est coché
document.querySelectorAll('input[name="type_livraison"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.value == 'differee') {
            document.getElementById('champ_heure').style.display = 'block';
        } else {
            document.getElementById('champ_heure').style.display = 'none';
        }
    });
});
</script>

</body>
</html>
