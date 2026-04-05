<?php
echo "PHP OK<br>";

$data = file_get_contents(__DIR__ . "/data/commandes.json");

if ($data === false) {
    die("Erreur lecture fichier");
}

$commandes = json_decode($data, true);

if ($commandes === null) {
    die("JSON invalide");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livraison</title>
</head>
<body>

<h1>CY Pizza - Livraison</h1>

<h2>Livraisons en cours</h2>

<?php
$trouve = false;

foreach ($commandes as $commande) {
    if ($commande["statut"] == "livraison") {
        $trouve = true;
?>

        <div style="border:1px solid black; margin:10px; padding:10px;">
            <p>Commande #<?= $commande["id"] ?></p>
            <p>Client : <?= $commande["client"] ?></p>
            <p>Produit : <?= $commande["produit"] ?></p>
            <p>Adresse : <?= $commande["adresse"] ?></p>
            <p>Téléphone : <?= $commande["telephone"] ?></p>
            <p>Livreur : <?= $commande["livreur"] ?></p>
            <p>Date : <?= $commande["date"] ?></p>

            <button onclick="alert('Maps')">Ouvrir Maps</button>
            <button>Livré</button>
        </div>

<?php
    }
}

if (!$trouve) {
    echo "<p>Aucune livraison en cours</p>";
}
?>

</body>
</html>
