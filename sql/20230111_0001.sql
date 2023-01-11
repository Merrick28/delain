
UPDATE quetes.aquete_etape_modele
SET aqetapmodel_parametres='[1:position|1%1],[2:valeur|1%1],[3:selecteur|1%1|{1~le meneur de quête seul},{2~le meneur et sa triplette à proximité},{3~le meneur et sa coterie à proximité},{4~tous les aventuriers à proximité}],[4:valeur|1%1],[5:texte|1%1]',
aqetapmodel_param_desc='C''est la position centrale où seront téléportés le ou les persos.' ||
                      '|C''est la dispersion qui sera appliquée à chaque perso téléporté, il le sera autour du point central dans un rayon inférieur à cette valeur.' ||
                      '|Ce sont les persos situés à proximité qui doivent être téléportés.' ||
                      '|Distance de proximité, au dela de cette distance les persos ne seront pas emmenés avec le meneur.' ||
                      '|Texte de l''evenement qui sera ajouté au(x) perso(s) téléportés (utiliser [cible]/[attaquant] pour remplacer le perso téléporté/le meneur de quete)'

WHERE aqetapmodel_tag='#TELEPORTATION #PERSO'
