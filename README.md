# AdopteUnDev - Plateforme de Mise en Relation Développeurs/Entreprises

## Description
AdopteUnDev est une plateforme web développée avec Symfony qui permet de mettre en relation les développeurs à la recherche d'opportunités professionnelles avec des entreprises qui recrutent.

## Fonctionnalités Principales
- Inscription et authentification des développeurs et entreprises
- Création et gestion de profils détaillés
- Publication et recherche d'offres d'emploi
- Système de matching basé sur les compétences
- Messagerie entre développeurs et entreprises

## Installation

1. Cloner le projet
```bash
git clone https://github.com/Ayovo9/Projet-Symfony-AdopteUnDev.git
```

2. Installer les dépendances
```bash
composer install
```

3. Configurer la base de données dans .env
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/adopteundev?serverVersion=8.0.32&charset=utf8mb4"
```

4. Créer la base de données et exécuter les migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Charger les fixtures (données de test)
```bash
php bin/console doctrine:fixtures:load
```

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

## Répartition du Travail
### Amevi
- [Liste des fonctionnalités attribuées à Amevi]

### Victor
- [Liste des fonctionnalités attribuées à Victor]

## Convention de Nommage des Commits
- feat: Nouvelle fonctionnalité
- fix: Correction de bug
- docs: Documentation
- style: Formatage, semicolons manquants, etc.
- refactor: Refactorisation du code
- test: Ajout ou modification de tests

## Contacts
- Amevi : [Email]
- Victor : [Email]
