# 🐾 SitMyPet - Plateforme de garde d'animaux

Application web de mise en relation entre propriétaires d'animaux et gardiens de confiance, développée avec Symfony 7.1.

## 🎯 À propos du projet

SitMyPet est une plateforme moderne qui facilite la garde d'animaux de compagnie en connectant :
- **Propriétaires** : qui cherchent des gardiens fiables pour leurs compagnons
- **Gardiens** : qui souhaitent s'occuper d'animaux durant l'absence de leurs propriétaires
- **Administrateurs** : qui supervisent et gèrent l'ensemble de la plateforme

---

## ✨ Fonctionnalités

### 🏠 Espace Propriétaire
- Création et gestion du profil avec photo
- Gestion des animaux de compagnie
- Publication d'annonces de garde
- Recherche de gardiens disponibles
- Messagerie avec les gardiens
- Suivi des réservations

### 🤝 Espace Gardien
- Création et gestion du profil avec photo
- Navigation des annonces disponibles
- Postulation aux demandes de garde
- Gestion des réservations acceptées
- Communication avec les propriétaires
- Historique des gardes effectuées

### 🛡️ Panneau Administrateur
- **Dashboard** : Statistiques en temps réel (utilisateurs actifs, propriétaires, gardiens)
- **Gestion des utilisateurs** :
  - CRUD complet (Create, Read, Update, Delete)
  - Upload et gestion des photos de profil
  - Recherche et filtres avancés (par rôle, statut)
  - Activation/désactivation de comptes
  - Soft delete avec possibilité de restauration
- **Gestion des animaux** (à venir)
- **Gestion des annonces** (à venir)
- **Modération des avis** (à venir)
- **Gestion des réservations** (à venir)

---

## 🗄️ Base de données

### Tables principales

| Table | Description |
|-------|-------------|
| **utilisateurs** | Informations des utilisateurs (Admin, Propriétaire, Gardien) avec photos de profil |
| **animaux** | Profils des animaux (race, âge, caractéristiques) |
| **annonces** | Demandes de garde publiées par les propriétaires |
| **reservations** | Réservations confirmées entre propriétaires et gardiens |
| **messages** | Système de messagerie interne |
| **avis** | Évaluations et commentaires |

### Schéma de la table `utilisateurs`
```sql
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) UNIQUE NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    adresse TEXT,
    role VARCHAR(50) NOT NULL,
    image_name VARCHAR(255),
    image_size INT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    deleted_at DATETIME
);
```

---

## 🛠️ Technologies utilisées

### Backend
- **Framework** : Symfony 7.1
- **Langage** : PHP 8.2+
- **ORM** : Doctrine
- **Sécurité** : Symfony Security Bundle
- **Upload de fichiers** : VichUploaderBundle
- **Validation** : Symfony Validator

### Frontend
- **Template Engine** : Twig
- **CSS Framework** : Bootstrap 5.3
- **Icons** : Font Awesome 6.5
- **JavaScript** : Vanilla JS

### Base de données
- **SGBD** : MySQL 8.0
- **Migrations** : Doctrine Migrations

---

## 📦 Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- Symfony CLI
- MySQL 8.0
- Node.js & npm (pour les assets)
- XAMPP ou serveur web équivalent

### Étapes d'installation
```bash
# 1. Cloner le projet
git clone https://github.com/ines272/PIDEV.git
cd PIDEV

# 2. Installer les dépendances PHP
composer install

# 3. Configurer la base de données
# Éditez le fichier .env et configurez votre DATABASE_URL
DATABASE_URL="mysql://root:@127.0.0.1:3306/sitmypet?serverVersion=8.0&charset=utf8mb4"

# 4. Créer la base de données
php bin/console doctrine:database:create

# 5. Exécuter les migrations
php bin/console doctrine:migrations:migrate

# 6. Créer un compte administrateur
php bin/console app:create-admin

# 7. Créer le dossier pour les uploads
mkdir -p public/uploads/users

# 8. Lancer le serveur de développement
symfony server:start
```

---

## 🌐 URLs principales

| Route | Description | Accès |
|-------|-------------|-------|
| `/` | Page d'accueil | Public |
| `/login` | Connexion | Public |
| `/register` | Inscription | Public |
| `/proprietaire/dashboard` | Espace propriétaire | Propriétaire |
| `/gardien/dashboard` | Espace gardien | Gardien |
| `/admin/dashboard` | Dashboard administrateur | Admin |
| `/admin/utilisateurs/` | Gestion des utilisateurs | Admin |

---

## 👤 Comptes de test

### Administrateur
- **Email** : admin@sitmypet.com
- **Mot de passe** : Admin@1234

### Propriétaire
- **Email** : jean.proprietaire@test.com
- **Mot de passe** : Test@1234

### Gardien
- **Email** : marie.gardien@test.com
- **Mot de passe** : Test@1234

---

## 📁 Structure du projet
```
PIDEV/
├── config/                 # Configuration Symfony
│   ├── packages/
│   │   ├── security.yaml
│   │   └── vich_uploader.yaml
│   └── routes.yaml
├── migrations/            # Migrations de base de données
├── public/
│   ├── uploads/
│   │   └── users/        # Photos de profil
│   └── index.php
├── src/
│   ├── Controller/
│   │   ├── Admin/        # Controllers admin
│   │   ├── SecurityController.php
│   │   ├── ProprietaireController.php
│   │   └── GardienController.php
│   ├── Entity/
│   │   └── User.php
│   ├── Form/
│   │   ├── RegistrationType.php
│   │   └── UserType.php
│   └── Repository/
│       └── UserRepository.php
├── templates/
│   ├── admin/            # Templates admin
│   ├── security/         # Login & Register
│   ├── proprietaire/     # Dashboard propriétaire
│   └── gardien/          # Dashboard gardien
├── .env                  # Configuration environnement
├── composer.json
└── README.md
```

---

## 🔐 Système de rôles

### Hiérarchie des rôles
```php
ROLE_ADMIN
├── Accès complet au panneau d'administration
├── Gestion des utilisateurs
├── Modération du contenu
└── Statistiques globales

ROLE_PROPRIETAIRE
├── Gestion de son profil
├── Gestion de ses animaux
├── Publication d'annonces
└── Messagerie

ROLE_GARDIEN
├── Gestion de son profil
├── Consultation des annonces
├── Postulation aux gardes
└── Messagerie
```

---

## 📸 Gestion des photos de profil

### Fonctionnalités
- Upload de photos (JPG, PNG, WEBP)
- Taille maximale : 2MB
- Renommage automatique pour éviter les conflits
- Suppression automatique des anciennes photos
- Génération automatique d'avatars si pas de photo
- Prévisualisation en temps réel avant upload

### Configuration
```yaml
# config/packages/vich_uploader.yaml
vich_uploader:
    db_driver: orm
    mappings:
        user_images:
            uri_prefix: /uploads/users
            upload_destination: '%kernel.project_dir%/public/uploads/users'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
            delete_on_update: true
            delete_on_remove: true
```

---

## 🧪 Tests
```bash
# Vérifier que tout fonctionne
php bin/console about

# Lister toutes les routes
php bin/console debug:router

# Valider le schéma de base de données
php bin/console doctrine:schema:validate

# Vider le cache
php bin/console cache:clear
```

---

## 🚀 Déploiement en production

### 1. Configuration de l'environnement
```bash
# Passer en mode production
APP_ENV=prod
APP_DEBUG=0

# Optimiser l'autoloader
composer install --no-dev --optimize-autoloader

# Vider et réchauffer le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### 2. Sécurité

- Changer `APP_SECRET` dans `.env`
- Utiliser HTTPS
- Configurer les permissions des dossiers
- Désactiver les outils de debug

### 3. Performance
```bash
# Compiler les assets
npm run build

# Optimiser les images
# Activer le cache HTTP
# Configurer un CDN pour les uploads
```

---

## 🤝 Contribution

Ce projet est développé dans un cadre éducatif (PIDEV - Projet Intégré de Développement).

### Développeurs
- **Gestion Utilisateurs** : Votre nom
- **Autres modules** : À compléter

---

## 📝 Commandes utiles
```bash
# Créer une nouvelle entité
php bin/console make:entity

# Créer un nouveau controller
php bin/console make:controller

# Créer un nouveau formulaire
php bin/console make:form

# Créer une migration
php bin/console make:migration

# Créer un utilisateur admin
php bin/console app:create-admin

# Voir les logs en temps réel
php bin/console server:log
```

---

## 📄 License

Ce projet est développé dans un cadre éducatif à l'**ESPRIT** (École Supérieure Privée d'Ingénierie et de Technologies).

---

## 📞 Support

Pour toute question ou problème :
- Ouvrir une issue sur GitHub
- Contacter l'équipe de développement

---

## 🎓 Contexte académique

**Projet** : PIDEV (Projet Intégré de Développement)  
**Année** : 2025-2026  
**Institution** : ESPRIT  
**Framework** : Symfony 7.1  
**Objectif** : Application web complète de gestion de garde d'animaux

---

## 🔄 Changelog

### Version 1.0.0 (Février 2026)
- ✅ Système d'authentification complet (Login/Register)
- ✅ Gestion des utilisateurs (CRUD complet)
- ✅ Upload de photos de profil
- ✅ Dashboards par rôle (Admin, Propriétaire, Gardien)
- ✅ Recherche et filtres avancés
- ✅ Soft delete avec restauration
- 🔜 Gestion des animaux (à venir)
- 🔜 Système d'annonces (à venir)
- 🔜 Réservations (à venir)
- 🔜 Messagerie (à venir)

---

**Développé avec ❤️ et Symfony 🎵**
