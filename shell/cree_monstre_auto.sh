#!/bin/bash
# TODO : commenter chaque ligne pour expliquer le but
source `dirname $0`/env
date >> $logdir/cree_monstre.log
$psql -t -d delain -U webdelain << EOF | grep -v ' - 0$' >> $logdir/cree_monstre.log 2>&1
select bouge_portail();
select init_stock_mag();
select cree_monstre_auto(0);
select cron_repousse_composants();
EOF
