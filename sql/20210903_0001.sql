UPDATE quetes.aquete_etape_modele
  SET aqetapmodel_parametres = '[1:quete|1%0],[2:selecteur|1%1|{0~Pour tous},{1~Meneur seulement}]',
      aqetapmodel_param_desc ='La quête à désactiver (laisser vide pour désactiver cette quête elle même).|Pour qui cette quête doit-elle être désactivée?'
    WHERE aqetapmodel_tag = '#QUETE #DESACTIVATION';

UPDATE quetes.aquete_etape_modele
  SET aqetapmodel_parametres = '[1:quete|1%0],[2:selecteur|1%1|{0~Pour tous},{1~Meneur seulement}]',
      aqetapmodel_param_desc ='La quête à activer.|Pour qui cette quête doit-elle être activée?'
    WHERE aqetapmodel_tag = '#QUETE #ACTIVATION';
