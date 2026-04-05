<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - CY Pizza</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>CY Pizza</h1>
        <nav id="menu_principal">
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="presentation.php">Presentation</a></li>
                <li><a href="administrateur.html">Administrateur</a></li>
                <li><a href="connexion.html">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
                <li><a href="profil.html">Profil</a></li>
            </ul>
        </nav>
    </header>

    <div id="recherche">
        <label for="recherche_plat">Rechercher un plat:</label>
        <input type="search" id="recherche_plat" name="recherche_plat" />
        <button>Rechercher</button>
    </div>

    <div id="liste_plat">
        <h2>Plat du jour</h2>
        <div id="plat_du_jour" class="plat">
            <img src="Images/Reine.png" alt="Pizza Reine">
            <div class="description">
                <h3>Reine</h3>
                <p>Description</p>
            </div>
        </div>

        <h2>Nos best-sellers</h2>
        <ul>
            <li class="plat">
                <img src="Images/Vegan.jpg" alt="Pizza Vegan">
                <div class="description">
                    <h3>Vegan</h3>
                    <p>Description</p>
                </div>
            </li>
            <li class="plat">
                <img src="Images/Cannibale.png" alt="Pizza Cannibale">
                <div class="description">
                    <h3>Cannibale</h3>
                    <p>Description</p>
                </div>
            </li>
            <li class="plat">
                <img src="Images/Margharita.jpg" alt="Pizza Margharita">
                <div class="description">
                    <h3>Margharita</h3>
                    <p>Description</p>
                </div>
            </li>
            <li class="plat">
                <img src="Images/Pepperoni.jpg" alt="Pizza Pepperoni">
                <div class="description">
                    <h3>Pepperoni</h3>
                    <p>Description</p>
                </div>
            </li>
        </ul>

        <h2>Toutes nos Pizzas</h2>
        <ul>
            <li class="plat"><img src="Images/pizza1.png" alt="Pizza"><div class="description"><h3>Pizza 1</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza2.png" alt="Pizza"><div class="description"><h3>Pizza 2</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza3.png" alt="Pizza"><div class="description"><h3>Pizza 3</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza4.png" alt="Pizza"><div class="description"><h3>Pizza 4</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza5.png" alt="Pizza"><div class="description"><h3>Pizza 5</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza6.png" alt="Pizza"><div class="description"><h3>Pizza 6</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza7.png" alt="Pizza"><div class="description"><h3>Pizza 7</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza8.png" alt="Pizza"><div class="description"><h3>Pizza 8</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza9.png" alt="Pizza"><div class="description"><h3>Pizza 9</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza10.png" alt="Pizza"><div class="description"><h3>Pizza 10</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza11.png" alt="Pizza"><div class="description"><h3>Pizza 11</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza12.png" alt="Pizza"><div class="description"><h3>Pizza 12</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza13.png" alt="Pizza"><div class="description"><h3>Pizza 13</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza14.png" alt="Pizza"><div class="description"><h3>Pizza 14</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza15.png" alt="Pizza"><div class="description"><h3>Pizza 15</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza16.png" alt="Pizza"><div class="description"><h3>Pizza 16</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza17.png" alt="Pizza"><div class="description"><h3>Pizza 17</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza18.png" alt="Pizza"><div class="description"><h3>Pizza 18</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza19.png" alt="Pizza"><div class="description"><h3>Pizza 19</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza20.png" alt="Pizza"><div class="description"><h3>Pizza 20</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza21.png" alt="Pizza"><div class="description"><h3>Pizza 21</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza22.png" alt="Pizza"><div class="description"><h3>Pizza 22</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza23.png" alt="Pizza"><div class="description"><h3>Pizza 23</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza24.png" alt="Pizza"><div class="description"><h3>Pizza 24</h3><p>Description</p></div></li>
            <li class="plat"><img src="Images/pizza25.png" alt="Pizza"><div class="description"><h3>Pizza 25</h3><p>Description</p></div></li>
        </ul>

        <h2>Nos Menus</h2>
        <ul>
            <li class="plat">
                <img src="Images/menu1.png" alt="Menu 1">
                <div class="description">
                    <h3>Menu Solo</h3>
                    <p>1 Pizza au choix + 1 Boisson 33cl</p>
                </div>
            </li>
            <li class="plat">
                <img src="Images/menu2.png" alt="Menu 2">
                <div class="description">
                    <h3>Menu Duo</h3>
                    <p>2 Pizzas au choix + 2 Boissons 33cl</p>
                </div>
            </li>
            <li class="plat">
                <img src="Images/menu3.png" alt="Menu 3">
                <div class="description">
                    <h3>Menu Famille</h3>
                    <p>3 Pizzas au choix + 1 Boisson 1.5L</p>
                </div>
            </li>
        </ul>
    </div>
</body>
</html>