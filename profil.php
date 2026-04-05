<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
</head>
<body>
    <header>
        <nav id="menu_principal">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="commande.php">Commande</a></li>
            <li><a href="connexion.php">Connexion</a></li>
            <li><a href="inscription.php">Inscription</a></li>
            <li><a href="livraison.php">Livraison</a></li>
            <li><a href="notation.php">Notation</a></li>
            <li><a href="presentation.php">Presentation</a></li>
            <li><a href="profil.php">Profil</a></li>
        </ul>
        </nav>
    </header>

    <section id="informations_utilisateur">
        <table id="profil">
            <tbody>
                <tr>
                    <th><span>Nom</span></th>
                    <td><span>vide</span></td>
                    <td><button class="boutton_modif" type="button">C</button></td>
                </tr>
                <tr>
                    <th><span>Prenom</span></th>
                    <td><span>vide</span></td>
                    <td><button class="boutton_modif" type="button">C</button></td>
                </tr>
                <tr>
                    <th><span>Adresse</span></th>
                    <td><span>vide</span></td>
                    <td><button class="boutton_modif" type="button">C</button></td>
                </tr>
                <tr>
                    <th><span>Civilité</span></th>
                    <td><span>vide</span></td>
                    <td><button class="boutton_modif" type="button">C</button></td>
                </tr>
                <tr>
                    <th><span>Numéro de téléphone</span></th>
                    <td><span>vide</span></td>
                    <td><button class="boutton_modif" type="button">C</button></td>
                </tr>
            </tbody>
        </table>

        <div id="anciennes_commandes">
            <ul>

            </ul>
        </div>
    </section>
</body>
</html>
