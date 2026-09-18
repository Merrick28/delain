ALTER TABLE IF EXISTS public.meca DROP CONSTRAINT IF EXISTS meca_pos_etage;

ALTER TABLE IF EXISTS public.meca
    ADD CONSTRAINT meca_numero_etage FOREIGN KEY (meca_pos_etage)
    REFERENCES public.etage (etage_numero) MATCH SIMPLE
        ON UPDATE CASCADE
       ON DELETE CASCADE ;
