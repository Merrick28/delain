#!/bin/bash
source `dirname $0`/env
for f in `find $livroot -type f| grep -v "initial_import.sql"|sort`; do
  livexist=`$shellroot/livraison_exists.sh $(basename $f)`
  if [ "$livexist" -eq "0" ];then
      echo "LIVRAIONS A TRAITER : $f"
      $psql -A -q -t -d delain -U webdelain -f $f
      $psql -A -q -t -d delain -U webdelain << EOF
insert into livraisons (liv_fichier) values ('$(basename $f)');
EOF
  else
    echo "$f déjà traité"
  fi
done
# livraison des fonctions
for f in `find $livfunc -type f| sort`; do
      echo "LIVRAIONS A TRAITER : $f"
      $psql -A -q -t -d delain -U webdelain -f $f
done