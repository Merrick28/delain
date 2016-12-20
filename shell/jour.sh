#!/bin/bash
source `dirname $0`/env
fichier=$shellroot/liste_monstre.txt
$psql -t -d delain -U webdelain << EOF >> $logdir/journuit.log 2>&1
update etage set etage_affichage = 's5' where etage_numero = 0;
select mission_regenerer();
EOF

