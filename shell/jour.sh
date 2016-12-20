#!/bin/sh
repertoire=/home/delain/shell
logdir=/home/delain/logs
tmp_sortie=$repertoire/tmp_resultat
sortie=$repertoire/resultat
fichier=$repertoire/liste_monstre.txt
/usr/bin/psql -t -d delain -U webdelain << EOF >> /home/delain/logs/journuit.log 2>&1
update etage set etage_affichage = 's5' where etage_numero = 0;
select mission_regenerer();
EOF

