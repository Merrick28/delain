
CREATE SEQUENCE public.seq_comptval_cod
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    CACHE 1;

ALTER SEQUENCE public.seq_comptval_cod
    OWNER TO delain;

CREATE TABLE public.compteur_valeur
(
    comptval_cod integer NOT NULL DEFAULT nextval('seq_comptval_cod'::regclass),
    comptval_compteur_cod integer NOT NULL,
    comptval_perso_cod integer DEFAULT NULL,
    comptval_valeur integer NOT NULL DEFAULT 0,

    CONSTRAINT compteur_valeur_pkey PRIMARY KEY (comptval_cod),

    CONSTRAINT fk_comptval_compteur_cod FOREIGN KEY (comptval_compteur_cod)
        REFERENCES public.compteur (compteur_cod) MATCH SIMPLE  ON UPDATE CASCADE  ON DELETE CASCADE,

    CONSTRAINT fk_comptval_perso_cod FOREIGN KEY (comptval_perso_cod)
        REFERENCES public.perso (perso_cod) MATCH SIMPLE  ON UPDATE CASCADE  ON DELETE CASCADE
);

ALTER TABLE public.compteur_valeur
    OWNER to delain;

COMMENT ON TABLE public.compteur_valeur
    IS 'Cette table contient la valeur des compteurs';

COMMENT ON COLUMN public.compteur_valeur.comptval_compteur_cod
    IS 'Compteur auquel la valeur est associée';

COMMENT ON COLUMN public.compteur_valeur.comptval_perso_cod
    IS 'valeur du compteur associée à un perso (null si le compteur est global)';

COMMENT ON COLUMN public.compteur_valeur.comptval_valeur
    IS 'valeur actuelle du compteur ';


