ALTER TABLE public.bonus_type
    ADD COLUMN tbonus_aura boolean NOT NULL DEFAULT false,
    ADD COLUMN tbonus_aura_libc character varying(3),
    ADD COLUMN tbonus_aura_vmax numeric NOT NULL DEFAULT 0;

ALTER TABLE public.bonus
    ADD COLUMN bonus_dfin timestamp without time zone,
    ALTER COLUMN bonus_nb_tours DROP NOT NULL;

INSERT INTO public.bonus_type(tbonus_libc, tonbus_libelle, tbonus_gentil_positif, tbonus_nettoyable, tbonus_cumulable, tbonus_degressivite, tbonus_description, tbonus_compteur, tbonus_aura, tbonus_aura_libc, tbonus_aura_vmax)
    VALUES ('@MA', 'Bouclier de Malédiction', true, 'N', 'N', null, 'Aura de protection contre les malédictions', 'N', true, 'MAU', 0);