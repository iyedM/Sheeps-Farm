# 🐑 FermeApp : Gestion Intelligente de Bergerie

![Symfony](https://img.shields.io/badge/Symfony-7.0-black?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/SQLite/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**FermeApp** est une solution SaaS moderne conçue spécifiquement pour la gestion, le suivi et le commerce de cheptel (moutons). Alliant une interface premium ultra-réactive et des fonctionnalités métier robustes, l'application permet de transformer une gestion traditionnelle en une exploitation pilotée par les données.

---

## 🚀 Fonctionnalités Clés

### 📊 Tableau de Bord (Dashboard)
Visualisez instantanément la santé de votre exploitation :
- **KPIs en Temps Réel** : Total du stock, moutons vendus, bénéfices et dépenses.
- **Dernières Activités** : Historique récent des entrées et sorties.
- **Grades & Alertes** : Suivi rapide de l'état global du cheptel.

### 🐑 Gestion du Cheptel (Moutons)
- **Fiches Individuelles** : Chaque animal possède son identifiant unique, sa race, son genre et son âge actuel (calculé dynamiquement).
- **Suivi Sanitaire** : Module complet pour enregistrer les **Vaccinations** et les **Observations (Infos)** pour chaque animal.
- **Statuts Prévisibles** : Filtres intelligents pour séparer le stock disponible des animaux déjà vendus.

### 🏠 Infrastructures (Granges)
- **Sectorisation** : Organisez vos animaux par bâtiment ou grange.
- **Capacité & Affectation** : Affectez chaque lot à une structure spécifique dès l'achat.

### 🧾 Logique de Commerce (Achats & Ventes)
Le projet intègre une double logique d'achat unique :
- **Achats de Stock (Stockage Auto)** : Lors d'un achat massif (ex: 50 têtes), le système génère **automatiquement** 50 fiches individuelles dans l'inventaire avec les caractéristiques saisies (Race, Âge, Genre, Grange).
- **Factures Achat (Commerce Externe)** : Pour l'achat-revente rapide sans stockage physique. Permet la saisie multi-lots (plusieurs races sur une facture) sans encombrer l'inventaire.
- **Factures Vente** : Édition de factures professionnelles lors de la vente d'animaux.

---

## 🛠️ Stack Technique (La Base)

- **Backend** : Symfony 7 / PHP 8.2+
- **ORM** : Doctrine (support SQLite & MySQL)
- **Frontend** : 
    - **Twig 3** : Pour le rendu dynamique.
    - **Bootstrap 5** : Système de grille robuste.
    - **Custom CSS Premium** : Design type SaaS (Glassmorphism, ombres douces, typographie moderne).
    - **AOS.js** : Animations fluides au défilement.
    - **Font: Inter & Outfit** : Pour une lisibilité haut de gamme.
- **Pagination** : KnpPaginator pour des chargements optimisés.

---

## 📂 Architecture des Données

- **Mouton** : Entité centrale (Race, Âge, Genre, Prix, Origine, Statut de vente, Grange).
- **FactureAchat** / **FactureVente** : Suivi transactionnel complet.
- **SousLotAchat** : Détail des lignes de facturation pour le commerce externe.
- **CommerceAchat** : Entité de stockage automatique par lots.
- **Vaccin / Infos** : Liaisons One-To-Many avec l'animal.

---

## 💻 Installation

1.  **Cloner le projet**
    ```bash
    git clone https://github.com/iyedM/Sheeps-Farm.git
    cd Sheeps-Farm
    ```
2.  **Installer les dépendances**
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Configurer l'environnement**
    Vérifiez votre fichier `.env` pour la base de données.
4.  **Initialiser la base de données**
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    ```
5.  **Lancer le serveur**
    ```bash
    symfony serve
    ```

---

## 🌟 Prochaines Étapes
- [ ] **Marketplace Feature** : Mise en relation directe entre vendeurs et acheteurs de bétail.
- [ ] **Génération PDF** : Exportation de factures d'achat/vente au format PDF.
- [ ] **Suivi Généalogique** : Arbre généalogique des naissances au sein de la ferme.

---
*Développé avec passion pour moderniser l'agriculture.* 🐏💎
