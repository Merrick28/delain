
ALTER TABLE public.etage ADD COLUMN etage_monture json NULL DEFAULT NULL ;
ALTER TABLE public.etage DROP COLUMN etage_monture_ordre  ;
