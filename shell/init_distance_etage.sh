#!/bin/bash
source $(dirname $0)/env

#IFS=$'\n'
START=0
for i in $(cat ./liste_etage); do
  echo $i
  while [[ $TEMPRESULT != "termine" ]]; do
    TEMPRESULT=$(
      $psql -A -q -t -d delain -U ${USERNAME} <<EOF
select remplissage_table_distance_etage_offset($i,$START);
EOF
    )
    echo "Resultat = " $TEMPRESULT
    START=$((START + 100))
  done
  TEMPRESULT=encore

done
