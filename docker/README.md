# Docker

Pour lancer Delain en docker (pour des fins de dev uniquement) :
- installez docker et docker-compose
- modifiez éventuellement les fichiers docker-compose.yml conf.php de ce répertoire. Par défaut, on pointesur localhost:9090, mais vous ppouvez changer cette destination
- lancer un docker-compose up -d

La base va mettre plusieurs minutes à se charger selon la puissance de votre machine.
Vous pouvez suivre l'évolution du chargement de la base de données en tapant la commande
```$xslt
docker logs -f delain_db
```

Une fois lancé, vous pouvez lancer l'interface web sur le port 9090, et vous connecter avec le user Admin, mot de passe admin.

## Données persistantes

Par défaut, à chaque redémarrage de la base de données, les données de la base sont détruites et recréées. 
Si vous souhaitez les conserver, il faudra :
- décommenter la ligne **/my/own/datadir:/var/lib/postgresql/data** du fichier docker-compose.yml
- remplacer /my/own/datadir par un répertoire local de votre machine

Dans ce cas, il vous faudra passer à la main les fichier qui seront poussés ultérieurement dans le répertoire /sql du projet

## Serveur web

Le container web contient un serveur apache/php7.3, configuré pour utiliser delain. Les dépendances sont déjà installées (l'image de base se trouve ici : https://hub.docker.com/repository/docker/delain/tests_unitaires).
L'utilitaire phpunit est également installé pour lancer les tests unitaires.

Le dockerfile qui a servi à la création de cette image se trouve à la racine du projet (fichier Dockerfile_buildtu)

## Données et tests unitaires

Vous pouvez modifier toutes les données en base à votre guise. Toutefois, pour lancer les tets unitaires, la base doit avoir ces éléments :
- un compte Admin/admin
- ce compte doit avoir un seul personnage, appelé Gildwen, dont le perso_cod est 1

Ces données sont automatiquement créées au démarrage de la base. Si vous ne prévoyez pas de lancer de tests unitaires sur cette base, vous pouvez les détruire.

Par défaut, les données web sont montées dans le répertoire /home/delain/delain. Si vous faites des modifications dans votre projet local, elles seront appliquées immédiatement et visible sur le localhost:9090


