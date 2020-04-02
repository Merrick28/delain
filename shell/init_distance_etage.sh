#!/bin/bash
source $(dirname $0)/env

#IFS=$'\n'
START=0
for i in $(cat ./liste_etage); do
  while [[ $TEMPRESULT != "termine" ]]; do
    TEMPRESULT=$(
      $psql -A -q -t -d delain -U ${USERNAME} <<EOF
select remplissage_table_distance_etage_offset($i,$START);
EOF
    )
    echo "Etage = " $i
    echo "Resultat = " $TEMPRESULT
    echo "Start = " $START
    START=$((START + 500))
  done
  TEMPRESULT=encore
  START=0

done
