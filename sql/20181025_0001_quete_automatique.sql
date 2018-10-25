INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#REMETTRE #OBJET #QUETE', 'Objets - Remettre des objets de quête à un PNJ ', 'Cette étape sert à donner des objets de quête à un PNJ (ces objets ne peuvent pas être mis en transaction, ils seront directement pris depuis l''inventaire du joueur).',
           '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:objet_generique|0%0]',
           'Délai alloué pour cette étape.' ||
           '|C''est le PNJ à qui l''aventurier doit remettre les objets' ||
           '|C''est le nombre d''objet attendu, si le nombre est superieur au nombre de générique alors le PNJ prendra autant d''objet mais avec au moins un exemplaire de chaque.' ||
           '|Ce sont les génériques des objets que le PNJ s''attend à recevoir.',
           '[2] attend que vous lui remettiez [3] objets parmi: [4].');