ALTER TABLE public.carac_arene ADD COLUMN carene_level_min integer;
COMMENT ON COLUMN public.carac_arene.carene_level_min IS 'Level minimum admis dans l''arène, si = 0 pas de level min';

update carac_arene set carene_level_min=0;