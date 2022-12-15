#!/bin/bash
source `dirname $0`/env
$psql -t -d delain -U webdelain << EOF >> /home/delain/logs/journuit.log 2>&1
update etage set etage_affichage = 's6' where etage_numero = 0;
select cron_remplissage_cachette ();
select action_derniere_taverne ();
EOF
