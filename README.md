# ForgeOps

ForgeOps est une mini-plateforme interne de suivi d'incidents et de services pour équipe DevOps.

## 1. Installation en Dev

Pré-requis : PHP 8.2+, Composer, Docker.

```bash
git clone <url-du-repo>
cd forgeops
composer install
docker compose up -d
```

## 2. Configuration Base de données
La configuration se fait via le fichier .env (Dev) et .env.test (Test).

Fichier .env :
DATABASE_URL="postgresql://app_user:securepassword@127.0.0.1:5432/forgeops?serverVersion=15&charset=utf8"


Fichier .env.test :
DATABASE_URL="postgresql://app_user:securepassword@127.0.0.1:5432/forgeops_test?serverVersion=15&charset=utf8"


## 3. Commandes Migrations
Création des bases et exécution des migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate
```

## 4. Commande Tests
```bash
php bin/phpunit
```

## 5. Secrets GitHub nécessaires au déploiement
Pour que le CD (GitHub Actions) fonctionne vers le VPS, configurer ces secrets dans Settings > Secrets and variables > Actions :

VPS_HOST : L'adresse IP du serveur VPS
VPS_USER : L'utilisateur SSH (ex: debian, ubuntu)
VPS_SSH_KEY : La clé privée SSH (Ed25519 ou RSA)

### C'est terminé ! 🎉