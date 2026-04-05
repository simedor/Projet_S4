<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>CY Pizza</h1>
        <nav id="menu_principal">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="presentation.php">Presentation</a></li>
            <li><a href="connexion.php">Connexion</a></li>
            <li><a href="inscription.php">Inscription</a></li>
            <li><a href="profil.php">Profil</a></li>
        </ul>
        </nav>
    </header>

    <form action="#" id="formulaire_connexion">
        <table>
            <tbody>
                <tr>
                    <th><label for="identifiant">Identifiant :</label></th>
                    <td><input type="text" name="identifiant" id="identifiant" required></td>
                </tr>
                <tr>
                    <th><label for="mot_de_passe">Mot de passe :</label></th>
                    <td><input type="password" name="mot_de_passe" id="mot_de_passe" required></td>
                </tr>
                <tr>
                    <td><input type="submit" name="envoyer" id="envoyer"></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>
</body>
</html>
