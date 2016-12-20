#!/bin/bash
# Ce script, prévu pour tourner toutes les nuits en heures creuses, 
# a pour objectif de comparer la version en production avec celle en oeuvre 
# dans git, et de mettre à jour le svn en conséquence si nécessaire.
SHELLPATH=/home/delain/shell
WWWPATH=/home/delain/public_html
DATE=`date`

pg_dump -U delainadm -s delain > /home/delain/public_html/delain.sql
cd $WWWPATH
git add --all .
git commit -m "Quotidien ${DATE}"
git push -u origin master

cd $SHELLPATH
git add --all .
git commit -m "Quotidien ${DATE}"
git push -u origin master

