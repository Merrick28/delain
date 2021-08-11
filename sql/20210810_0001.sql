ALTER TABLE quetes.aquete
   ADD COLUMN aquete_interaction character varying(1) NOT NULL DEFAULT 'N';


 INSERT INTO quetes.aquete_etape_modele(  aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,   aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#START #INTERACTION',
            'Interaction - Déclenchement sur position',
            'Cette étape ne doit être utilisée qu''en tant que 1ère étape d''une quête. Elle permet de déterminer quelles poisitons vont déclencher l''interraction.',
            '[0:perso_condition|0%0],[1:position|1%0]',
            'Liste des conditions que doit verifier le perso pour pouvoir commencer l''interaction.|' ||
            'Liste des positions vont déclencher l''interaction.',
            '');

ALTER TABLE quetes.aquete
   ADD COLUMN aquete_pos_etage integer DEFAULT NULL;
