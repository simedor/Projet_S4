<?php
session_start();
// On détruit toute la session
session_destroy();
// On redirige vers l'accueil
header('Location: ../accueil.php');
exit();
?>
