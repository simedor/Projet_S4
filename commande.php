<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>CY Pizza</h1>
    <nav id="menu_principale">
        <ul>
            <li><a href="accueil.html">Accueil</a></li>
            <li><a href="#">Commande</a></li>
            <li><a href="connexion.html">Connexion</a></li>
            <li><a href="inscription.html">Inscription</a></li>
            <li><a href="livraison.html">Livraison</a></li>
            <li><a href="notation.html">Notation</a></li>
            <li><a href="presentation.html">Presentation</a></li>
            <li><a href="profil.html">Profil</a></li>
        </ul>
    </nav>
</header>

<h2>Gestion des commandes</h2>

<form>
    <label for="tri">Afficher :</label>
    <select id="tri" name="tri">
        <option value="toutes">Toutes les commandes</option>
        <option value="preparation">En préparation</option>
        <option value="livraison">En livraison</option>
    </select>
    <button type="submit">Valider</button>
</form>

<br>

<table border="1">
    <tr>
        <th>Numéro</th>
        <th>Client</th>
        <th>Produits</th>
        <th>Adresse</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>
    <tr>
        <td>#101</td>
        <td>Siméon</td>
        <td>Pizza Reine</td>
        <td>12 rue des chenes</td>
        <td>En préparation</td>
        <td><button>Passer en livraison</button></td>
    </tr>
    <tr>
        <td>#102</td>
        <td>Alexandre</td>
        <td>Pizza Margherita</td>
        <td>2 Boulevard de l'Oise</td>
        <td>En préparation</td>
        <td><button>Passer en livraison</button></td>
    </tr>
    <tr>
        <td>#099</td>
        <td>Charbel</td>
        <td>Pizza Pepperoni</td>
        <td>5 rue du Port</td>
        <td>En livraison</td>
        <td>-</td>
    </tr>
</table>

</body>
</html>
