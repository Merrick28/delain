#!/bin/bash
source `dirname $0`/env
date >> $logdir/recycle_monstres.log
$psql -t -d delain -U webdelain << EOF >> $logdir/recycle_monstres.log 2>&1
select case when tue_perso_final(perso_cod, perso_cod) = '' then perso_cod else perso_cod end from perso where perso_pnj != 1 and perso_type_perso = 2 and perso_dcreat + '1 month'::interval < now() and perso_nom like '%n°%' and not exists (select 1 from ligne_evt where levt_tevt_cod not in (2, 54) and levt_perso_cod1 = perso_cod) and not exists (select 1 from perso_compte where pcompt_perso_cod = perso_cod) limit 200;
EOF

