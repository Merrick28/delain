#!/bin/sh
source `dirname $0`/env

#IFS=$'\n'
for i in $(cat ./liste_etage)
do
echo $i
$psql -A -q -t -d delain -U ${USERNAME} << EOF
select remplissage_table_distance_etage($i);
EOF


done

