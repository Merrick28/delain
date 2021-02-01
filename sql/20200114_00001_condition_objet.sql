CREATE SEQUENCE seq_objelem_cod
  INCREMENT 1
  START 1;
ALTER TABLE seq_objelem_cod
  OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE seq_objelem_cod TO delain;

CREATE TABLE objet_element
(
objelem_cod integer NOT NULL DEFAULT nextval(('seq_objelem_cod'::text)::regclass),
objelem_gobj_cod integer, -- Le code de l'objet générique pour cet élément
objelem_obj_cod integer, -- Le code de l'obet pour cet élément
objelem_param_id integer NOT NULL, -- N° du paramètre pour cet element (normalement 1, mais ouvre d'autres possibilités)
objelem_type text NOT NULL, -- Type de l'élément: perso_condition
objelem_misc_cod integer, -- lien vers un _cod d'une autre table
objelem_param_num_1 numeric, -- Paramètre numeric utilisé en fonction du type de l'element
objelem_param_num_2 numeric, -- Paramètre numeric utilisé en fonction du type de l'element
objelem_param_num_3 numeric, -- Paramètre numeric utilisé en fonction du type de l'element
objelem_param_txt_1 text, -- Paramètre texte utilisé en fonction du type de l'element
objelem_param_txt_2 text, -- Paramètre texte utilisé en fonction du type de l'element
objelem_param_txt_3 text, -- Paramètre texte utilisé en fonction du type de l'element
objelem_param_ordre integer, -- Si un parmètre dispose de plusieurs éléments, ce champ permet de les trier.
objelem_nom text, -- Nom de l'élément pour une utilisation texte
CONSTRAINT objelem_pkey PRIMARY KEY (objelem_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE objet_element
  OWNER TO delain;


COMMENT ON COLUMN objet_element.objelem_gobj_cod  IS 'Le code de l''objet générique pour cet élément';
COMMENT ON COLUMN objet_element.objelem_obj_cod  IS 'Le code de l''objet pour cet élément';
COMMENT ON COLUMN objet_element.objelem_param_id  IS 'N° du paramètre pour cet element (normalement 1, mais ouvre d''autres possibilités)';
COMMENT ON COLUMN objet_element.objelem_type  IS 'Type de l''élément: perso_condition';
COMMENT ON COLUMN objet_element.objelem_misc_cod  IS 'lien vers un _cod d''une autre table';
COMMENT ON COLUMN objet_element.objelem_param_num_1  IS 'Paramètre numeric utilisé en fonction du type de l''element';
COMMENT ON COLUMN objet_element.objelem_param_num_2  IS 'Paramètre numeric utilisé en fonction du type de l''element';
COMMENT ON COLUMN objet_element.objelem_param_num_3  IS 'Paramètre numeric utilisé en fonction du type de l''element';
COMMENT ON COLUMN objet_element.objelem_param_txt_1  IS 'Paramètre texte utilisé en fonction du type de l''element';
COMMENT ON COLUMN objet_element.objelem_param_txt_2  IS 'Paramètre texte utilisé en fonction du type de l''element';
COMMENT ON COLUMN objet_element.objelem_param_txt_3  IS 'Paramètre texte utilisé en fonction du type de l''element';
COMMENT ON COLUMN objet_element.objelem_param_ordre  IS 'Si un parmètre dispose de plusieurs éléments, ce champ permet de les trier.';
COMMENT ON COLUMN objet_element.objelem_nom   IS 'Nom de l''élément pour une utilisation texte';


ALTER TABLE quetes.aquete_type_carac ADD COLUMN aqtypecarac_aff text;
COMMENT ON COLUMN quetes.aquete_type_carac.aqtypecarac_aff IS 'Texte pour l''affichage (or interface admin)';
UPDATE quetes.aquete_type_carac SET aqtypecarac_aff=aqtypecarac_nom ;

UPDATE quetes.aquete_type_carac SET aqtypecarac_aff='Sexe' where aqtypecarac_cod=5 ;
UPDATE quetes.aquete_type_carac SET aqtypecarac_aff='Race' where aqtypecarac_cod=6 ;
UPDATE quetes.aquete_type_carac SET aqtypecarac_aff='Voie Magique' where aqtypecarac_cod=16;
UPDATE quetes.aquete_type_carac SET aqtypecarac_aff='Type de perso' where aqtypecarac_cod=17 ;
UPDATE quetes.aquete_type_carac SET aqtypecarac_aff='Type de PNJ' where aqtypecarac_cod=18 ;

-- rendre desequipable les armes naturrelles des familiers
UPDATE objet_generique SET gobj_desequipable='N'  where gobj_cod in (169,307,369);
UPDATE objets SET obj_desequipable='N'  where obj_gobj_cod in (169,307,369);
