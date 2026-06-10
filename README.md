# CY Pizza

CY Pizza est notre projet de site de pizzeria. Le site permet de commander des pizzas en ligne, mais aussi de tester les interfaces admin, restaurateur et livreur. On est le groupe formé par : Siméon DORNINGER, Alexandre GODINEAU et Charbel ELIAS (MEF2-F).

## Lancer le projet

Le projet est prévu pour tourner avec XAMPP.

1. Mettre le dossier `CYPIZZA` dans `htdocs`.
2. Lancer Apache avec XAMPP.
3. Ouvrir `http://localhost/CYPIZZA/accueil.php`.

On n'utilise pas de base de données SQL. Les données sont dans le dossier `data/`, sous forme de fichiers JSON.

## Comptes de test

### Administrateurs

| Login | Mot de passe |
| --- | --- |
| `admin1` | `admin123` |
| `admin2` | `admin456` |

### Restaurateur

| Login | Mot de passe |
| --- | --- |
| `resto1` | `resto123` |

### Livreurs

| Login | Mot de passe |
| --- | --- |
| `livreur1` | `livreur123` |
| `livreur2` | `livreur456` |

### Clients

| Login | Mot de passe | Fidélité |
| --- | --- | --- |
| `sara` | `client123` | Standard |
| `leo` | `client123` | Premium |
| `ines` | `client123` | Standard |
| `yanis` | `client123` | Standard |
| `tony1` | `tony123` | VIP |
| `Alex1` | `alex123` | Standard |

Certains comptes peuvent être bloqués. Dans ce cas, il faut se connecter avec un admin et les débloquer.

## Ce qu'on peut tester rapidement

- Se connecter en client, filtrer la carte par type : pizza, menu, boisson ou accompagnement.
- Ajouter des produits au panier.
- Appliquer un code promo, par exemple `PIZZA10`, `ALEX30` ou `SIM15`.
- Vérifier que les remises fidélité changent le total du panier.
- Valider une commande avec CYBank.
- Modifier une commande encore en attente et vérifier que le paiement complémentaire calcule seulement la différence.
- Se connecter en restaurateur pour voir les commandes et les statistiques.
- Se connecter en livreur pour prendre en charge une livraison.
- Se connecter en admin pour bloquer/débloquer un utilisateur ou changer sa fidélité.

## Paiement CYBank

Pour le paiement de test, il faut utiliser :

- Numéro de carte : `5555 1234 5678 9000`
- CVV : `555`
- Titulaire : n'importe quel nom
- Expiration : n'importe quelle date

Le site affiche le montant avant d'envoyer vers CYBank. Pour une modification de commande, il affiche aussi l'ancien total, le nouveau total et le montant restant à payer.

## Fonctionnalité ajoutée : codes promo

Dans l'interface admin, on peut créer des codes promo avec un pourcentage de réduction. Le client peut ensuite entrer ce code dans son panier. Si le code est valide, la réduction est appliquée au total.
Code promo qui éxiste déja: PIZZA10, ALEX30 ou SIM15.

On a aussi gardé la remise fidélité :

- Standard : 0 %
- Premium : 5 %
- VIP : 10 %

## Organisation rapide

- `accueil.php` : page d'accueil.
- `presentation.php` : carte des produits.
- `panier.php` : panier et validation de commande.
- `profil.php` : profil client et historique.
- `commande.php` : interface restaurateur.
- `livraison.php` : interface livreur.
- `administrateur.php` et `utilisateur.php` : interface admin.
- `traitements/` : fichiers PHP qui traitent les actions.
- `Includes/` : fonctions communes et CYBank.
- `data/` : stockage JSON.
- `Images/` : images des produits et logo.
