#!/bin/bash
source `dirname $0`/env_docker
echo "===========================" 
echo "= LIVRAISONS SQL EN COURS =" 
for f in `find $livroot -type f| grep -v "initial_import.sql"|grep -v ".gitignore" |sort`; do
      echo "LIVRAISONS A TRAITER : $f" 
      $psql -A -q -t -d delain -U ${USERNAME} -f $f 
      $psql -A -q -t -d delain -U ${USERNAME} << EOF 
insert into livraisons (liv_fichier) values ('$(basename $f)');
EOF
done
# livraison des fonctions
echo "= LIVRAISONS DES FONCTIONS =" 
for f in `find $livfunc -type f| sort`; do
      $psql -A -q -t -d delain -U ${USERNAME} -f $f 
done
