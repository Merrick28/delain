ALTER TABLE public.objets_poste ADD COLUMN opost_emet_pos_cod integer NOT NULL;
COMMENT ON COLUMN public.objets_poste.opost_emet_pos_cod IS 'position de l''emeteur à l''emission';
