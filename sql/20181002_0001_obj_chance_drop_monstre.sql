ALTER TABLE public.objet_generique
   ADD COLUMN gobj_chance_drop_monstre integer;
COMMENT ON COLUMN public.objet_generique.gobj_chance_drop_monstre
  IS 'Ce sont les chances de droper l''objet s''il est possédé par un monstre qui décède (chances de drop de 100% si NULL)';
