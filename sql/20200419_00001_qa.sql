INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #COMPETENCE',
            'Quête - Aller à l''ETAPE sur condition (compétence)',
            'Cette étape sert faire un saut d''étape conditionné par un jet de compétence, il n''est pas necessaire de mettre du texte d''étape.',
            '[1:competence|1%1],[2:valeur|1%1],[3:etape|1%1],[4:etape|1%1],[5:etape|1%1],[6:etape|1%1]',
            'C''est la compétence qui sera évaluée.|' ||
            'Le test est réalisé par le lancé d''1D100. La valeur de ce paramètre est la difficulté qui sera ajouté à la compétence avant de faire le test. Une valeur négative pour ajouter de la difficulter, et positive pour de la facilité.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite critique du jet de compétence.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas de réussite sur le jet de compétence.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas d''échec ou si le perso ne possède pas la compétence.|' ||
            'C''est l''étape vers laquelle le perso sera envoyé en cas d''échec critique.',
            '');

