#!/bin/bash
source `dirname $0`/env
for f in `find $livroot -type f| grep -v "initial_import.sql"|sort`; do
  livexist=`$shellroot/livraison_exists.sh $(basename $f)`
  if [ "$livexist" -eq "0" ];then
      echo "LIVRAISONS A TRAITER : $f" >> `dirname $0`/livraison.log 2>&1
      $psql -A -q -t -d delain -U webdelain -f $f >> `dirname $0`/livraison.log 2>&1
      $psql -A -q -t -d delain -U webdelain << EOF >> `dirname $0`/livraison.log 2>&1
insert into livraisons (liv_fichier) values ('$(basename $f)');
EOF
  else
    echo "$f déjà traité" >> `dirname $0`/livraison.log 2>&1
  fi
done
# livraison des fonctions
for f in `find $livfunc -type f| sort`; do
      echo "LIVRAISONS A TRAITER : $f" >> /dev/null 2>`dirname $0`/livraison.log
      $psql -A -q -t -d delain -U webdelain -f $f >> `dirname $0`/livraison.log 2>&1
done