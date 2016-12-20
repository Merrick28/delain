logdir=/home/delain/logs
/usr/bin/psql -t -d delain -U webdelain << EOF >> $logdir/dissipation.log 2>&1
select dissipation_magique();
select cron_maj_idee();
select consolide_classement_concours();
EOF

