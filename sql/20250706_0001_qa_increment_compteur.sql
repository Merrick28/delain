INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
VALUES ('#COMPTEUR #MODIFIER',
        'Intéraction - Modifier la valeur d''un ou plusieurs compteurs.',
        'Dans cette étape on va modifier la valeur des compteur globaux ou individuels. (cette étape esr automatiquement validé)',
        '[1:delai|1%1],[2:compteur|1%0],[3:selecteur|1%1|{0~Assigner},{1~Incrémeter},{-1~Décrémenter}],[4:valeur|1%1]',
        'Délai alloué pour cette étape.|' ||
        'Les compteurs concernés par la modification|'||
        'Le type de modification: assigner, incrementer, decrementer|' ||
        'La valeur à assigner, ajouter ou soustraire du compteur.' ,
        '');
