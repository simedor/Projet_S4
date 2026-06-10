# CY Pizza

CY Pizza est notre projet de site de pizzeria en PHP. On l'a fait pour s'entrainer sur les bases du web : pages PHP, formulaires, sessions, fichiers JSON, un peu de JavaScript et plusieurs roles utilisateurs.

Le site permet de commander des pizzas en ligne, mais aussi de tester les interfaces admin, restaurateur et livreur.

## Lancer le projet

Le projet est prevu pour tourner avec XAMPP.

1. Mettre le dossier `CYPIZZA` dans `htdocs`.
2. Lancer Apache avec XAMPP.
3. Ouvrir `http://localhost/CYPIZZA/accueil.php`.

On n'utilise pas de base de donnees SQL. Les donnees sont dans le dossier `data/`, sous forme de fichiers JSON.

## Comptes de test

### Administrateurs

| Login | Mot de passe | A tester |
| --- | --- | --- |
| `admin1` | `admin123` | Gestion des utilisateurs, blocage/deblocage, codes promo |
| `admin2` | `admin456` | Deuxieme compte admin pour tester l'acces admin |

### Restaurateur

| Login | Mot de passe | A tester |
| --- | --- | --- |
| `resto1` | `resto123` | Suivi des commandes, stats rapides, ajout/modification de plats |

### Livreurs

| Login | Mot de passe | A tester |
| --- | --- | --- |
| `livreur1` | `livreur123` | Commandes en livraison et changement de statut |
| `livreur2` | `livreur456` | Deuxieme livreur, utile pour tester l'attribution |

### Clients

| Login | Mot de passe | Fidelite | A tester |
| --- | --- | --- | --- |
| `sara` | `client123` | Standard | Commande classique |
| `leo` | `client123` | Premium | Remise fidelite de 5 % |
| `ines` | `client123` | Standard | Autre compte client |
| `yanis` | `client123` | Standard | Autre compte client |
| `tony1` | `tony123` | VIP | Remise fidelite de 10 % |
| `Alex1` | `alex123` | Standard | Test avec code promo |

Certains comptes peuvent etre bloques si on teste l'administration. Dans ce cas, il faut se connecter avec un admin et les debloquer.

## Ce qu'on peut tester rapidement

- Se connecter en client, filtrer la carte par type : pizza, menu, boisson ou accompagnement.
- Ajouter des produits au panier.
- Appliquer un code promo, par exemple `PIZZA10`, `ALEX30` ou `SIM15`.
- Verifier que les remises fidelite changent le total du panier.
- Valider une commande avec CYBank.
- Modifier une commande encore en attente et verifier que le paiement complementaire calcule seulement la difference.
- Se connecter en restaurateur pour voir les commandes et les statistiques.
- Se connecter en livreur pour prendre en charge une livraison.
- Se connecter en admin pour bloquer/debloquer un utilisateur ou changer sa fidelite.

## Paiement CYBank

Pour le paiement de test, il faut utiliser :

- Numero de carte : `5555 1234 5678 9000`
- CVV : `555`
- Titulaire : n'importe quel nom
- Expiration : n'importe quelle date

Le site affiche le montant avant d'envoyer vers CYBank. Pour une modification de commande, il affiche aussi l'ancien total, le nouveau total et le montant restant a payer.

## Fonctionnalite ajoutee : codes promo

Dans l'interface admin, on peut creer des codes promo avec un pourcentage de reduction. Le client peut ensuite entrer ce code dans son panier. Si le code est valide, la reduction est appliquee au total.

On a aussi garde la remise fidelite :

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

Le projet est surtout fait pour une demonstration locale, pas pour une vraie mise en production.
