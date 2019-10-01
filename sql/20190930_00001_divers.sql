
UPDATE type_evt set tevt_texte='[cible] a été protégé d''un coup critique/spécial par son équipement' where tevt_cod=47 ;

ALTER TABLE public.monstre_generique
   ADD COLUMN gmon_sex character varying(2);

