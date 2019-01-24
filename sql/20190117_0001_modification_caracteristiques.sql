INSERT INTO public.parametres(parm_type, parm_desc, parm_valeur, parm_valeur_texte) VALUES
( 'Integer', 'Code de l''objet générique nécéssaire pour l''interface de modification de caracs.', 997, null),
( 'Integer', 'Nombre d''objet à rammener pour utiliser l''interface de modification de caracs.', 20, null),
( 'Integer', 'Cout en brouzoufs (en plus des objets) pour utiliser l''interface de modification de caracs.', 1000, null),
( 'Integer', 'Nombre de jours devant spérarer l''utilisation successive de l''interface de modification de caracs.', 365, null),
( 'Text', 'Ouvrir l''interface de modification de caracs (O/N)?', null, 'N');


CREATE SEQUENCE public.seq_prcarac_cod
  INCREMENT 1
  MINVALUE 1
  START 1;
ALTER TABLE public.seq_prcarac_cod
  OWNER TO delain;


-- Table: public.perso_rituel_caracs

-- DROP TABLE public.perso_rituel_caracs;

CREATE TABLE public.perso_rituel_caracs
(
  prcarac_cod integer NOT NULL DEFAULT nextval(('seq_prcarac_cod'::text)::regclass),
  prcarac_perso_cod integer NOT NULL, -- identifiant du perso
  prcarac_date_rituel timestamp with time zone DEFAULT NOW(), -- date de dernière modification de carac
  prcarac_amelioration_carac_cod integer NOT NULL, -- Compétence amélioré
  prcarac_diminution_carac_cod integer NOT NULL, -- Compétence détérioré
  CONSTRAINT prcarac_cod_pkey PRIMARY KEY (prcarac_cod),
  CONSTRAINT fk_prcarac_perso_cod FOREIGN KEY (prcarac_perso_cod)
      REFERENCES public.perso (perso_cod) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.perso_rituel_caracs
  OWNER TO delain;
GRANT ALL ON TABLE public.perso_rituel_caracs TO delain;
COMMENT ON TABLE public.perso_rituel_caracs
  IS 'Table permettant de connaitre la dernière modification de carac d''un perso via le rtiuel de transformation';
COMMENT ON COLUMN public.perso_rituel_caracs.prcarac_perso_cod IS 'Identifiant du perso';
COMMENT ON COLUMN public.perso_rituel_caracs.prcarac_date_rituel IS 'Date de modification de carac';
COMMENT ON COLUMN public.perso_rituel_caracs.prcarac_amelioration_carac_cod IS 'Compétence amélioré';
COMMENT ON COLUMN public.perso_rituel_caracs.prcarac_diminution_carac_cod IS 'Compétence détérioré';


CREATE TYPE public.f_resultat AS
   (etat integer,
    code_retour text);
ALTER TYPE public.f_resultat
  OWNER TO delain;
