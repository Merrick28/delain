#!/bin/bash
######################################
# Refactor magie
######################################
INFILE=$(dirname $0)/../sql_init/initial_import.sql
START_FONCTION="CREATE FUNCTION public.dv_mg"
END_FONCTION="ALTER FUNCTION public.dv_mg"
COMMENT_FONCTION="COMMENT ON FUNCTION public.dv_mg"
OUTPUTDIR=$(dirname $0)/temp
TEMPFILE=$(dirname $0)/initial_import.sql
encours=0
nom_fonction=vide

while IFS= read -r line; do

  if [[ $line == ${START_FONCTION}* ]]; then
    # On est en début de fonction
    encours=1
    # découpage du nom
    nom_fonction=$(echo $line | awk '{print $3}' | awk -F. '{print $2}' | awk -F\( '{print $1}')
    echo "Fonction nom_fonction"
    echo "Ligne trouvée : $line"
  fi
  if [[ $encours == 1 ]]; then
    echo "$line" >>"${OUTPUTDIR}/${nom_fonction}.sql"

  fi
  if [[ $line == ${END_FONCTION}* ]]; then
    # a priori fin de la fonction
    encours=0
  fi

  if [[ $line == ${COMMENT_FONCTION}* ]]; then
    # cas particulier du commentaire
    echo "$line" >>"${OUTPUTDIR}/${nom_fonction}.sql"
  fi


done <"$INFILE"
