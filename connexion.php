<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    include 'includes/header.php';
    ?>

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
<script src="script.js"></script>
</body>
</html>
