#!/bin/bash
source `dirname $0`/env
tmp_sortie=$shellroot/tmp_resultat
sortie=$shellroot/resultat
fichier=$shellroot/liste_monstre.txt
declare -i nb_ligne
nb_ligne=`wc -l<$fichier`
nb_ligne=$nb_ligne-1
declare -i num_ligne=1
declare -i num_traite
declare -i num_skip
num_traite=0
num_skip=0
echo $nb_ligne >> $logdir/ia_auto.log
$psql -U webdelain -d delain -q -t << EOF >> /dev/null
update perso set perso_actif = 'N'
where perso_type_perso = 1
and perso_actif = 'O'
and not exists 
(select 1 from perso_compte
where pcompt_perso_cod = perso_cod);
select reduc_compt_pvp();
\q
EOF
while read monstre
do
echo "Lancement de $monstre"
if [ ! -f /home/delain/delain/web/www/stop_jeu ]
then
if [ 8 -le `cat /proc/loadavg | awk '{print $1}' | awk -F "." '{print $1}'` ]
then
num_skip=`expr $num_skip + 1` 
else
num_traite=`expr $num_traite + 1` 
$psql -U webdelain -d delain << EOF >> /dev/null
\timing
select ia_monstre($monstre);
EOF
fi
fi
done < $fichier
echo "`date` - $num_traite lignes traitées et $num_skip lignes non traitées pour surcharge"  >> $logdir/ia_auto.log
echo "-----------------------------------------------"
