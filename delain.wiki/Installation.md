L'installation est décrite sur un serveur Ubuntu 16.04 LTS. Si vous installez sur un autre distribution (RedHat, FreBSD), ou une autre plateforme (Windows), il vous faudra adapter les instructions. Sous Windows, la partie "cron" ne sera pas utilisable. De même, si vous voulez utiliser un autre serveur web qu'apache, il faudra modifier la configuration des virtualHosts.

Pour toutes la suite de la documentation, on suppose que le projet est installé dans /home/delain.
Si ce n'est pas le cas, il vous faudra adapter tous les chemins en fonction.

# Prérequis

## Dépendances

Installation des packages postgresql, apache2, php, postfix (pour les envois de mail)

```
sudo apt install apache2 php libapache2-mod-php \
   php-pgsql postgresql postfix php-apcu git \
   memcached php-memcached
sudo a2enmod rewrite
```

## Création d'un utilisateur dédié

```
adduser delain
```

# Récupération du code source

Ces actions se font en tant qu'utilisateur delain 

```
su - delain 
git clone https://github.com/Merrick28/delain.git
chown -R delain:delain delain
chmod -R 777 delain/sql
```

Si vous avez fait un fork du projet, remplacez l'URL par celle de votre fork.

# Préparation de la base de données

## Droits d'accès

La base de données nécessite un groupe **delain** pour fonctionner, ainsi que d'un utilisateur **webdelain**. 
S'il n'est pas créé, vous devez le faire :

```
sudo su - postgres
psql
create group delain;
create role webdelain LOGIN INHERIT IN GROUP delain PASSWORD 'mypassword';
\q
exit
```

Il faut entrer les droits de connexion de l'utilisateur webdelain dans le fichier **/etc/postgresql/9.5/main/pg_hba.conf** en ajoutant la ligne

```
local   delain     webdelain md5
```
avant la ligne
```
local   all   all   peer
```
Puis redémarrer postgresql
```
sudo systemctl restart postgresql
```

## Importation des données

Par défaut, on installe dans une base de données "delain". Si vous souhaitez utiliser un autre nom pour la base de données, adaptez le code ci dessous (pour la partie createdb et psql).

```
su - postgres
createdb delain
psql delain < /home/delain/sql/initial_import.sql
exit
```

Une fois les premières données importées, il faut importer les différentiels créés depuis l'import initial

```
su - delain
for import in `find /home/delain/delain/sql -type f| grep -v "initial_import.sql"|sort`; do
psql -U webdelain -f $import -d delain
done
exit
```

Le mot de passe de l'utilisateur webdelain sera demandé autant de fois qu'il y a de fichier à importer.

# Configuration apache

## Changement d'utilisateur apache

Il faut éditer le fichier **/etc/apache2/envvars** pour changer l'utilisateur qui lance les process apache :

```
export APACHE_RUN_USER=delain
export APACHE_RUN_GROUP=delain
```

## Configuration simple (machine de dev seulement)

Si vous n'utilisez qu'une machine, et qu'un nom de domain, vous pouvez faire pointer la racine par défaut du serveur web sur **/home/delain/delain/web/www**
Pour cela, il faut changer la ligne **DocumentRoot** du fichier **/etc/apache2/sites-available/000-default.conf** 

```
DocumentRoot /home/delain/delain/web/www
<Directory />
  Options FollowSymLinks
  AllowOverride All
  Require all granted
</Directory>
```

Puis redémarrer apache

```
sudo systemctl restart apache2
```
# Configuration projet

A partir d'ici, on ne travaille qu'avec l'utilisateur **delain**

## configuration générale

```
cd delain/web/www/includes/
cp conf_dist.php conf.php
```
Editez le nouveau fichier pour correspondre à vos besoins :

* G_URL : URL d'accès à votre projet (http://localhost/ si vous avez suivi cette documentation)
* G_IMAGES : En général, c'est G_URL + 'images/' (soit http://localhost/images). En production, vous pouvez choisir de séparer les images sur un virtualhost séparé pour booster les performances (voir [ce post](https://www.sdewitte.net/2011/01/20110114optimisations-apachephppostgres-pour-site-a-haut-volume-partie-2-3/) pour plus d'explications)
* NOM_COOK : Obsolète, cette valeur n'est plus utilisée
* IMG_PATH : même valeur que G_IMAGES
* SERVER_PROD : doit rester à false. Variable présente pour des soucis de compatibilité
* SERVER_HOST : hôte postgresql (par défaut localhost)
* SERVER_USERNAME : user postgresql (par défaut webdelain)
* SERVER_PASSWORD : password pour l'utilisateur webdelain
* SERVER_DBNAME : nom de la database (par défaut delain)
* URL_API : Obsolète, cette valeur n'est plus utilisée
* SMTP_HOST : Hôte pour l'envoi des mails. Si vide, localhost sera utilisé. Vous pouvez mettre le serveur SMTP de votre provider pour les machines de développement.
* SMTP_PORT : Port pour l'envoi des mails par smtp. Non utilisé si SMTP_HOST est vide.
* SMTP_USER : Nom d'utilisateur pour l'envoi des mails par smtp. Non utilisé si SMTP_HOST est vide.
* SMTP_PASSWORD : Mot de passe pour l'envoi des mails par smtp. Non utilisé si SMTP_HOST est vide.



