ALTER TABLE IF EXISTS quetes.aquete_type_carac
    ADD COLUMN aqtypecarac_index character varying(64);

ALTER TABLE IF EXISTS quetes.aquete_type_carac
    ADD COLUMN aqtypecarac_index_desc character varying(256);


insert into quetes.aquete_type_carac (aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type, aqtypecarac_aff, aqtypecarac_index, aqtypecarac_index_desc)
values(40,
       'Compteur (index=code du compteur)',
       'INDEX',
       'Vérification de la valeur d’un compteur',
       'compteur',
       'Code du compteur à vérifier');


