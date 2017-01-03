# Les souterrains de Delain

## Projet
Après plus de dix ans d'existence, le jeu de rôle en ligne ["Les souterrains de Delain"](https://www.jdr-delain.net) devient opensource. 

## Contenu
Ce dépôt contient les informations nécessaires pour installer une instance de démarrage du jeu, et commencer les développements.
Il contient :

* La structure de la base de données
* Des données nécessaires au démarrage :
    * les étages
    * toutes les fonctions du jeu
* Le code php 


Il ne contient pas :

* Les données personnelles (comptes, personnages, ...) du jeu en cours

## Prérequis

* une machine Linux (Ubuntu 16.04 conseillée), avec accès au shell et au cron
* Postgres 9+ 
* php 5.5+
* apache 2.2+

Avec des modifications, le projet peut être installé sur d'autres distributions (Centos,...) et même sous windows. De même, un autre serveur web
qu'apache est possible (nginx), mais aucune aide ne pourra être apportée pour ces configurations.

## Philosophie

### Fonctionnelle
L'analyse du code source peut apprendre beaucoup de choses sur le jeu en cours (combinaisons de runes, sorts, cartes,...).
Il est demandé que ces informations ne soient pas retransmises en clair (de façon facilement lisibile) pour ne pas gâcher le plaisir des joueurs.

### Technique
Les souterrains de Delain est un jeu ancien, écrit par des amateurs, en fonction de leur temps libre.
A cause de cela, il est possible d'y trouver des failles de sécurité. Il est demandé de les signaler (ou mieux, de les corriger) plutôt que de les exploiter.

## Installation et documentation
La procédure d'installation se trouve dans le ["wiki"](/wiki) associé au projet




