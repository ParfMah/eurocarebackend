# 🌍 EuroCare Humanitaire — Plateforme CMS Humanitaire

**Plateforme web complète, CMS multipages, gestion des dons, bénéficiaires et partenaires.**

---

## 🏗️ Stack technique

| Couche | Technologie |
|--------|-------------|
| Frontend | HTML5, CSS3 Vanilla, JavaScript Vanilla |
| Backend | PHP 8.1+ natif (sans framework) |
| Base de données | MySQL 8.0+ |
| Architecture | MVC, Front Controller, Singleton DB |
| Sécurité | CSRF, XSS, SQLi, bcrypt, sessions sécurisées |

---

## 📁 Structure du projet

```
eurocare-humanitaire/
├── app/
│   ├── config/
│   │   ├── config.php          # Configuration principale
│   │   ├── database.php        # Paramètres MySQL
│   │   └── constants.php       # Constantes métier
│   ├── core/
│   │   ├── Database.php        # Wrapper PDO Singleton
│   │   ├── Router.php          # Routeur URL propre
│   │   ├── Session.php         # Sessions sécurisées + CSRF
│   │   ├── Auth.php            # Authentification + rôles
│   │   ├── Security.php        # XSS, validation, uploads
│   │   ├── Logger.php          # Journal d'audit
│   │   └── Helpers.php         # Utilitaires globaux
│   ├── controllers/
│   │   ├── PublicController.php
│   │   ├── AuthController.php
│   │   ├── BlogController.php
│   │   ├── DonController.php
│   │   ├── BeneficiaireController.php
│   │   ├── PartenaireController.php
│   │   ├── DashboardController.php
│   │   ├── AdminController.php
│   │   └── ApiController.php
│   ├── models/                 # Modèles de données
│   └── views/
│       ├── layouts/
│       │   ├── main.php        # Layout principal
│       │   ├── auth.php        # Layout authentification
│       │   └── admin.php       # Layout administration
│       ├── public/             # Vues publiques
│       ├── auth/               # Vues authentification
│       ├── admin/              # Vues administration
│       ├── donor/              # Espace donateur
│       ├── beneficiary/        # Espace bénéficiaire
│       ├── partner/            # Espace partenaire
│       └── errors/             # Pages d'erreur
├── database/
│   └── schema.sql              # Schéma complet MySQL
├── public/
│   ├── index.php               # Point d'entrée unique
│   ├── .htaccess               # Réécriture URL + sécurité
│   └── assets/
│       ├── css/
│       │   ├── main.css        # Styles globaux
│       │   ├── nav.css         # Navigation + footer
│       │   ├── public.css      # Pages publiques
│       │   ├── auth.css        # Pages authentification
│       │   ├── admin.css       # Interface administration
│       │   └── dashboard.css   # Espaces utilisateurs
│       ├── js/
│       │   ├── main.js         # JavaScript principal
│       │   ├── nav.js          # Navigation interactive
│       │   ├── admin.js        # Tableaux et CMS admin
│       │   └── charts.js       # Graphiques transparence
│       ├── images/             # Images statiques
│       └── uploads/            # Fichiers uploadés (ignoré Git)
└── storage/
    ├── logs/                   # Logs applicatifs
    └── cache/                  # Cache statistiques
```

---

## ⚡ Installation locale

### Prérequis
- PHP **8.1+** avec extensions : `pdo_mysql`, `mbstring`, `gd`, `fileinfo`, `openssl`
- MySQL **8.0+** ou MariaDB **10.6+**
- Apache avec `mod_rewrite` activé (ou Nginx)

### Étapes

```bash
# 1. Cloner ou extraire le projet
git clone https://github.com/votre-repo/eurocare-humanitaire.git
# OU extraire l'archive ZIP

# 2. Créer la base de données
mysql -u root -p
> CREATE DATABASE eurocare_humanitaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> CREATE USER 'eurocare_user'@'localhost' IDENTIFIED BY 'MotDePasseSecurise123!';
> GRANT ALL PRIVILEGES ON eurocare_humanitaire.* TO 'eurocare_user'@'localhost';
> FLUSH PRIVILEGES;
> EXIT;

# 3. Importer le schéma SQL
mysql -u eurocare_user -p eurocare_humanitaire < database/schema.sql

# 4. Configurer les variables d'environnement
cp .env.example .env
# Éditer .env avec vos paramètres

# 5. Créer les dossiers de stockage
mkdir -p storage/logs storage/cache
mkdir -p public/assets/uploads/{profils,documents,articles,projets,partenaires,temoignages}
chmod -R 755 public/assets/uploads storage

# 6. Configurer Apache (Virtual Host)
# Pointer DocumentRoot vers le dossier public/
```

### Fichier `.env` à créer

```env
APP_ENV=development
DB_HOST=localhost
DB_PORT=3306
DB_NAME=eurocare_humanitaire
DB_USER=eurocare_user
DB_PASS=MotDePasseSecurise123!
```

### Identifiants administrateur par défaut

| Champ | Valeur |
|-------|--------|
| Email | `admin@eurocare-humanitaire.eu` |
| Mot de passe | `Admin@2024!` |

> ⚠️ **Changez immédiatement le mot de passe après la première connexion !**

---

## 🚀 Déploiement sur Render

### Option A : Web Service PHP (recommandée)

> Render supporte nativement PHP via des **Docker containers**.

#### 1. Créer le `Dockerfile`

```dockerfile
FROM php:8.2-apache

# Extensions PHP requises
RUN docker-php-ext-install pdo pdo_mysql mbstring gd

# Activer mod_rewrite Apache
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# DocumentRoot vers public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Permissions uploads
RUN mkdir -p /var/www/html/public/assets/uploads \
             /var/www/html/storage/logs \
             /var/www/html/storage/cache
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/public/assets/uploads /var/www/html/storage

# AllowOverride pour .htaccess
RUN echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Options -Indexes\n</Directory>' \
    >> /etc/apache2/apache2.conf

EXPOSE 80
```

#### 2. Créer le fichier `render.yaml`

```yaml
services:
  - type: web
    name: eurocare-humanitaire
    runtime: docker
    plan: starter
    envVars:
      - key: APP_ENV
        value: production
      - key: DB_HOST
        fromDatabase:
          name: eurocare-db
          property: host
      - key: DB_PORT
        fromDatabase:
          name: eurocare-db
          property: port
      - key: DB_NAME
        fromDatabase:
          name: eurocare-db
          property: database
      - key: DB_USER
        fromDatabase:
          name: eurocare-db
          property: user
      - key: DB_PASS
        fromDatabase:
          name: eurocare-db
          property: password

databases:
  - name: eurocare-db
    plan: starter
    databaseName: eurocare_humanitaire
```

#### 3. Étapes sur Render

1. Connectez-vous sur [render.com](https://render.com)
2. **New** → **Web Service**
3. Connectez votre repo GitHub/GitLab
4. Sélectionnez **Docker** comme environment
5. Ajoutez les variables d'environnement ci-dessus
6. **New** → **PostgreSQL** ou utilisez un MySQL externe

#### 4. MySQL externe sur Render

Render ne propose que PostgreSQL nativement. Pour MySQL, utilisez :
- **PlanetScale** (gratuit, MySQL compatible) → [planetscale.com](https://planetscale.com)
- **Railway** (MySQL gratuit) → [railway.app](https://railway.app)
- **Clever Cloud** (MySQL) → [clever-cloud.com](https://clever-cloud.com)

```env
# Exemple avec PlanetScale
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_NAME=eurocare_humanitaire
DB_USER=votre_user_planetscale
DB_PASS=votre_mdp_planetscale
```

#### 5. Importer la base de données

```bash
# Via PlanetScale CLI
pscale shell eurocare_humanitaire main < database/schema.sql

# Via Railway
railway run mysql < database/schema.sql

# Via tunnel SSH (si VPS)
mysql -h host -u user -p dbname < database/schema.sql
```

### Option B : VPS / Serveur dédié

```bash
# Sur serveur Ubuntu/Debian
apt-get update
apt-get install apache2 php8.2 php8.2-mysql php8.2-mbstring php8.2-gd php8.2-fileinfo mysql-server

# Configurer Apache Virtual Host
nano /etc/apache2/sites-available/eurocare.conf
```

```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    DocumentRoot /var/www/eurocare/public
    
    <Directory /var/www/eurocare/public>
        AllowOverride All
        Options -Indexes
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/eurocare_error.log
    CustomLog ${APACHE_LOG_DIR}/eurocare_access.log combined
</VirtualHost>
```

```bash
a2ensite eurocare.conf
a2enmod rewrite
systemctl reload apache2
```

---

## 🔒 Sécurité en production

```env
APP_ENV=production  # Désactive l'affichage des erreurs
```

**Checklist sécurité :**
- [ ] Changer le mot de passe admin par défaut
- [ ] Configurer HTTPS (Let's Encrypt)
- [ ] Définir `SESSION_SECURE=true`
- [ ] Configurer le SMTP (remplacer `mail()`)
- [ ] Sauvegardes automatiques MySQL
- [ ] Configurer `crontab` pour nettoyage sessions expirées
- [ ] Restreindre accès `storage/` et `app/`

---

## 📧 Configuration SMTP (optionnel)

Remplacer les appels `mail()` dans `AuthController.php` par PHPMailer :

```bash
composer require phpmailer/phpmailer
```

Ou utiliser des services gratuits :
- **Brevo** (ex-Sendinblue) — 300 emails/jour gratuit
- **Mailgun** — 5000 emails/mois gratuit
- **Resend** — 3000 emails/mois gratuit

---

## 👤 Rôles utilisateurs

| Rôle | Accès |
|------|-------|
| `admin` | Accès total : CMS, utilisateurs, statistiques, audit |
| `moderateur` | Gestion bénéficiaires, dons, articles, messages |
| `donateur` | Espace donateur, historique dons, reçus fiscaux |
| `beneficiaire` | Dossier social, documents, suivi des aides |
| `partenaire` | Profil organisation, recommandations |

---

## 📊 Fonctionnalités principales

- ✅ CMS complet (pages, articles, FAQ, témoignages)
- ✅ Gestion des dons (ponctuels + récurrents)
- ✅ Dossiers bénéficiaires avec workflow de validation
- ✅ Espace partenaires institutionnels
- ✅ Tableau de bord admin avec statistiques
- ✅ Journal d'audit complet (RGPD)
- ✅ Page transparence financière
- ✅ Messagerie interne sécurisée
- ✅ Notifications in-app temps réel
- ✅ Upload sécurisé de documents
- ✅ Authentification complète (email verify, 2FA ready)
- ✅ Responsive parfait mobile/tablette/desktop
- ✅ SEO optimisé

---

## 🐛 Support

Pour toute question : `contact@eurocare-humanitaire.eu`

---

*EuroCare Humanitaire — Version 1.0.0 — Tous droits réservés*
