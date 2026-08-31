ALTER TABLE public.meca
    ADD COLUMN meca_compteur_cod integer;

ALTER TABLE public.meca
    ADD CONSTRAINT meca_compteur_cod_fkey FOREIGN KEY (meca_compteur_cod)
        REFERENCES public.compteur (compteur_cod) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE SET NULL;