-- Sequence: quetes.seq_aqperson_cod

-- DROP SEQUENCE quetes.seq_aqperson_cod;

CREATE SEQUENCE quetes.seq_aqperson_cod
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE quetes.seq_aqperson_cod
  OWNER TO delain;


CREATE TABLE quetes.aquete_perso_notes
(
  aqperson_cod integer NOT NULL DEFAULT nextval(('quetes.seq_aqperson_cod'::text)::regclass),
  aqperson_date timestamp with time zone DEFAULT now(), -- La date de mise à jour
  aqperson_perso_cod integer NOT NULL, -- Le perso
  aqperson_notes text, -- le texte de son journal
  CONSTRAINT aquete_perso_notes_pkey PRIMARY KEY (aqperson_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE quetes.aquete_perso_notes
  OWNER TO webdelain;

COMMENT ON COLUMN quetes.aquete_perso_notes.aqperson_date IS 'La date de  mise à jour';
COMMENT ON COLUMN quetes.aquete_perso_notes.aqperson_perso_cod IS 'Le perso';
COMMENT ON COLUMN quetes.aquete_perso_notes.aqperson_notes IS 'le texte de le journal';

ALTER TABLE quetes.aquete_perso_notes
  ADD CONSTRAINT fk_aqperson_perso_cod FOREIGN KEY (aqperson_perso_cod) REFERENCES public.perso (perso_cod)
   ON UPDATE CASCADE ON DELETE CASCADE;
CREATE INDEX fki_aqperson_perso_cod
  ON quetes.aquete_perso_notes(aqperson_perso_cod);
