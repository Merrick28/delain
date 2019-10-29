UPDATE quetes.aquete_etape_modele set
aqetapmodel_nom='Objets - Perdre immédiatement un objet.' ,
aqetapmodel_parametres = '[1:delai|1%1],[2:valeur|1%1],[3:objet_generique|0%0],[4:position|1%0]' ,
aqetapmodel_param_desc = 'Délai alloué pour cette étape.' ||
    '|C''est le nombre d''objet attendu,' ||
    '|Ce sont les génériques qui servent de modèle aux objets demandés.' ||
    '|Si la position est définie (different de 0), c''est un endroit ou doit se trouver le perso pour réaliser cette étape '
where aqetapmodel_tag = '#REMETTRE #DETRUIRE #OBJET';

UPDATE quetes.aquete_etape_modele set
aqetapmodel_parametres = '[1:delai|1%1],[2:valeur|1%1],[3:position|1%0]' ,
aqetapmodel_param_desc = 'Délai alloué pour cette étape.' ||
    '|C''est le nombre de brouzoufs attendu.' ||
    '|Si la position est définie (different de 0), c''est un endroit ou doit se trouver le perso pour réaliser cette étape '
where aqetapmodel_tag = '#REMETTRE #DETRUIRE #PO';




