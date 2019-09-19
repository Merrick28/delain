UPDATE quetes.aquete_etape_modele
SET
aqetapmodel_modele='Voici ce que [2] vous propose comme services:',
aqetapmodel_parametres='[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:valeur|1%1],[5:echange|0%0]',
aqetapmodel_param_desc='Délai alloué pour cette étape.|Le joueur doit se trouver sur la case de ce PNJ pour faire l''échange.|C''est le nombre de ligne maximum qui pourront-être choisies par le joueur (0 pour illimité)|C''est le nombre maximum autorisé pour chaque ligne (0 pour illimité)|Liste des transactions: les objets à acquerir pour le joueur contre les objets qui servent de monnaie d''échange.<br><u><b>NOTA</b></u>: Si partie gauche (objet à donner) est laissée à 0 (0x0), la partie droite sert alors de cout additionnel à l''objet situé au-dessus.'
WHERE aqetapmodel_tag = '#ECHANGE #OBJET';


UPDATE quetes.aquete_element
SET aqelem_param_id=5
  WHERE aqelem_param_id=4 and aqelem_aqetape_cod in (
		SELECT aqetape_cod
		FROM quetes.aquete_etape
		where aqetape_aqetapmodel_cod in (
			SELECT aqetapmodel_cod   FROM quetes.aquete_etape_modele where aqetapmodel_tag='#ECHANGE #OBJET'
			)
	)
