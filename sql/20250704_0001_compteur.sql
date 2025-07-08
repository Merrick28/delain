
CREATE SEQUENCE public.seq_compteur_cod
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    CACHE 1;

ALTER SEQUENCE public.seq_compteur_cod
    OWNER TO delain;

CREATE TABLE public.compteur
(
    compteur_cod integer NOT NULL DEFAULT nextval('seq_compteur_cod'::regclass),
    compteur_libelle character varying(255),
    compteur_type integer NOT NULL DEFAULT 0,
    compteur_init integer NOT NULL DEFAULT 0,
    CONSTRAINT compteur_pkey PRIMARY KEY (compteur_cod)
);

ALTER TABLE public.compteur
    OWNER to delain;

COMMENT ON TABLE public.compteur
    IS 'Cette table permet de gérer les compteurs en jeu utilisable par les QA';

COMMENT ON COLUMN public.compteur.compteur_libelle
    IS 'Nom du compteur';

COMMENT ON COLUMN public.compteur.compteur_type
    IS 'Type de compteur: 0 = global (pour tout le monde), 1 = perso (1 par joueur)';

COMMENT ON COLUMN public.compteur.compteur_init
    IS 'Valeur du compteur à son initialisation (0 par défaut)';

