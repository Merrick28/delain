#!/bin/bash
source $(dirname $0)/env

#IFS=$'\n'
START=0
for i in $(cat ./liste_etage); do
  echo "################"
  echo "Etage = " $i
  while [[ $TEMPRESULT != "termine" ]]; do
    echo "Start = " $START
    TEMPRESULT=$(
      $psql -A -q -t -d delain -U ${USERNAME} <<EOF
select remplissage_table_distance_etage_offset($i,$START);
EOF
    )

    echo "Resultat = " $TEMPRESULT " sur etage " $i
    echo "--"

    START=$((START + 50))
  done
  TEMPRESULT=encore
  START=0

done
