#!/bin/bash
source `dirname $0`/env
$psql -t -d delain -U webdelain << EOF >> $logdir/purge.log 2>&1
select purge_evenements(0);
select purge_mes(0);
--select joueur_inactif(0);
--select compte_inactif(0);
select nettoie(0);
select nettoie_automap();
delete from logs_ia where now() - lia_date > '2 days'::interval;
update objet_position set pobj_pos_cod = pos_aleatoire_ref( -9 ) where pobj_obj_cod in (select obj_cod from objets, objet_position, murs, positions where obj_cod = pobj_obj_cod and pobj_pos_cod = mur_pos_cod and mur_pos_cod = pos_cod and mur_creusable = 'N');
delete from fonction_specifique where fonc_date_limite < (now() - '15 days'::interval) AND fonc_date_limite IS NOT NULL;
EOF

