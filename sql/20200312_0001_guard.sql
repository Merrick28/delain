ALTER TABLE public.guards
    ADD COLUMN guard_perso_cod integer;

ALTER TABLE public.guards
    ADD COLUMN guard_date timestamp with time zone;
ALTER TABLE public.guards
    DROP CONSTRAINT guards_pkey;

CREATE INDEX idx_guard_key_perso
    ON public.guards USING btree
        (guard_key ASC NULLS LAST, guard_perso_cod ASC NULLS LAST)
    TABLESPACE pg_default;

ALTER TABLE public.guards
    ADD CONSTRAINT fk_guard_perso_cod FOREIGN KEY (guard_perso_cod)
        REFERENCES public.perso (perso_cod) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION;

ALTER TABLE public.guards
    ALTER COLUMN guard_date SET DEFAULT now();