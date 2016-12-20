#!/bin/bash
source `dirname $0`/env
sortie=$shellroot/liste_monstre.txt
rm $sortie
$psql -U webdelain -d delain -f $shellroot/liste_monstre.sql -q -t -o $sortie
sed '/^$/d' $sortie > tt 
mv tt $sortie
