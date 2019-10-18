INSERT INTO quetes.aquete_etape_modele(
            aqetapmodel_tag, aqetapmodel_nom, aqetapmodel_description,
            aqetapmodel_parametres, aqetapmodel_param_desc, aqetapmodel_modele)
    VALUES ('#SAUT #CONDITION #PERSO', 'Quête - Aller à l''ETAPE sur condition (perso)',
           'Cette étape sert faire un saut d''étape conditionné par les carastéristiques du meneur, il n''est pas necessaire de mettre du texte d''étape.',
           '[1:perso_condition|0%0],[2:etape|1%1],[3:etape|1%1]',
           'Liste des conditions que le perso doit vérifier. S''il y a des contitions en "ET" elles toutes obligatoires, s''il y a des contiions en "OU" le meneur doit en vérifier au moins une.' ||
            '|C''est l''étape de destination souhaitée si la/les conditions sont remplies.' ||
            '|La quête continuera à cette étape si la/les conditions ne sont pas remplies.',
           '');


-- Table: quetes.aquete_type_carac
-- DROP TABLE quetes.aquete_type_carac;
CREATE TABLE quetes.aquete_type_carac
(
  aqtypecarac_cod integer NOT NULL,
  aqtypecarac_nom character varying(64) NOT NULL,
  aqtypecarac_type character varying(16) NOT NULL,
  aqtypecarac_description text NULL DEFAULT NULL,
  CONSTRAINT aqtypecarac_pkey PRIMARY KEY (aqtypecarac_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE quetes.aquete_type_carac
  OWNER TO delain;


INSERT INTO quetes.aquete_type_carac(aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type) VALUES
  (0, '', ''),
  (1, 'Force', 'CARAC'),
  (2, 'Dextérité', 'CARAC'),
  (3, 'Intelligence', 'CARAC'),
  (4, 'Constitution', 'CARAC'),
  (5, 'Sexe (M/F)', 'CARAC'),
  (6, 'Race (Nain, Elfe, Humain, etc..)', 'CARAC'),
  (7, 'VUE', 'CARAC'),
  (8, 'Nb de Bzf', 'VARIABLE'),
  (9, 'NB de PA', 'VARIABLE'),
  (10, 'Point de Vie MAX', 'CARAC'),
  (11, 'Point de Vie', 'VARIABLE'),
  (12, 'PV en % de Blessure', 'VARIABLE'),
  (13, 'Temps au Tour (en minutes)', 'CARAC'),
  (14, 'Niveau', 'CARAC'),
  (15, 'Intangibilité (en nb de tour)', 'VARIABLE'),
  (16, 'Voie Magique (de 0 à 7)', 'CARAC'),
  (17, 'Type perso (1=PJ, 2=Monstre, 3=Fam.)', 'CARAC'),
  (18, 'Perso PNJ (0=PJ, 1=PNJ, 3=4ème)', 'CARAC'),
  (19, 'Competence Alchimie (en %)', 'COMPETENCE'),
  (20, 'Competence Forgemage (en %)', 'COMPETENCE'),
  (21, 'Competence Enluminure (en %)', 'COMPETENCE'),
  (22, 'Niveau Alchimie', 'COMPETENCE'),
  (23, 'Niveau Forgemage', 'COMPETENCE'),
  (24, 'Niveau Enluminure', 'COMPETENCE');


UPDATE quetes.aquete_etape_modele SET
aqetapmodel_parametres='[0:perso_condition|0%0],[1:perso|1%0],[2:choix|2%2]' ,
aqetapmodel_param_desc='Liste des condtions que doit verifier le perso pour pouvoir commencer la quête.|Liste des persos (pnj) qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).'
where aqetapmodel_tag='#START' AND  aqetapmodel_parametres='[1:perso|1%0],[2:choix|2%2]';


UPDATE quetes.aquete_etape_modele SET
aqetapmodel_parametres='[0:perso_condition|0%0],[1:position|1%0],[2:choix|2%2]' ,
aqetapmodel_param_desc='Liste des condtions que doit verifier le perso pour pouvoir commencer la quête.|Liste des positions qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).'
where aqetapmodel_tag='#START' AND  aqetapmodel_parametres='[1:position|1%0],[2:choix|2%2]';


UPDATE quetes.aquete_etape_modele SET
aqetapmodel_parametres='[0:perso_condition|0%0],[1:lieu_type|1%0],[2:choix|2%2]' ,
aqetapmodel_param_desc='Liste des condtions que doit verifier le perso pour pouvoir commencer la quête.|Liste des lieux qui permettent de démarrer la quête.|Liste de deux choix pour soit accepter (saisir 0 comme n° d''étape) la quête, soit la refuser (saisir -1).'
where aqetapmodel_tag='#START' AND  aqetapmodel_parametres='[1:lieu_type|1%0],[2:choix|2%2]';

