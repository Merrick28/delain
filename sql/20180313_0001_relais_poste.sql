ALTER TABLE public.objet_generique ADD COLUMN  gobj_postable character varying(1) NOT NULL DEFAULT 'N'::character varying;

COMMENT ON COLUMN public.objet_generique.gobj_postable IS 'Objet générique postable dans les relais poste';

UPDATE objet_generique SET gobj_postable='O' WHERE gobj_tobj_cod not in (5,11,17,18,19,21,22,28,30,34,26,35,31,7,14,24,20)
