
CREATE SEQUENCE public.seq_objusebm_cod
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    CACHE 1;

ALTER SEQUENCE public.seq_objusebm_cod OWNER TO delain;

CREATE TABLE IF NOT EXISTS public.objets_usage_bm
(
    objusebm_cod integer NOT NULL DEFAULT nextval('seq_objusebm_cod'::regclass),
    objusebm_parent_cod integer,
    objusebm_gobj_cod integer,
    objusebm_obj_cod integer,
    objusebm_tbonus_cod integer NOT NULL,
    objusebm_bonus_valeur character varying(16) COLLATE pg_catalog."default",
    objusebm_bonus_nb_tours character varying(16) COLLATE pg_catalog."default",
    objusebm_cout integer DEFAULT 4,
    objusebm_malchance numeric NOT NULL,
    objusebm_nb_utilisation_max integer,
    objusebm_nb_utilisation integer NOT NULL DEFAULT 0,
    objusebm_bonus_distance integer NOT NULL DEFAULT 0,
    objusebm_bonus_aggressif character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'N'::character varying,
    objusebm_bonus_soutien character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'N'::character varying,
    objusebm_bonus_soi_meme character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'O'::character varying,
    objusebm_bonus_monstre character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'O'::character varying,
    objusebm_bonus_joueur character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'O'::character varying,
    objusebm_bonus_case character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'N'::character varying,
    objusebm_bonus_mode character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'S'::character varying,
    objusebm_bonus_familier character varying(1) COLLATE pg_catalog."default" NOT NULL DEFAULT 'N'::character varying,
    objusebm_vide_detruit char(1) DEFAULT 'N',
    CONSTRAINT objusebm_cod_pkey PRIMARY KEY (objusebm_cod),
    CONSTRAINT fk_objusebm_cod FOREIGN KEY (objusebm_parent_cod)
    REFERENCES public.objets_usage_bm (objusebm_cod) MATCH SIMPLE
    ON UPDATE CASCADE
    ON DELETE CASCADE
    );

ALTER TABLE public.objets_usage_bm OWNER to delain;