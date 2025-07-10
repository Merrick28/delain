UPDATE quetes.aquete_etape_modele
    SET aqetapmodel_param_desc = 'Liste des compteurs à tester.|' ||
                                    'Formule de calcul utilisant les variables [#compteur.x] (avec x n° de compteur dans l''ordre ci-dessus). Exemple pour une formule qui serait vrai si le 1er compteur est supérieur à 2 fois le 2eme: [#compteur.1]>(2*[#compteur.2])|' ||
                                    'La quête continuera à cette étape si la formule est vrai.|' ||
                                    'La quête continuera à cette étape si la formule n''est pas vérifée.'
WHERE aqetapmodel_tag = '#SAUT #CONDITION #COMPTEUR' ;