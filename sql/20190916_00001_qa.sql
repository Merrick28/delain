UPDATE quetes.aquete_etape_modele
set aqetapmodel_param_desc='Délai alloué pour cette étape.|Le joueur doit se trouver sur la case de ce PNJ pour faire l''échange.|C''est le nombre d''echange maximum autorisé (0 pour illimité),|Liste des transactions: les objets à acquerir pour le joueur contre les objets qui servent de monnaie d''échange.<br><u><b>NOTA</b></u>: Si partie gauche (objet à donner) est laissée à 0 (0x0), la partie droite sert alors de cout additionnel à l''objet situé au-dessus.'
where aqetapmodel_tag = '#ECHANGE #OBJET';


