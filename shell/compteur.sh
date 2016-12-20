#!/bin/bash
# TODO : commenter chaque ligne pour expliquer le but
source `dirname $0`/env
$psql -t -d delain -U webdelain << EOF >> $logdir/compteurs.log 2>&1
select init_compteur();
select cree_stat();
select cron_dissip_monstre();
select cron_comptes_temp();
select cron_repousse_composants();
EOF

