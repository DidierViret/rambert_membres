#!/bin/bash

# Changer les droits du répertoire
chown -R www-data:www-data /var/www/html
chmod -R 777 /var/www/html/cache

# Lancer le processus principal
exec "$@"