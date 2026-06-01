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
    <h2>Creer un compte</h2>

    <?php if ($messageErreur !== '') : ?>
        <p class="alerte"><?php echo h($messageErreur); ?></p>
    <?php endif; ?>

    <form action="traitements/process_inscription.php" method="POST" class="formulaire js-validate-form" id="form_inscription" novalidate>
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" maxlength="40" data-rule="texte" required>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="prenom">Prenom</label>
            <input type="text" name="prenom" id="prenom" maxlength="40" data-rule="texte" required>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="login">Login</label>
            <input type="text" name="login" id="login" maxlength="30" data-rule="login" required>
            <small class="compteur" data-for="login">0 / 30</small>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" maxlength="60" data-rule="email" required>
            <small class="compteur" data-for="email">0 / 60</small>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label>
            <div class="champ_mdp">
                <input type="password" name="mot_de_passe" id="mot_de_passe" maxlength="30" data-rule="password" required>
                <button type="button" class="toggle-password" data-target="mot_de_passe">Afficher</button>
            </div>
            <small class="compteur" data-for="mot_de_passe">0 / 30</small>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="naissance">Date de naissance</label>
            <input type="date" name="naissance" id="naissance" data-rule="naissance" required>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="civilite">Civilite</label>
            <select name="civilite" id="civilite" required>
                <option value="Mr">Mr</option>
                <option value="Mme">Mme</option>
            </select>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="adresse">Adresse</label>
            <input type="text" name="adresse" id="adresse" maxlength="120" data-rule="adresse" required>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="telephone">Telephone</label>
            <input type="tel" name="telephone" id="telephone" maxlength="10" data-rule="telephone" required>
            <small class="compteur" data-for="telephone">0 / 10</small>
            <small class="erreur_champ"></small>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
