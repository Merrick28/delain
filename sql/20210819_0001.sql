ALTER TABLE public.meca_action RENAME ameca_pmeca_cod  TO ameca_meca_cod;
ALTER TABLE public.meca_action DROP COLUMN ameca_type_action ;
ALTER TABLE public.meca_action ADD COLUMN ameca_sens_action integer NOT NULL DEFAULT 0;
