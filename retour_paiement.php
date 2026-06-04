<?php
require_once __DIR__ . '/Includes/cybank.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$transaction = isset($_GET['transaction']) ? trim($_GET['transaction']) : '';
$montant = isset($_GET['montant']) ? trim($_GET['montant']) : '';
$vendeur = isset($_GET['vendeur']) ? trim($_GET['vendeur']) : '';
$statut = isset($_GET['status']) ? trim($_GET['status']) : (isset($_GET['statut']) ? trim($_GET['statut']) : '');
$control = isset($_GET['control']) ? trim($_GET['control']) : '';

$paiement = $token !== '' ? cybank_trouver_paiement($token) : null;

if ($paiement === null) {
    header('Location: panier.php?erreur=paiement_refuse');
    exit();
}

$controlValide = cybank_control_retour_valide($transaction, $montant, $vendeur, $statut, $control);

if (
    !$controlValide ||
    $transaction !== $paiement['transaction'] ||
    $montant !== $paiement['montant'] ||
    $vendeur !== $paiement['vendeur']
) {
    ajouter_incident('paiement', 'Retour CYBank invalide', '', (int) $paiement['client_id']);
    cybank_supprimer_paiement($token);
    header('Location: panier.php?erreur=paiement_refuse');
    exit();
}

if ($statut !== 'accepted') {
    ajouter_incident('paiement', 'Paiement CYBank refuse', '', (int) $paiement['client_id']);
    cybank_supprimer_paiement($token);

    if ($paiement['type_operation'] === 'modification_commande') {
        header('Location: detail_commande.php?id=' . (int) $paiement['donnees']['commande_id'] . '&erreur=paiement_refuse');
        exit();
    }

    header('Location: panier.php?erreur=paiement_refuse');
    exit();
}

if ($paiement['type_operation'] === 'nouvelle_commande') {
    $utilisateurs = lire_json('utilisateurs.json');
    $commandes = lire_json('commandes.json');
    $donnees = $paiement['donnees'];
    $nouvelId = 1;

    foreach ($commandes as $commande) {
        if ((int) $commande['id'] >= $nouvelId) {
            $nouvelId = (int) $commande['id'] + 1;
        }
    }

    $commandes[] = [
        'id' => $nouvelId,
        'client_id' => $donnees['client_id'],
        'client_nom' => $donnees['client_nom'],
        'produit' => $donnees['produit'],
        'lignes' => $donnees['lignes'],
        'code_promo' => isset($donnees['code_promo']) ? $donnees['code_promo'] : '',
        'pourcentage_promo' => isset($donnees['pourcentage_promo']) ? (int) $donnees['pourcentage_promo'] : 0,
        'montant_remise' => isset($donnees['montant_remise']) ? (float) $donnees['montant_remise'] : 0,
        'total_avant_remise' => isset($donnees['total_avant_remise']) ? (float) $donnees['total_avant_remise'] : (float) $paiement['montant'],
        'adresse' => $donnees['adresse'],
        'telephone' => $donnees['telephone'],
        'interphone' => $donnees['interphone'],
        'etage' => $donnees['etage'],
        'commentaire_livraison' => $donnees['commentaire_livraison'],
        'mode_retrait' => $donnees['mode_retrait'],
        'type_livraison' => $donnees['type_livraison'],
        'creneau' => $donnees['creneau'],
        'statut_paiement' => 'paye',
        'statut_commande' => $donnees['type_livraison'] === 'differee' ? 'en_attente' : 'a_preparer',
        'livreur_id' => 0,
        'livreur_nom' => '',
        'date_commande' => date('Y-m-d H:i'),
        'total' => (float) $paiement['montant'],
        'paiements' => [
            [
                'date' => date('Y-m-d H:i'),
                'montant' => (float) $paiement['montant'],
                'type' => 'initial',
                'transaction' => $paiement['transaction']
            ]
        ]
    ];

    ecrire_json('commandes.json', $commandes);
    cybank_supprimer_paiement($token);
    $_SESSION['panier'] = [];
    unset($_SESSION['code_promo']);
    ajouter_incident('paiement', 'Paiement CYBank accepte', '', (int) $paiement['client_id']);
    header('Location: profil.php?commande=ok');
    exit();
}

if ($paiement['type_operation'] === 'modification_commande') {
    $commandes = lire_json('commandes.json');
    $utilisateurs = lire_json('utilisateurs.json');
    $donnees = $paiement['donnees'];

    foreach ($commandes as &$commande) {
        if ((int) $commande['id'] === (int) $donnees['commande_id']) {
            $commande['lignes'] = $donnees['lignes'];
            $commande['produit'] = $donnees['produit'];
            $commande['total'] = $donnees['nouveau_total'];

            if (!isset($commande['paiements']) || !is_array($commande['paiements'])) {
                $commande['paiements'] = [];
            }

            $commande['paiements'][] = [
                'date' => date('Y-m-d H:i'),
                'montant' => (float) $paiement['montant'],
                'type' => 'complement',
                'transaction' => $paiement['transaction']
            ];
            break;
        }
    }
    unset($commande);

    ecrire_json('commandes.json', $commandes);
    cybank_supprimer_paiement($token);
    ajouter_incident('paiement', 'Paiement complementaire CYBank accepte', '', (int) $paiement['client_id']);
    header('Location: detail_commande.php?id=' . (int) $donnees['commande_id'] . '&modif=ok');
    exit();
}

cybank_supprimer_paiement($token);
header('Location: panier.php?erreur=paiement_refuse');
exit();
