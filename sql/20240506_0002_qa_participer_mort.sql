
UPDATE quetes.aquete_etape_modele set
              aqetapmodel_parametres = '[1:delai|1%1],[2:valeur|1%1|],[3:selecteur|1%1|{0~Participer à la mort},{1~Tuer}],[4:selecteur|1%1|{0~Peu importe},{1~Au moins 1 monstre de chaque type ou race},{2~Au moins 1 monstre de chaque type et de chaque race}],[5:race|0%0],[6:type_monstre_generique|0%0]',
              aqetapmodel_param_desc =  'Délai alloué pour cette étape.|' ||
                                        'Nombre total de monstre (de race ou de type demandé) du contrat de chasse.|'||
                                        'Le meneur doit-il tuer ou seulement participer à la mort du ou des contrats demandés.|'||
                                        'Le contrat de chasse est libre ou l''on peu imposer un monstre de chaque type et/ou de chaque race.|'||
                                        'C''est la liste des races de monstres à tuer, si cette liste est vide, seule la listes des type de monstre sera pris en compte (au moins 1 des 2 listes doit être non-vide).|' ||
                                        'C''est la liste des types de monstre à tuer, si cette liste est vide, seule la liste des races de monstre sera prise en compte.|' ||
                                        'C''est la repartition des types/races à tuer.'
                  where aqetapmodel_tag = '#TUER #PARTICIPER #MORT' ;

