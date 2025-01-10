# AdopteUnDev - Plateforme de Mise en Relation Développeurs/Entreprises

## Description
AdopteUnDev est une plateforme web développée avec Symfony qui permet de mettre en relation les développeurs à la recherche d'opportunités professionnelles avec des entreprises qui recrutent.

## Fonctionnalités Principales
- Inscription et authentification des développeurs et entreprises
- Création et gestion de profils détaillés
- Publication et recherche d'offres d'emploi
- Système de matching basé sur les compétences

## 🚀 Installation avec Docker

### Prérequis
- Docker
- Docker Compose
- Git

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/Ayovo9/Projet-Symfony-AdopteUnDev.git
cd Projet-Symfony-AdopteUnDev
```

2. **Lancer les conteneurs Docker**
```bash
docker-compose up --build -d
```

3. **Attendre que les conteneurs soient prêts**
- L'application sera accessible sur : `http://localhost:8888`
- PhpMyAdmin sera accessible sur : `http://localhost:8081`
  - Utilisateur : `root`
  - Mot de passe : `root`

4. **Initialiser la base de données (première installation uniquement)**
```bash
# Créer le schéma de la base de données
docker-compose exec app php bin/console doctrine:schema:create

# Remplir la base de données avec des données de test

### 🔧 Configuration

Les services suivants sont configurés :
- **Application Symfony** : `http://localhost:8888`
- **Base de données MySQL**
  - Hôte : `database`
  - Port : `3306`
  - Base de données : `adopteundev`
  - Utilisateur : `root`
  - Mot de passe : `root`
- **PhpMyAdmin** : `http://localhost:8081`

### 🛠 Commandes utiles

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Voir les logs de l'application
docker-compose logs app

# Exécuter des commandes Symfony
docker-compose exec app php bin/console [commande]

# Accéder au shell du conteneur
docker-compose exec app bash
```

### 🔍 Debug

- Xdebug est configuré et prêt à être utilisé
- Les logs de l'application sont accessibles via `docker-compose logs app`
- En cas de problème de permissions : `docker-compose exec app chown -R www-data:www-data /var/www/html`


## 🤝 Contribution
Le projet est divisé en deux branches principales de développement :
- `dev-amevi` : Fonctionnalités développeurs
- `dev-victor` : Fonctionnalités entreprises


## Contacts
- Amevi : amevi.yovo@etudiant.univ-rennes.fr
- Victor : victor.degas@etudiant.univ-rennes.fr
