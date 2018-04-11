
CREATE SEQUENCE public.seq_pfav_cod
  INCREMENT 1;


CREATE TABLE public.perso_favoris
(
  pfav_cod integer NOT NULL DEFAULT nextval(('seq_pfav_cod'::text)::regclass),
  pfav_perso_cod integer NOT NULL,
  pfav_type text NOT NULL,
  pfav_misc_cod integer NOT NULL,
  pfav_nom text,
  pfav_function_cout_pa text NOT NULL,
  pfav_link text NOT NULL,
  CONSTRAINT fk_pfav_perso_cod FOREIGN KEY (pfav_perso_cod)
      REFERENCES public.perso (perso_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=TRUE
);

