-- ------------------------------------------------------------------------
CREATE SEQUENCE public.seq_meca_cod
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE public.seq_meca_cod OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_meca_cod TO delain;

CREATE TABLE public.meca
(
  meca_cod integer NOT NULL DEFAULT nextval(('seq_meca_cod'::text)::regclass),
  meca_nom character varying(24) NOT NULL,
  meca_type character varying(1) NOT NULL  DEFAULT 'G',
  meca_pos_etage integer NOT NULL,
  meca_pos_type_aff integer DEFAULT NULL,
  meca_pos_decor integer DEFAULT NULL,
  meca_pos_decor_dessus integer DEFAULT NULL,
  meca_pos_passage_autorise integer DEFAULT NULL,
  meca_pos_modif_pa_dep integer DEFAULT NULL,
  meca_pos_ter_cod integer DEFAULT NULL,
  meca_mur_type integer DEFAULT NULL,
  meca_mur_tangible character varying(1) DEFAULT NULL,
  meca_mur_illusion character varying(1) DEFAULT NULL,
  meca_si_active json DEFAULT NULL,
  meca_si_desactive json DEFAULT NULL,
  CONSTRAINT pk_meca_cod PRIMARY KEY (meca_cod)
);

ALTER TABLE public.meca OWNER TO delain;

ALTER TABLE public.meca
  ADD CONSTRAINT meca_pos_etage FOREIGN KEY (meca_pos_etage) REFERENCES public.etage (etage_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE public.meca OWNER TO delain;

-- ------------------------------------------------------------------------
CREATE SEQUENCE public.seq_pmeca_cod
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE public.seq_pmeca_cod OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_pmeca_cod TO delain;

CREATE TABLE public.meca_position
(
  pmeca_cod integer NOT NULL DEFAULT nextval(('seq_pmeca_cod'::text)::regclass),
  pmeca_meca_cod integer,
  pmeca_pos_cod integer,
  pmeca_pos_etage integer NOT NULL,
  pmeca_base_pos_type_aff integer NOT NULL,
  pmeca_base_pos_decor integer NOT NULL,
  pmeca_base_pos_decor_dessus integer NOT NULL,
  pmeca_base_pos_passage_autorise integer NOT NULL,
  pmeca_base_pos_modif_pa_dep integer NOT NULL,
  pmeca_base_pos_ter_cod integer NOT NULL,
  pmeca_base_mur_type integer DEFAULT NULL,
  pmeca_base_mur_tangible character varying(1) DEFAULT NULL,
  pmeca_base_mur_tangible character varying(1) DEFAULT NULL,
  pmeca_base_mur_illusion character varying(1) DEFAULT NULL,
  CONSTRAINT pk_pmeca_cod PRIMARY KEY (pmeca_cod)
);

ALTER TABLE public.meca_position OWNER TO delain;

ALTER TABLE public.meca_position
  ADD CONSTRAINT pmeca_meca_cod FOREIGN KEY (pmeca_meca_cod) REFERENCES public.meca (meca_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE public.meca_position
  ADD CONSTRAINT pmeca_pos_cod FOREIGN KEY (pmeca_pos_cod) REFERENCES public.positions (pos_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;

-- ------------------------------------------------------------------------
CREATE SEQUENCE public.seq_ameca_cod
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE public.seq_ameca_cod OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_ameca_cod TO delain;

CREATE TABLE public.meca_action
(
  ameca_cod integer NOT NULL DEFAULT nextval(('seq_ameca_cod'::text)::regclass),
  ameca_pmeca_cod integer,
  ameca_date_action timestamp with time zone NOT NULL,
  ameca_type_action character varying(1) NOT NULL DEFAULT 'A',
  ameca_pos_cod integer DEFAULT NULL,
  CONSTRAINT pk_ameca_cod PRIMARY KEY (ameca_cod)
);

ALTER TABLE public.meca_action OWNER TO delain;

ALTER TABLE public.meca_action
  ADD CONSTRAINT ameca_pmeca_cod FOREIGN KEY (ameca_pmeca_cod) REFERENCES public.meca (meca_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;

