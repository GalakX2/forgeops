# ForgeOps

ForgeOps est une mini-plateforme interne de suivi d'incidents et de services pour équipe DevOps, réalisée avec Symfony 6/7.

## 1. Pré-requis techniques

*   **PHP 8.4** minimum (requis par les dépendances)
*   Composer
*   Docker (pour la base de données PostgreSQL)
*   Symfony CLI (optionnel, mais recommandé)

## 2. Installation en local (Dev)

1.  Cloner le projet :
    ```bash
    git clone <url-du-repo>
    cd forgeops
    ```

2.  Installer les dépendances PHP :
    ```bash
    composer install
    ```

3.  Lancer la base de données (PostgreSQL) via Docker :
    ```bash
    docker compose up -d
    ```

## 3. Configuration Base de Données

Le projet est pré-configuré pour fonctionner avec le conteneur Docker fourni.

*   **Fichier `.env` (Dev) :**
    `DATABASE_URL="postgresql://app_user:securepassword@127.0.0.1:5432/forgeops?serverVersion=15&charset=utf8"`

*   **Fichier `.env.test` (Test) :**
    `DATABASE_URL="postgresql://app_user:securepassword@127.0.0.1:5432/forgeops?serverVersion=15&charset=utf8"`

## 4. Initialisation (Migrations)

Une fois Docker lancé, exécutez ces commandes pour créer les bases de données (Dev et Test) et jouer les migrations :

```bash
# Base de DEV
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Base de TEST (Obligatoire pour le step suivant)
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

## 5. Lancer les Tests
Les tests fonctionnels valident les règles métier et les scénarios d'incidents.

```Bash
php bin/phpunit
```

## 6. Déploiement (CI/CD)
Le projet dispose d'un workflow GitHub Actions complet :

CI : Lance les tests PHPUnit sur chaque Push/Pull Request.
CD : Déploie automatiquement sur le VPS lors d'un push sur la branche main.

### Secrets GitHub requis
Pour que le déploiement fonctionne, les secrets suivants doivent être configurés dans le dépôt GitHub :

* VPS_HOST : IP du serveur.
* VPS_USER : Utilisateur SSH (ex: jordan).
* VPS_SSH_KEY : Clé privée SSH pour la connexion.

### Structure sur le VPS
Le déploiement s'attend à l'arborescence suivante sur le serveur :
/home/<VPS_USER>/apps/forgeops/current

## 7. Accès Production
Une fois déployé, l'application est accessible via l'IP du VPS :
http://<IP_DU_VPS>