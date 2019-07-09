ALTER TABLE public.type_objet ADD COLUMN tobj_degradation integer NOT NULL DEFAULT 0;
COMMENT ON COLUMN public.type_objet.tobj_degradation  IS 'Délai de dégradation de l''objet en nombre de jour.  0 pour une persistance à vie.';


UPDATE type_objet set tobj_degradation=180  WHERE tobj_libelle IN ('Minerai', 'Objet de quête', 'Or', 'Pierre précieuse', 'Récipient');

UPDATE type_objet set tobj_degradation=60  WHERE tobj_libelle IN ('Totems et Charmes', 'Signe distinctif', 'Potion', 'Objet d''information', 'Glyphe', 'Gemme', 'Espèce Minérale', 'Clé', 'Minéral', 'Réceptacle alchimique', 'Document', 'Poisson', 'Outil');;

UPDATE type_objet set tobj_degradation=30 WHERE tobj_libelle IN ('Inutile');

UPDATE type_objet set tobj_degradation=15 WHERE tobj_libelle IN ('Substance', 'Ingrédient magique', 'Plantes', 'Quiddités', 'Peau magique', 'Parchemin', 'Information artisanale', 'Fève', 'Médaillon');

UPDATE type_objet set tobj_degradation=7 WHERE tobj_libelle IN ('Truc en toc');


INSERT INTO parametres(parm_type, parm_desc, parm_valeur, parm_valeur_texte) VALUES
( 'Text', 'Coefficient d''absortion des vents', null, '1');