-- Table: public.perso_registre

-- DROP TABLE public.perso_registre;

CREATE TABLE public.perso_registre
(
  preg_perso_cod integer NOT NULL, -- iidentifiant du perso
  preg_pos_cod integer, -- position du lieu où il doit retourner s'il meurt et qu'il est au bat admin
  preg_date_inscription timestamp with time zone, -- date de l'inscription sur le registre
  CONSTRAINT preg_perso_cod_pkey PRIMARY KEY (preg_perso_cod),
    CONSTRAINT fk_preg_perso_cod FOREIGN KEY (preg_perso_cod)
      REFERENCES public.perso (perso_cod) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.perso_registre
  OWNER TO delain;
GRANT ALL ON TABLE public.perso_registre TO delain;
COMMENT ON TABLE public.perso_registre
  IS 'Table permettant un retour rapide en arene par une inscription sur un registre (lieu avec enreg_pos_donjon.php)';
COMMENT ON COLUMN public.perso_registre.preg_perso_cod IS 'iidentifiant du perso';
COMMENT ON COLUMN public.perso_registre.preg_pos_cod IS 'position du lieu où il doit retourner s''il meurt et qu''il est au bat admin';
COMMENT ON COLUMN public.perso_registre.preg_date_inscription IS 'date de l''inscription sur le registre';

