#!/bin/bash
##############
source `dirname $0`/env
echo "Nettoyage de la base en cours" >> $webroot/stop_jeu
date >> $logdir/result_vacuum.log
$psql -U webdelain -q -t -d delain << EOF >>$logdir/result_vacuum.log 2>&1
delete from logs_ia where lia_date  < (select now() - '15 days'::interval);
delete from ligne_evt where levt_date  < (select now() - '15 days'::interval);
delete from log_objet where llobj_date < (select now() - '15 days'::interval);
delete from histo_log where hlog_date < (select now() - '90 days'::interval);
delete from compte_ip where icompt_compt_date < (select now() - '90 days'::interval);
delete from perso_tableau_chasse where not exists (select 1 from perso where perso_cod = ptab_perso_cod);
delete from perso_compte_monstre where pcm_pcompt_cod not in (select pcompt_cod from perso_compte);
delete from bonus where bonus_perso_cod not in (select perso_cod from perso);
analyze;
EOF
date >> $logdir/result_vacuum.log
rm -f $webroot/stop_jeu