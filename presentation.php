<?php 
include 'includes/header.php'; 

// On ouvre le frigo et on traduit le JSON
$contenu_fichier = file_get_contents('data/plats.json');
$carte = json_decode($contenu_fichier, true);

// LE FACTEUR ($_GET) : On regarde si le client a cliqué sur un filtre ou fait une recherche
// Si oui on range le mot dans la boîte, sinon la boîte est vide (null)
$filtre_categorie = isset($_GET['categorie']) ? $_GET['categorie'] : null;
$recherche = isset($_GET['recherche_plat']) ? $_GET['recherche_plat'] : null;
?>

<div id="liste_plat">
    <h2>Toute notre Carte</h2>

    <nav style="margin-bottom: 20px;">
        <strong>Filtrer : </strong>
        <a href="presentation.php">Tout voir</a> | 
        <a href="presentation.php?categorie=classique">Classiques</a> | 
        <a href="presentation.php?categorie=vegetarien">Végétarien</a> |
        <a href="presentation.php?categorie=menu">Menus</a>
    </nav>

    <ul>
        <?php 
        // Le Tapis Roulant
        foreach ($carte as $item) {
            
            $afficher_ce_plat = true; // Par défaut, on se dit qu'on va l'afficher

            // VIGILE 1 (Le Filtre par catégorie) : 
            // Si le client a demandé une catégorie ET que la catégorie du plat ne correspond pas...
            if ($filtre_categorie != null && $item['categorie'] != $filtre_categorie) {
                $afficher_ce_plat = false; // ... on recalcule, on ne l'affiche pas !
            }

            // VIGILE 2 (La Recherche tapée au clavier) :
            // Si le client a tapé un mot ET que ce mot n'est PAS dans le nom du plat...
            if ($recherche != null && stripos($item['nom'], $recherche) === false) {
                $afficher_ce_plat = false; // ... on ne l'affiche pas !
            }

            // Si après avoir passé les vigiles, on doit toujours l'afficher :
            if ($afficher_ce_plat == true) {
        ?>
            <li class="plat">
                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['nom']; ?>">
                <div class="description">
                    <h3><?php echo $item['nom']; ?></h3>
                    <p><?php echo $item['description']; ?></p>
                    <p><strong>Prix : <?php echo $item['prix']; ?> €</strong></p>
                    <small>Catégorie : <?php echo $item['categorie']; ?></small>
                </div>
            </li>
        <?php 
            } // Fin du "if on affiche"
        } // Fin du foreach
        ?>
    </ul>
</div>
</body>
</html>