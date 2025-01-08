# AdopteUnDev - Plateforme de Mise en Relation Développeurs/Entreprises

## Description
AdopteUnDev est une plateforme web développée avec Symfony qui permet de mettre en relation les développeurs à la recherche d'opportunités professionnelles avec des entreprises qui recrutent.

## Fonctionnalités Principales
- Inscription et authentification des développeurs et entreprises
- Création et gestion de profils détaillés
- Publication et recherche d'offres d'emploi
- Système de matching basé sur les compétences

## 🚀 Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL ou MariaDB
- Node.js et npm

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/Ayovo9/Projet-Symfony-AdopteUnDev.git
cd Projet-Symfony-AdopteUnDev
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Installer les dépendances JavaScript**
```bash
npm install
npm run build
```

4. **Configurer la base de données**
- Copier le fichier .env en .env.local
- Modifier la ligne DATABASE_URL avec vos informations de connexion

5. **Créer la base de données et les tables**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. **Charger les données de test (optionnel)**
```bash
php bin/console doctrine:fixtures:load
```

7. **Démarrer le serveur**
```bash
symfony server:start
```

Le site sera accessible à l'adresse : `http://localhost:8000`

## 👥 Comptes de test

### Compte Développeur
- Email : dev@test.com
- Mot de passe : password123

### Compte Entreprise
- Email : company@test.com
- Mot de passe : password123

## 🛠 Fonctionnalités

### Pour les Développeurs
- Création et gestion de profil
- Système de matching avec les offres
- Gestion des favoris

### Pour les Entreprises
- Gestion du profil entreprise
- Publication d'offres d'emploi
- Recherche de développeurs

## 🤝 Contribution
Le projet est divisé en deux branches principales de développement :
- `dev-amevi` : Fonctionnalités développeurs
- `dev-victor` : Fonctionnalités entreprises

## Structure des Branches
- `main` : Version stable de production
- `dev-amevi` : Branche de développement pour Amevi
- `dev-victor` : Branche de développement pour Victor

## Workflow Git
1. Toujours créer une nouvelle branche à partir de main pour chaque fonctionnalité
2. Faire des commits réguliers avec des messages descriptifs
3. Pousser les modifications sur sa branche de développement
4. Créer une Pull Request vers main quand la fonctionnalité est prête
5. Faire valider la PR par l'autre développeur avant de merger

## Contacts
- Amevi : amevi.yovo@etudiant.univ-rennes.fr
- Victor : victor.degas@etudiant.univ-rennes.fr
