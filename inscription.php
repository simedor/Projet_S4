<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>CY Pizza</h1>
        <nav id="menu_principale">
        <ul>
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="presentation.php">Presentation</a></li>
            <li><a href="connexion.html">Connexion</a></li>
            <li><a href="#">Inscription</a></li>
            <li><a href="profil.html">Profil</a></li>
        </ul>
        </nav>
    </header>

    <h2>Créer un compte</h2>

    <?php
    if (isset($_GET['erreur'])) { 
        if ($_GET['erreur'] == 'email_existant') {
            echo '<div class="alerte">Cet email est déjà utilisé. Veuillez en choisir un autre ou vous connecter.</div>';
        }
    }
    ?>
    
    <form action="traitements/process_inscription.php" method="POST" id="formulaire_inscription">
        <table>
            <tbody>
                <tr>
                    <th><label for="nom">Nom</label></th>
                    <td><input type="text" name="nom" id="nom" required></td>
                </tr>
                <tr>
                    <th><label for="prenom">Prenom</label></th>
                    <td><input type="text" name="prenom" id="prenom" required></td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td><input type="email" name="email" id="email" required></td>
                </tr>
                <tr>
                    <th><label for="civilite">Civilité</label></th>
                    <td>
                        <input type="radio" name="civilite" id="mr" value="Mr"><label for="mr">Mr</label>
                        <input type="radio" name="civilite" id="mme" value="Mme"><label for="mme">Mme</label>
                    </td>
                </tr>
                <tr>
                    <th><label for="adresse">Adresse</label></th>
                    <td><input type="text" name="adresse" id="adresse" required></td>
                </tr>
                <tr>
                    <th><label for="telephone">Numéro de téléphone</label></th>
                    <td><input type="tel" name="telephone" id="telephone" required></td>
                </tr>
                <tr>
                    <td><input type="submit" name="envoyer" id="envoyer" value="S'inscrire"></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>
</html>
