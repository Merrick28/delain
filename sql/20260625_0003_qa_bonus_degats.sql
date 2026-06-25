update quetes.aquete_etape_modele
    set aqetapmodel_parametres='[1:valeur|1%1],[2:bonus|0%0],[3:texte|1%1]',
        aqetapmodel_nom='Gain/Perte - Recevoir Dégats/Soin et/ou BONUS/MALUS.',
        aqetapmodel_param_desc='C''est le nombre de bonus/malus à donner, si ce nombre est inférieur au nombre de bonus/malus total alors un tirage aléatoire sera effectué, s''il est superieur alors le joueur recevra tous les bonus/malus. 0 pour ne donner aucun B/M' ||
                               '|Ce sont les bonus/malus qui seront donnés.' ||
                               '|Degats ou soins à appliquer sur le joueur au format «Dé Roliste», valeur positive pour le Soin et négative pour les dégats (laisser vide si inutilisé) .'
    where aqetapmodel_tag='#RECEVOIR #BONUS';