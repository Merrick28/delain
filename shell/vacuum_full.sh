#!/bin/bash
#############
source `dirname $0`/env
echo "Nettoyage approfondi de la base en cours" >> $webroot/stop_jeu
$psql -U webdelain -q -t -d delain << EOF >> $logdir/result_vacuum.log 2>&1
vacuum full;
EOF
rm -f $webroot/stop_jeu