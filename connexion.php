<?php
require_once __DIR__ . '/Includes/fonctions.php';

$erreurs = [
    'connexion_requise' => 'Vous devez vous connecter pour acceder a cette page.',
    'identifiants_incorrects' => 'Login ou mot de passe incorrect.',
    'compte_bloque' => 'Votre compte est bloque.',
    'compte_desactive' => 'Votre compte est desactive.'
];

$messageErreur = '';

if (isset($_GET['erreur']) && isset($erreurs[$_GET['erreur']])) {
    $messageErreur = $erreurs[$_GET['erreur']];
}

$titre_page = 'Connexion';
include 'Includes/header.php';
?>

<section class="bloc_page bloc_formulaire">
    <h2>Connexion</h2>

    <?php if ($messageErreur !== '') : ?>
        <p class="alerte" role="alert"><?php echo h($messageErreur); ?></p>
    <?php endif; ?>

    <form action="traitements/process_connexion.php" method="POST" class="formulaire js-validate-form" id="form_connexion" novalidate>
        <div>
            <label for="identifiant">Login</label>
            <input type="text" name="identifiant" id="identifiant" maxlength="30" data-rule="login" autocomplete="username" required>
            <small class="compteur" data-for="identifiant">0 / 30</small>
            <small class="erreur_champ"></small>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label>
            <div class="champ_mdp">
                <input type="password" name="mot_de_passe" id="mot_de_passe" maxlength="30" data-rule="password" autocomplete="current-password" required>
                <button type="button" class="toggle-password" data-target="mot_de_passe" aria-label="Afficher le mot de passe">Afficher</button>
            </div>
            <small class="compteur" data-for="mot_de_passe">0 / 30</small>
            <small class="erreur_champ"></small>
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <div class="encadre">
        <h3>Comptes de test</h3>
        <p>Admin : <code>admin1 / admin123</code></p>
        <p>Restaurateur : <code>resto1 / resto123</code></p>
        <p>Livreur : <code>livreur1 / livreur123</code></p>
        <p>Client : <code>sara / client123</code></p>
    </div>
</section>

</main>
<script src="script.js"></script>
</body>
</html>
