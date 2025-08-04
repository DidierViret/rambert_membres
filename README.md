# rambert_membres
Gestion du fichier de membres du club Rambert

# Installation sur membres.rambert.ch
Pour migrer la base de données depuis le terminal SSH de l'hébergement Plesk :
```
cd membres.rambert.ch
/opt/plesk/php/8.3/bin/php spark migrate --all
```

# Trouble shooting
Si un message d'erreur de ce style apparaît dans le journal Plesk :
```
/var/www/vhosts/rambert.ch/membres.rambert.ch/public/.htaccess: Option FollowSymlinks not allowed here
```
Il faut aller dans l'interface d'administration Plesk, sous :

Hébergement et DNS > Apache & nginx

Enlever la coche en face de "Restreindre le suivi des liens symboliques".