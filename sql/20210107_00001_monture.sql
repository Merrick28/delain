CREATE SEQUENCE seq_ter_cod
  INCREMENT 1
  START 1;
ALTER TABLE seq_ter_cod
  OWNER TO delain;

GRANT SELECT, UPDATE ON SEQUENCE seq_ter_cod TO delain;

CREATE TABLE terrain
(
  ter_cod integer NOT NULL DEFAULT nextval(('seq_ter_cod'::text)::regclass),
  ter_nom varchar(24), -- Le nom du terrain
  ter_desc text, -- description du terrain
  CONSTRAINT ter_cod_pkey PRIMARY KEY (ter_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE terrain  OWNER TO delain;

CREATE SEQUENCE seq_tmon_cod
  INCREMENT 1
  START 1;
ALTER TABLE seq_tmon_cod
  OWNER TO delain;

GRANT SELECT, UPDATE ON SEQUENCE seq_tmon_cod TO delain;

CREATE TABLE monstre_terrain
(
  tmon_cod integer NOT NULL DEFAULT nextval(('seq_tmon_cod'::text)::regclass),
  tmon_gmon_cod integer, -- mosntre generique
  tmon_ter_cod integer, -- son comportement sur ce terrain
  tmon_accessible character varying(1) NOT NULL  DEFAULT 'O', -- peux-t-il aller dessus ?
  tmon_terrain_pa integer, -- bous/malus de PA sur ce terrain
  tmon_event_chance decimal(10,2), -- chance d'évenement sur se terrain
  tmon_event_pa character varying(24) NOT NULL  DEFAULT '0', -- gain/perte de PA (format dé rolliste)
  tmon_message text, -- text d'evenement si l'event se declenche
  CONSTRAINT tmon_cod_pkey PRIMARY KEY (tmon_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE monstre_terrain  OWNER TO delain;


INSERT INTO terrain (ter_nom, ter_desc) values
  ('eau', 'eau'),
  ('feu', 'feu'),
  ('boue', 'boue'),
  ('caillou', 'caillou'),
  ('vent', 'vent'),
  ('glace', 'glace'),
  ('herbe', 'herbe'),
  ('foret', 'foret');

ALTER TABLE monstre_generique   ADD COLUMN gmon_monture character varying(1) NOT NULL DEFAULT 'N';

ALTER TABLE perso ADD COLUMN perso_monture integer DEFAULT NULL;
ALTER TABLE perso ADD CONSTRAINT perso_monture_fk  FOREIGN KEY (perso_monture) REFERENCES perso(perso_cod) ON DELETE SET NULL;

ALTER TABLE monstre_terrain ADD CONSTRAINT tmon_gmon_cod_fk  FOREIGN KEY (tmon_gmon_cod) REFERENCES monstre_generique(gmon_cod) ON DELETE CASCADE;
ALTER TABLE monstre_terrain ADD CONSTRAINT tmon_ter_cod_fk  FOREIGN KEY (tmon_ter_cod) REFERENCES terrain(ter_cod) ON DELETE CASCADE;

INSERT INTO public.type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 17, 'Monture', 'ia_monture([perso])');
