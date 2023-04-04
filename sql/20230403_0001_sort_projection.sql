
INSERT INTO sorts (sort_cod, sort_combinaison, sort_nom, sort_fonction, sort_cout, sort_comp_cod, sort_distance, sort_description,
                        sort_aggressif, sort_niveau, sort_soi_meme, sort_monstre, sort_joueur, sort_soutien, sort_bloquable, sort_case, sort_temps_recharge)
VALUES(178,	'999999',	'Repousser',	'magie_dispersion',	10,	51,	0,	'Vous tentez de pousez la cible suivant une certaine direction. Elle se retrouve à une distance de 0 à 1 case de sa position d’origine, peut être arrêtée par des murs, et peut blesser ceux qui se trouvent sur sa trajectoire.',
                        'O',	5,	'N',	'O',	'O',	'N',	'N',	'N',	0);


DROP FUNCTION public.nv_magie_dispersion(integer, integer, integer);
