logdir=/home/delain/logs
date >> $logdir/cree_monstre.log
/usr/bin/psql -t -d delain -U delainadm << EOF | grep -v ' - 0$' >> $logdir/cree_monstre.log 2>&1 
select bouge_portail();
select init_stock_mag();
select cree_monstre_auto(0);
select cron_repousse_composants();
EOF
