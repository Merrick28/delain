#!/bin/bash
# Ce script, prévu pour tourner toutes les nuits en heures creuses, 
# a pour objectif de comparer la version en production avec celle en oeuvre 
# dans le svn, et de mettre à jour le svn en conséquence si nécessaire.

SVNPATH=~/svn/delain
LOGFILE=~/logs/update_svn.log

# shell et www ne sont pas dans la même arborescence entre ~ et svn.
# On doit donc les traiter séparément.
SHELLPATH=~/shell
SVNSHELLPATH=$SVNPATH/shell
APIPATH=~/public_html/api
SVNAPIPATH=$SVNPATH/api
WWWPATH=~/public_html/www
SVNWWWPATH=$SVNPATH/www

# Liste des fichiers de www à garder à jour.
FILELIST=$SHELLPATH/res/svnfiles.lst

# On commence par lister les fichiers en production

# include ~/shell -> svn/shell
# include ~/public_html/www -> svn/www
# export_struct.sh

# On met la version courante à jour, en défaisant les modifs précédentes
# pour éviter les conflits. (Personne ne devrait travailler sur le svn
# avec l'utilisateur delain de toutes façons.)
svn revert -R $SVNPATH >/dev/null
rm -Rf `svn status $SVNPATH | grep '^?' | awk '{ print $2; }'`
svn update $SVNPATH >/dev/null

# crontab
crontab -l > $SVNPATH/crontab

# shell files. 
cp -r $SHELLPATH/*.sh $SVNSHELLPATH
cp -r $SHELLPATH/*.sql $SVNSHELLPATH
cp -rT $SHELLPATH/res $SVNSHELLPATH/res

# api files
cp -r $APIPATH/* $SVNAPIPATH

# www files
for i in `cat $FILELIST | grep -v '^#'`
do
    cp -rT $WWWPATH/$i $SVNWWWPATH/$i
done

# Export de la bdd
echo "Sauvegarde de la base en cours" >> /home/delain/public_html/stop_jeu
/usr/bin/pg_dump -U delainadm -s delain > $SVNPATH/schema_base.sql
rm -f /home/delain/public_html/stop_jeu

svn add `svn status $SVNPATH | grep '^?' | awk '{ print $2; }'` >/dev/null 2>&1 
svn delete `diff -r $SVNWWWPATH $WWWPATH | fgrep "Seulement dans $SVNWWWPATH" | fgrep -v .svn | sed 's/: /\//' | sed 's/ /\n/g' | fgrep $SVNWWWPATH` >/dev/null 2>&1

svn commit $SVNPATH -m "Mise a jour automatique." >>$LOGFILE

