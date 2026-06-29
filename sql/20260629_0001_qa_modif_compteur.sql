


update quetes.aquete_etape_modele
    set aqetapmodel_parametres='[1:compteur|1%0],[2:selecteur|1%1|{0~Assigner},{1~Incrémeter},{-1~Décrémenter}],[3:texte|1%1]',
        aqetapmodel_param_desc='Les compteurs concernés par la modification|' ||
                               'Le type de modification: assigner, incrementer, decrementer|' ||
                               'La valeur au format «dé roliste» (par exemple 1D6+2) à assigner, ajouter ou soustraire du compteur.'
    where aqetapmodel_tag='#COMPTEUR #MODIFIER';

