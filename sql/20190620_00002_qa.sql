INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#REMETTRE #DETRUIRE #OBJET', 'Objets - Remettre un objet.',
           'Le joueur perd le ou les objets demandés. Attention si le joueur possède les objets demandés au début de l''étape, les objets sont immédiatement détruits.',
           '[1:delai|1%1],[2:valeur|1%1],[3:objet_generique|0%0]',
           'Délai alloué pour cette étape.|C''est le nombre d''objet attendu,|Ce sont les génériques qui servent de modèle aux objets demandés.',
           'Pour continuer, procurez vous [2] objet(s) parmi: [3], ces objets seront détruits.');

UPDATE quetes.aquete_etape_modele set aqetapmodel_nom='Objets - Recevoir immédiatement un objet.' where aqetapmodel_tag = '#RECEVOIR #INSTANT #OBJET';
