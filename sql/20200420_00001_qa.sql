UPDATE quetes.aquete_etape_modele SET
  aqetapmodel_parametres = '[1:competence|1%1],[2:valeur|1%1],[3:valeur|1%1],[4:etape|1%1],[5:etape|1%1],[6:etape|1%1],[7:etape|1%1],[8:etape|1%1]',
  aqetapmodel_param_desc = 'C''est la compétence qui sera évaluée.|' ||
            'C''est le niveau de compétence minimum requis. Si le perso ne possède pas la compétence ou n''a pas le niveau requis la quête se poursuit à l''étape suivante.|' ||
            'Le test est réalisé par le lancé d''1D100. La valeur de ce paramètre est la difficulté qui sera ajoutée à la compétence avant de faire le test. Une valeur négative pour ajouter de la difficulté, et positive pour de la facilité.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite critique du jet de compétence.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite speciale sur le jet de compétence.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite sur le jet de compétence.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas d''échec.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas d''échec critique.'
  WHERE aqetapmodel_tag='#SAUT #CONDITION #COMPETENCE' ;