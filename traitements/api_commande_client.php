<?php
require_once __DIR__ . '/../Includes/cybank.php';
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

if (!is_array($noms)) {
    $noms = [$noms];
}

if (!is_array($quantites)) {
    $quantites = [$quantites];
}
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

$calculRemises = calculer_total_commande_apres_remises($nouveauTotal, $commande);
$nouveauTotalPaye = $calculRemises['total'];
$difference = $nouveauTotalPaye - (float) $commande['total'];

if ($difference > 0) {
    $paiement = cybank_creer_paiement('modification_commande', $difference, [
        'commande_id' => $commande['id'],
        'lignes' => $nouvellesLignes,
        'produit' => implode(', ', $resume),
        'nouveau_total' => $nouveauTotalPaye,
        'total_avant_remise' => $nouveauTotal,
        'montant_remise_fidelite' => $calculRemises['remise_fidelite'],
        'montant_remise' => $calculRemises['remise_promo'],
        'pourcentage_fidelite' => $calculRemises['pourcentage_fidelite'],
        'pourcentage_promo' => $calculRemises['pourcentage_promo']
    ]);

    if ($paiement === null) {
        echo json_encode(['ok' => false, 'message' => 'CYBank est indisponible']);
        exit();
    }
} elseif ($difference < 0) {
    if (!isset($client['avoir'])) {
        $client['avoir'] = 0;
    }

    $client['avoir'] += abs($difference);
}

if ($difference > 0) {
    ajouter_incident('paiement', 'Preparation du paiement complementaire CYBank', $client['login'], $client['id']);
    echo json_encode([
        'ok' => true,
        'message' => 'Redirection vers CYBank pour payer la difference.',
        'redirect_url' => 'paiement_cybank.php?token=' . urlencode($paiement['token'])
    ]);
    exit();
}

$commandes[$commandeIndex]['lignes'] = $nouvellesLignes;
$commandes[$commandeIndex]['produit'] = implode(', ', $resume);
$commandes[$commandeIndex]['total'] = $nouveauTotalPaye;
$commandes[$commandeIndex]['total_avant_remise'] = $nouveauTotal;
$commandes[$commandeIndex]['montant_remise_fidelite'] = $calculRemises['remise_fidelite'];
$commandes[$commandeIndex]['montant_remise'] = $calculRemises['remise_promo'];
$commandes[$commandeIndex]['pourcentage_fidelite'] = $calculRemises['pourcentage_fidelite'];
$commandes[$commandeIndex]['pourcentage_promo'] = $calculRemises['pourcentage_promo'];

ecrire_json('commandes.json', $commandes);
ecrire_json('utilisateurs.json', $utilisateurs);
ajouter_incident('commande', 'Commande client modifiee', $client['login'], $client['id']);

$message = $difference > 0 ? 'Commande modifiee avec paiement complementaire.' : 'Commande modifiee.';

if ($difference < 0) {
    $message .= ' Un avoir a ete ajoute.';
}

echo json_encode(['ok' => true, 'message' => $message, 'total' => $nouveauTotalPaye]);
