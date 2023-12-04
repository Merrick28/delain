update type_objet set tobj_equipable=1, tobj_max_equip=1 where tobj_libelle='Animation';

update quetes.aquete_etape_modele set
	aqetapmodel_parametres='[1:valeur|1%1],[2:objet_generique|0%0],[3:selecteur|1%1|{0~Dans l''inventaire},{1~Au sol},{2~Equipé}],[4:valeur|1%1]',
	aqetapmodel_param_desc= 'C''est le nombre d''objet à donner, si ce nombre est inférieur au nombre de générique alors un tirage aléatoire sera effectué, s''il est superieur alors le joueur recevra au moins un exemplaire de chaque puis un complément aléatoire pour atteidre le nombre prévu.|' ||
	                        'Ce sont les génériques qui serviront de modèle aux objets qui seront donné.|' ||
	                         'Le moyen de distribution (au sol, dans l''inventaire ou équipé)|' ||
	                         'Si le sol est choisis comme moyen de distribution, c''est la disperation sur le sol autour du meneur de quête.'
	where aqetapmodel_tag = '#RECEVOIR #INSTANT #OBJET' ;
