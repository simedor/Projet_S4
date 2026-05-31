<?php
require_once __DIR__ . '/../Includes/fonctions.php';
session_destroy();

header('Location: ../accueil.php');
exit();
