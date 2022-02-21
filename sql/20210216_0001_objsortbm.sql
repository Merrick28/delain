
CREATE SEQUENCE public.seq_objsortbm_cod
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    CACHE 1;

ALTER SEQUENCE public.seq_objsortbm_cod
    OWNER TO delain;

CREATE TABLE public.objets_sorts_bm
(
    objsortbm_cod integer NOT NULL DEFAULT nextval('seq_objsortbm_cod'::regclass),
    objsortbm_parent_cod integer,
    objsortbm_gobj_cod integer,
    objsortbm_obj_cod integer,
    objsortbm_tbonus_cod integer NOT NULL,
    objsortbm_bonus_valeur character varying(16),
    objsortbm_bonus_nb_tours character varying(16),
    objsortbm_nom character varying(50),
    objsortbm_cout integer,
    objsortbm_malchance numeric NOT NULL,
    objsortbm_nb_utilisation_max integer,
    objsortbm_nb_utilisation integer NOT NULL DEFAULT 0,
    objsortbm_equip_requis boolean NOT NULL DEFAULT false,
    CONSTRAINT objets_sorts_bm_pkey PRIMARY KEY (objsortbm_cod),
    CONSTRAINT fk_objsortbm_cod FOREIGN KEY (objsortbm_parent_cod)
        REFERENCES public.objets_sorts_bm (objsortbm_cod) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

ALTER TABLE public.objets_sorts_bm
    OWNER to delain;

COMMENT ON TABLE public.objets_sorts_bm
    IS 'Permet d’associer un BM à un objet spécifique ou a un générique, comme si on utilisait un sort';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_parent_cod
    IS 'C''est un lien vers le objsortbm_cod d''un ensorcellement d''objet générique qui est le pere de celui-ci, cette liaison est nécéssaire à cause du nombre d''utilisation du BM pour chaque objet.';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_gobj_cod
    IS 'Le code de l''objet générique sur lequel est rattaché le BM';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_obj_cod
    IS 'Le code de l''obet sur lequel est rattaché le bm  (le BM peut être rattaché à un objet spécifique)';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_tbonus_cod
    IS 'Code du BM utilisé comme sort ';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_bonus_valeur
    IS 'Puissance du BM (au format Dé rolliste)';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_bonus_nb_tours
    IS 'Durée du BM (au format Dé rolliste)';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_nom
    IS 'Le nom du BM (si null le nom sera celui du BM réel) ';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_cout
    IS 'Nombre de PA';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_malchance
    IS 'Pourcentage d''échec possible (0=toujours réussi)';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_nb_utilisation_max
    IS 'Nombre d''utilisation max possible (illimité si null)';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_nb_utilisation
    IS 'Compteur du nombre d''utilisation';

COMMENT ON COLUMN public.objets_sorts_bm.objsortbm_equip_requis
    IS 'si vrai: l''objet doit être équipé pour être utilisé';