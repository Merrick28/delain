
-- CREATE Sequence
CREATE SEQUENCE public.seq_ccompt_cod
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE public.seq_ccompt_cod OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_ccompt_cod TO delain;

-- Table: public.compte_coffre
CREATE TABLE public.compte_coffre
(
  ccompt_cod integer NOT NULL DEFAULT nextval(('seq_ccompt_cod'::text)::regclass),
  ccompt_compt_cod integer NOT NULL,
  ccompt_date_ouverture timestamp with time zone NOT NULL DEFAULT now(),
  ccompt_date_extension timestamp with time zone DEFAULT NULL,
  ccompt_taille integer NOT NULL default 0,
  ccompt_cout integer NOT NULL default 0,
  CONSTRAINT pk_ccompt_cod PRIMARY KEY (ccompt_cod)
);
ALTER TABLE public.compte_coffre
  OWNER TO delain;
COMMENT ON TABLE public.compte_coffre  IS 'Gestion du coffre de triplette';

-- foreign key sur le compte
ALTER TABLE public.compte_coffre
  ADD CONSTRAINT fk_ccompt_compt_cod FOREIGN KEY (ccompt_compt_cod) REFERENCES public.compte (compt_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;
CREATE INDEX fki_ccompt_compt_cod
  ON public.compte_coffre(ccompt_compt_cod);

-- un coffre par compte !
ALTER TABLE public.compte_coffre
  ADD UNIQUE (ccompt_compt_cod);


-- CREATE Sequence
CREATE SEQUENCE public.seq_coffre_cod
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE public.seq_coffre_cod OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_coffre_cod TO delain;

-- Table: public.compte_coffre
CREATE TABLE public.coffre_objets
(
  coffre_cod integer NOT NULL DEFAULT nextval(('seq_coffre_cod'::text)::regclass),
  coffre_compt_cod integer NOT NULL,
  coffre_obj_cod integer NOT NULL,
  coffre_perso_cod integer DEFAULT NULL,
  coffre_date_depot timestamp with time zone NOT NULL DEFAULT now(),
  CONSTRAINT pk_coffre_cod PRIMARY KEY (coffre_cod),

  CONSTRAINT fk_coffre_obj_cod FOREIGN KEY (coffre_obj_cod)
      REFERENCES public.objets (obj_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,

  CONSTRAINT fk_coffre_perso_cod FOREIGN KEY (coffre_perso_cod)
    REFERENCES public.perso (perso_cod) MATCH SIMPLE
    ON UPDATE SET NULL ON DELETE SET NULL
);
ALTER TABLE public.coffre_objets
  OWNER TO delain;
COMMENT ON TABLE public.compte_coffre  IS 'Contenu du coffre de triplette';

ALTER TABLE public.coffre_objets
  ADD UNIQUE (coffre_obj_cod);
