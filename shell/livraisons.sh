#!/bin/bash
source `dirname $0`/env
for f in `find $livroot -type f -maxdepth 1| grep -v "initial_import.sql"`; do
  livexist=`$shellroot/livraison_exists.sh $(basename $f)`
  if [ "$livexist" -eq "0" ];then
    echo "$f déjà traité"
  else
    $psql -A -q -t -d delain -U webdelain -f $f
    $psql -A -q -t -d delain -U webdelain << EOF
insert into livraisons (liv_fichier) values ($(basename $f));
EOF
  fi
done