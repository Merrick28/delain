update quetes.aquete_element set aqelem_misc_cod=lpos_pos_cod, aqelem_type='position' from  quetes.aquete_etape,quetes.aquete_etape_modele,lieu_position
where aqelem_aqetape_cod=aqetape_cod
and aqetape_aqetapmodel_cod=aqetapmodel_cod
and aqetapmodel_tag='#START' and aqetapmodel_nom='Quête - Déclenchement sur lieu'
and aqelem_type='lieu'
and lpos_lieu_cod=aqelem_misc_cod;

update quetes.aquete_etape_modele set
aqetapmodel_nom='Quête - Déclenchement sur position',
aqetapmodel_parametres='[1:position|1%0],[2:choix|2%2]',
aqetapmodel_param_desc='Liste des positions qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).',
aqetapmodel_modele='L''endroit où vous vous trouvez est particulier.[2]'
where aqetapmodel_tag='#START' and aqetapmodel_nom='Quête - Déclenchement sur lieu'


update quetes.aquete_element set aqelem_misc_cod=lpos_pos_cod, aqelem_type='position' from  quetes.aquete_etape,quetes.aquete_etape_modele,lieu_position
where aqelem_aqetape_cod=aqetape_cod
and aqetape_aqetapmodel_cod=aqetapmodel_cod
and aqetapmodel_tag='#MOVE #LIEU'
and aqelem_type='lieu'
and lpos_lieu_cod=aqelem_misc_cod;

update quetes.aquete_etape_modele set
aqetapmodel_nom='Déplacement - Vers une POSITION',
aqetapmodel_parametres='[1:delai|1%1],[2:position|1%0]',
aqetapmodel_param_desc='Délai alloué pour cette étape.|C''est la position sur laquelle doit se rendre l''aventurier.',
aqetapmodel_modele='Rendez vous là: [2].',
aqetapmodel_tag='#MOVE #POSITION'
where aqetapmodel_tag='#MOVE #LIEU'