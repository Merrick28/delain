INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description, aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
VALUES ('#SAUT #CONDITION #COMPTEUR',
        'Quête - Aller à l''ETAPE sur condition (valeur de compteur)',
        'Cette étape sert faire un saut d''étape conditionné par la valeur d''un ou plusieurs compteurs.',
        '[1:compteur|0%0],[2:texte|1%1],[3:etape|1%1],[4:etape|1%1]',
        'Liste des compteurs à tester.|' ||
        'Formule de calcul utilisant les variables [#compteur.x] (avec x n° de compteur dans l''ordre ci-dessus). Exemple pour une formule qui serait vrai si le 1er compteur est supérieur à 2 fois le 2eme: [#compteur.1]>(2*[#compteur.2])|' ||
        'La quête continuera à cette étape si la formule n''est pas vérifée.|' ||
        'La quête continuera à cette étape si la formule est vrai.' ,
        '');
