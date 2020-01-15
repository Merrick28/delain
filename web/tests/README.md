# Tests unitaires

Dans ce répertoire se trouvent les tests unitaires pour Delain.

## Contraintes

La database sur laquelle on va lancer les tests unitaires doit:
* avoir un compte Admin/admin
* ce compte doit avoir un seul personnage, appelé Gildwen, dont le perso_cod est 1

Sans ces contraintes, les tests vont échouer.

## Lancement en docker

Si vous avez lancé une conf docker (répertoire /docker ce ce projet), vous pouvez lancer les script phpunit_docker.sh pour lancer les tests unitaires.

Cela va lancer un phpunit dans le container web, et tester ce répertoire.

## phpunit_docker-tu.sh

Ce fichier ne doit pas être lancé à la main. Il est uniquement lancé par Jenkins lors d'un noouveau commit.
Il est basé sur une base obligatoirement neuve, dans un container dont le nom est spécifique.