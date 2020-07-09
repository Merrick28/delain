
update quetes.aquete_etape_modele
set aqetapmodel_parametres='[1:delai|1%1],[2:perso|1%0],[3:valeur|1%1],[4:objet_generique|0%0]'
  where aqetapmodel_tag='#RECEVOIR #OBJET';


update quetes.aquete_etape_modele
set aqetapmodel_parametres='[1:delai|1%1],[2:perso|1%0],[3:valeur|1%1],[4:objet_generique|0%0]'
  where aqetapmodel_tag='#REMETTRE #OBJET';


update quetes.aquete_etape_modele
set aqetapmodel_parametres='"1:delai|1%1],[2:perso|1%0],[3:valeur|1%1],[4:objet_generique|0%0]'
  where aqetapmodel_tag='#REMETTRE #OBJET #QUETE';