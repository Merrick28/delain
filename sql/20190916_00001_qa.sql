UPDATE quetes.aquete_etape_modele
SET
aqetapmodel_modele='Voici ce que [2] vous propose comme services:',
aqetapmodel_parametres='[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:valeur|1%1],[5:valeur|1%1],[6:echange|0%0]',
aqetapmodel_param_desc='Délai alloué pour cette étape.|Le joueur doit se trouver sur la case de ce PNJ pour faire l''échange.|C''est le nombre d''échange maximum qui pourront-être choisis par le joueur (0 pour illimité)|C''est le nombre maximum autorisé pour chaque échange (0 pour illimité)|C''est le nombre maximum d''objets autorisés tout échanges confondues (0 pour illimité)|Liste des transactions: les objets à acquerir pour le joueur contre les objets qui servent de monnaie d''échange.<br><u><b>NOTA</b></u>: Si partie gauche (objet à donner) est laissée à 0 (0x0), la partie droite sert alors de cout additionnel à l''objet situé au-dessus.'
WHERE aqetapmodel_tag = '#ECHANGE #OBJET';


UPDATE quetes.aquete_element
SET aqelem_param_id=6
  WHERE aqelem_param_id=4 and aqelem_aqetape_cod in (
		SELECT aqetape_cod
		FROM quetes.aquete_etape
		where aqetape_aqetapmodel_cod in (
			SELECT aqetapmodel_cod   FROM quetes.aquete_etape_modele where aqetapmodel_tag='#ECHANGE #OBJET'
			)
	) ;


UPDATE quetes.aquete_etape_modele
SET
aqetapmodel_modele='[2] vous interroge, que lui dites-vous?',
aqetapmodel_parametres='[1:perso|1%0],[2:valeur|1%1],[3:etape|1%1],[4:choix_etape|1%0],[5:texte|0%0]',
aqetapmodel_param_desc= 'C''est le perso avec lequel le joueur va dialoguer.' ||
           '|C''est le nombre de tentative maximum laissé au joueur (mettre 0 pour illimité)' ||
           '|La quête continuera à cette étape si le nombre de tentative est atteint.' ||
           '|C''est une liste de mot qui s''ils sont donnés par le joueurs vont lui permettre d''aller vers l''etape correspondante. Pour chaque ligne, plusieurs mot peuvent être fournis séparés par le caractère &verbar;.' ||
           '|Serie de textes qui seront fournis au joueur, 1 par 1 dans l''ordre après chaque tentative infructueuse, s''il y a plus de tentative que de texte, le dernier texte sera redonné.'
WHERE aqetapmodel_tag = '#SAUT #CONDITION #DIALOGUE';

UPDATE quetes.aquete_element
SET aqelem_param_id=aqelem_param_id+1
  WHERE aqelem_aqetape_cod in (
		SELECT aqetape_cod
		FROM quetes.aquete_etape
		where aqetape_aqetapmodel_cod in (
			SELECT aqetapmodel_cod   FROM quetes.aquete_etape_modele where aqetapmodel_tag='#SAUT #CONDITION #DIALOGUE'
			)
	) ;

