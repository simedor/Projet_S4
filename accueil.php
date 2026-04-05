<?php 
// 1. LE CLONEUR : On importe le haut de la page (notre moule à gâteau)
include 'includes/header.php'; 

// 2. LE FRIGO : On va chercher nos données dans le fichier JSON
$contenu_fichier = file_get_contents('data/plats.json');

// 3. LE TRADUCTEUR : On transforme le texte JSON en un vrai tableau PHP qu'on appelle $carte
$carte = json_decode($contenu_fichier, true);
?>

<div id="recherche">
    <form action="presentation.php" method="GET">
        <label for="recherche_plat">Rechercher un plat:</label>
        <input type="search" id="recherche_plat" name="recherche_plat" placeholder="Ex: Vegan..." />
        <button type="submit">Rechercher</button>
    </form>
</div>

<div id="liste_plat">
    
    <h2>Plat du jour</h2>
    <?php 
    // LE TAPIS ROULANT : On passe chaque élément de la carte en revue
    foreach ($carte as $item) {
        // LE VIGILE : "Est-ce que cet item a l'étiquette plat_du_jour à 'true' ?"
        if ($item['plat_du_jour'] == true) { 
    ?>
        <div id="plat_du_jour" class="plat">
            <img src="<?php echo $item['image']; ?>" alt="Pizza <?php echo $item['nom']; ?>">
            <div class="description">
                <h3><?php echo $item['nom']; ?></h3>
                <p><?php echo $item['description']; ?></p>
                <p><strong>Prix : <?php echo $item['prix']; ?> €</strong></p>
            </div>
        </div>
    <?php 
        } // Fin du vigile (if)
    } // Fin du tapis roulant (foreach)
    ?>


    <h2>Nos best-sellers</h2>
    <ul>
        <?php 
        foreach ($carte as $item) {
            // Le vigile vérifie si c'est un best_seller
            if ($item['best_seller'] == true) { 
        ?>
            <li class="plat">
                <img src="<?php echo $item['image']; ?>" alt="Pizza <?php echo $item['nom']; ?>">
                <div class="description">
                    <h3><?php echo $item['nom']; ?></h3>
                    <p><?php echo $item['description']; ?></p>
                    <p><strong>Prix : <?php echo $item['prix']; ?> €</strong></p>
                </div>
            </li>
        <?php 
            }
        } 
        ?>
    </ul>

</div>
</body>
</html>