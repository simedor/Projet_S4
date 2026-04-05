<?php
$data = file_get_contents("data/commandes.json");
$commandes = json_decode($data, true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>CY Pizza</h1>
</header>

<main>

<h2>Gestion des commandes</h2>

<table border="1">
<tr>
    <th>Numéro</th>
    <th>Client</th>
    <th>Produit</th>
    <th>Adresse</th>
    <th>Téléphone</th>
    <th>Statut</th>
    <th>Livreur</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php foreach ($commandes as $commande): ?>
<tr>
    <td>#<?= $commande["id"] ?></td>
    <td><?= $commande["client"] ?></td>
    <td><?= $commande["produit"] ?></td>
    <td><?= $commande["adresse"] ?></td>
    <td><?= $commande["telephone"] ?></td>
    <td><?= $commande["statut"] ?></td>
    <td>
        <?= $commande["livreur"] ? $commande["livreur"] : "Non attribué" ?>
    </td>
    <td><?= $commande["date"] ?></td>
    <td>
        <?php if ($commande["statut"] == "preparation"): ?>
            <button>Passer en livraison</button>
        <?php elseif ($commande["statut"] == "livraison"): ?>
            <button>En cours de livraison</button>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</main>
</body>
</html>
