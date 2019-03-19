#!/bin/bash
source `dirname $0`/env
echo "===========================" >> ${shellroot}/livraison.log
echo "= LIVRAISONS SQL EN COURS =" >> ${shellroot}/livraison.log
for f in `find $livroot -type f| grep -v "initial_import.sql"|sort`; do
  livexist=`$shellroot/livraison_exists.sh $(basename $f)`
  if [ "$livexist" -eq "0" ];then
      echo "LIVRAISONS A TRAITER : $f" >> ${shellroot}/livraison.log
      $psql -A -q -t -d delain -U ${USERNAME} -f $f >> ${shellroot}/livraison.log 2>&1
      $psql -A -q -t -d delain -U ${USERNAME} << EOF >> ${shellroot}/livraison.log 2>&1
insert into livraisons (liv_fichier) values ('$(basename $f)');
EOF
fi
done
# livraison des fonctions
echo "= LIVRAISONS DES FONCTIONS =" >> ${shellroot}/livraison.log
for f in `find $livfunc -type f| sort`; do
      $psql -A -q -t -d delain -U ${USERNAME} -f $f >> ${shellroot}/livraison.log 2>&1
done