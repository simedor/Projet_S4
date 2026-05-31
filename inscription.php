<?php
require_once __DIR__ . '/Includes/fonctions.php';

$erreurs = [
    'login_existant' => 'Ce login est deja pris.',
    'email_existant' => 'Cet email existe deja.',
    'champs_manquants' => 'Merci de remplir tous les champs.'
];

$messageErreur = '';

if (isset($_GET['erreur']) && isset($erreurs[$_GET['erreur']])) {
    $messageErreur = $erreurs[$_GET['erreur']];
}

$titre_page = 'Inscription';
include 'Includes/header.php';
?>

<section class="bloc_page bloc_formulaire">
    <h2>Creer un compte client</h2>

    <?php if ($messageErreur !== '') : ?>
        <p class="alerte"><?php echo h($messageErreur); ?></p>
    <?php endif; ?>

    <form action="traitements/process_inscription.php" method="POST" class="formulaire">
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" required>
        </div>

        <div>
            <label for="prenom">Prenom</label>
            <input type="text" name="prenom" id="prenom" required>
        </div>

        <div>
            <label for="login">Login</label>
            <input type="text" name="login" id="login" required>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required>
        </div>

        <div>
            <label for="naissance">Date de naissance</label>
            <input type="date" name="naissance" id="naissance" required>
        </div>

        <div>
            <label for="civilite">Civilite</label>
            <select name="civilite" id="civilite" required>
                <option value="Mr">Mr</option>
                <option value="Mme">Mme</option>
            </select>
        </div>

        <div>
            <label for="adresse">Adresse</label>
            <input type="text" name="adresse" id="adresse" required>
        </div>

        <div>
            <label for="telephone">Telephone</label>
            <input type="tel" name="telephone" id="telephone" required>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
