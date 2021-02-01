
CREATE TABLE public.fonction_specifique_perso
(
  pfonc_fonc_cod integer,
  pfonc_perso_cod integer,
  pfonc_ddda timestamp without time zone default now(),
  pfonc_encours integer default 0

)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.fonction_specifique
  OWNER TO delain;

COMMENT ON COLUMN public.fonction_specifique_perso.pfonc_fonc_cod IS 'fonction specifique';
COMMENT ON COLUMN public.fonction_specifique_perso.pfonc_perso_cod IS 'le perso';
COMMENT ON COLUMN public.fonction_specifique_perso.pfonc_ddda IS 'date de dernière action';
COMMENT ON COLUMN public.fonction_specifique_perso.pfonc_encours IS 'nombre d''actions de cette fonction déjà en cours pour ce perso';

ALTER TABLE public.fonction_specifique_perso  ADD UNIQUE (pfonc_fonc_cod, pfonc_perso_cod);
