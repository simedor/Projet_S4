<?php
require_once __DIR__ . '/../Includes/fonctions.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['ok' => false, 'message' => 'Non connecte']);
    exit();
}

$utilisateurs = lire_json('utilisateurs.json');
$commandes = lire_json('commandes.json');
$plats = lire_json('plats.json');
$client = null;
$commandeId = isset($_POST['commande_id']) ? (int) $_POST['commande_id'] : 0;

foreach ($utilisateurs as &$utilisateur) {
    if ((int) $utilisateur['id'] === (int) $_SESSION['utilisateur_id']) {
        $client = &$utilisateur;
        break;
    }
}

if ($client === null || $client['role'] !== 'client') {
    echo json_encode(['ok' => false, 'message' => 'Acces refuse']);
    exit();
}

$commandeIndex = -1;

foreach ($commandes as $index => $commande) {
    if ((int) $commande['id'] === $commandeId) {
        $commandeIndex = $index;
        break;
    }
}

if ($commandeIndex === -1) {
    echo json_encode(['ok' => false, 'message' => 'Commande introuvable']);
    exit();
}

$commande = $commandes[$commandeIndex];

if ((int) $commande['client_id'] !== (int) $client['id'] || !in_array($commande['statut_commande'], ['a_preparer', 'en_attente'], true)) {
    echo json_encode(['ok' => false, 'message' => 'Commande non modifiable']);
    exit();
}

$noms = isset($_POST['noms']) ? $_POST['noms'] : [];
$quantites = isset($_POST['quantites']) ? $_POST['quantites'] : [];
$ajoutNom = isset($_POST['ajout_nom']) ? trim($_POST['ajout_nom']) : '';
$ajoutQuantite = isset($_POST['ajout_quantite']) ? (int) $_POST['ajout_quantite'] : 0;
$nouvellesLignes = [];
$resume = [];
$nouveauTotal = 0;

foreach ($noms as $index => $nom) {
    $qte = isset($quantites[$index]) ? (int) $quantites[$index] : 0;

    if ($qte > 0) {
        foreach ($commande['lignes'] as $ligne) {
            if ($ligne['nom'] === $nom) {
                $sousTotal = $ligne['prix'] * $qte;
                $nouvellesLignes[] = [
                    'nom' => $ligne['nom'],
                    'prix' => $ligne['prix'],
                    'quantite' => $qte,
                    'sous_total' => $sousTotal
                ];
                $resume[] = $ligne['nom'] . ' x' . $qte;
                $nouveauTotal += $sousTotal;
                break;
            }
        }
    }
}

if ($ajoutNom !== '' && $ajoutQuantite > 0) {
    foreach ($plats as $plat) {
        if ($plat['nom'] === $ajoutNom) {
            $trouve = false;
            foreach ($nouvellesLignes as &$ligne) {
                if ($ligne['nom'] === $plat['nom']) {
                    $ligne['quantite'] += $ajoutQuantite;
                    $ligne['sous_total'] = $ligne['prix'] * $ligne['quantite'];
                    $trouve = true;
                    break;
                }
            }

            if (!$trouve) {
                $nouvellesLignes[] = [
                    'nom' => $plat['nom'],
                    'prix' => $plat['prix'],
                    'quantite' => $ajoutQuantite,
                    'sous_total' => $plat['prix'] * $ajoutQuantite
                ];
            }
            break;
        }
    }
}

if (count($nouvellesLignes) === 0) {
    echo json_encode(['ok' => false, 'message' => 'La commande ne peut pas etre vide']);
    exit();
}

$resume = [];
$nouveauTotal = 0;

foreach ($nouvellesLignes as &$ligne) {
    $ligne['sous_total'] = $ligne['prix'] * $ligne['quantite'];
    $nouveauTotal += $ligne['sous_total'];
    $resume[] = $ligne['nom'] . ' x' . $ligne['quantite'];
}

$difference = $nouveauTotal - $commande['total'];

if ($difference > 0) {
    $nomCarte = isset($_POST['nom_carte']) ? trim($_POST['nom_carte']) : '';
    $numeroCarte = isset($_POST['numero_carte']) ? preg_replace('/\D/', '', $_POST['numero_carte']) : '';
    $expiration = isset($_POST['expiration']) ? trim($_POST['expiration']) : '';
    $cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';

    if ($nomCarte === '' || $numeroCarte === '' || $expiration === '' || strlen($cvv) < 3) {
        echo json_encode(['ok' => false, 'message' => 'Paiement complementaire invalide']);
        exit();
    }
}

if ($difference < 0) {
    if (!isset($client['avoir'])) {
        $client['avoir'] = 0;
    }

    $client['avoir'] += abs($difference);
}

$commandes[$commandeIndex]['lignes'] = $nouvellesLignes;
$commandes[$commandeIndex]['produit'] = implode(', ', $resume);
$commandes[$commandeIndex]['total'] = $nouveauTotal;

if (!isset($commandes[$commandeIndex]['paiements']) || !is_array($commandes[$commandeIndex]['paiements'])) {
    $commandes[$commandeIndex]['paiements'] = [];
}

if ($difference > 0) {
    $commandes[$commandeIndex]['paiements'][] = [
        'date' => date('Y-m-d H:i'),
        'montant' => $difference,
        'type' => 'complement'
    ];
}

ecrire_json('commandes.json', $commandes);
ecrire_json('utilisateurs.json', $utilisateurs);

$message = $difference > 0 ? 'Commande modifiee avec paiement complementaire.' : 'Commande modifiee.';

if ($difference < 0) {
    $message .= ' Un avoir a ete ajoute.';
}

echo json_encode(['ok' => true, 'message' => $message, 'total' => $nouveauTotal]);
