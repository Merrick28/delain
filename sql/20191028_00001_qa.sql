INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#REMETTRE #DETRUIRE #PO', 'Gain/Perte - Perdre des Brouzoufs.',
           'Le joueur perd les Brouzoufs demandés. Attention si le joueur possède les Brouzoufs demandés au début de l''étape, les Brouzoufs sont immédiatement détruits.',
           '[1:delai|1%1],[2:valeur|1%1]',
           'Délai alloué pour cette étape.|C''est le nombre de brouzoufs attendu',
           'Pour continuer, procurez vous [2] Brouzoufs qui seront immédiatement détruits.');


update quetes.aquete_etape_modele set aqetapmodel_nom='Gain/Perte - Recevoir un TITRE' where aqetapmodel_tag='#RECEVOIR #TITRE';
update quetes.aquete_etape_modele set aqetapmodel_nom='Gain/Perte - Recevoir PX/PO' where aqetapmodel_tag='#RECEVOIR #PX';
update quetes.aquete_etape_modele set aqetapmodel_nom='Gain/Perte - Recevoir un BONUS ou un MALUS.' where aqetapmodel_tag='#RECEVOIR #BONUS';

