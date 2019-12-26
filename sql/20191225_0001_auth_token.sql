CREATE TABLE public.auth_token
(
    at_token uuid NOT NULL,
    at_compt_cod integer NOT NULL,
    at_date timestamp with time zone NOT NULL DEFAULT now(),
    PRIMARY KEY (at_token),
    CONSTRAINT fk_at_compt_cod FOREIGN KEY (at_compt_cod)
        REFERENCES public.compte (compt_cod) MATCH SIMPLE
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
)
    WITH (
        OIDS = FALSE
    );

ALTER TABLE public.auth_token
    OWNER to delain;
COMMENT ON TABLE public.auth_token
    IS 'Table des tokens pour auth api et jeu';

create index auth_token_date__index
    on auth_token (at_date);