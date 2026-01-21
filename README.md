# rambert_membres
Gestion du fichier de membres du club Rambert

# Installation sur membres.rambert.ch
Pour migrer la base de données depuis le terminal SSH de l'hébergement Plesk :
```
cd membres.rambert.ch
/opt/plesk/php/8.3/bin/php spark migrate --all
```

## Trouble shooting
Si un message d'erreur de ce style apparaît dans le journal Plesk :
```
/var/www/vhosts/rambert.ch/membres.rambert.ch/public/.htaccess: Option FollowSymlinks not allowed here
```
Il faut aller dans l'interface d'administration Plesk, sous :

Hébergement et DNS > Apache & nginx

Enlever la coche en face de "Restreindre le suivi des liens symboliques".

# Environnement de développement
L'environnement de développement est prévu pour travailler avec Docker. Il prévoit un container MariaDB et un container Apache ainsi que l'application phpMyAdmin dans un troisième container.

## Variables d'environnement
Un fichier "env_dist" se trouve à la racine du projet. Il faut en faire une copie et nommer cette copie ".env". Ce fichier sert à définir les variables d'environnement pour Docker.

Il faut définir :
```
# DB_ROOT_PASSWORD = root_password
# DB_NAME = rambert_db
# DB_USER = rambert_user
# DB_PASSWORD = rambert_password
```

Un second fichier "env_dist" se trouve dans le dossier "ci-application". Il faut en faire une copie et nommer cette copie ".env". Ce fichier sert à définir les variables d'environnement pour l'application CodeIgniter.

Il faut définir :
```
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:80/public/'

database.default.hostname = mariadb
database.default.database = rambert_db
database.default.username = rambert_user
database.default.password = rambert_password
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

## Installation des librairies PHP avec Composer
Au préalable, le gestionnaire de librairies PHP "Composer" doit être installé sur le système (https://getcomposer.org/download/).

Même si les extensions PHP nécessaires sont prévue pour être installées dans le container Docker apache, il est possible que Composer signale des extensions PHP manquantes dans le système hôte. Pour éviter ce blocage, il faut les installer et/ou les activer dans l'installation PHP du système hôte. Les extensions nécessaires pour CodeIgniter sont décrites ici : https://www.codeigniter.com/user_guide/intro/requirements.html.

Dans un terminal :
```bash
cd ci-application
composer install
```

## Montage et démarrage des containers

Pour permettre la création du réseau de containers dans une version LIVE de Linux, il faut d'abord créer le réseau manuellement (pas nécessaire si Docker tourne sur un système d'exploitation installé):
```bash
sudo systemctl start docker
sudo docker network create --driver bridge app-network
```

Montage et démarrage des containers :
```bash
docker compose build
docker compose up
```

## Création de la structure de la base de données
Une fois que les containers sont démarrés, entrer dans la ligne de commande du container apache pour générer la structure de la base de données.

```bash
docker exec -it <container name> sh
php spark migrate --all
```