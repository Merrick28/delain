
CREATE SEQUENCE public.seq_opost_cod
  INCREMENT 1;
ALTER TABLE public.seq_opost_cod
  OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_opost_cod TO delain;

CREATE TABLE public.objets_poste
(
  opost_cod integer NOT NULL DEFAULT nextval(('seq_opost_cod'::text)::regclass),
  opost_colis_cod integer, -- cod du colis (opost_cod d'un des objets du colis, null si un seul objet envoyé)
  opost_obj_cod integer NOT NULL, -- cod de l'objet
  opost_emet_perso_cod integer NOT NULL, -- cod du personnage emetteur
  opost_dest_perso_cod integer, -- cod du personnage destinataire
  opost_date_poste timestamp with time zone DEFAULT now(),
  opost_prix_demande integer,
  CONSTRAINT opost_cod_pkey PRIMARY KEY (opost_cod),
  CONSTRAINT fk_opost_dest_perso_cod FOREIGN KEY (opost_dest_perso_cod)
      REFERENCES public.perso (perso_cod) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT fk_opost_emet_perso_cod FOREIGN KEY (opost_emet_perso_cod)
      REFERENCES public.perso (perso_cod) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT fk_opost_obj_cod FOREIGN KEY (opost_obj_cod)
      REFERENCES public.objets (obj_cod) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.objets_poste
  OWNER TO delain;
GRANT ALL ON TABLE public.objets_poste TO delain;
  
COMMENT ON COLUMN public.objets_poste.opost_colis_cod IS 'cod du colis (opost_cod d''un des objets du colis, null si un seul objet envoyé)';
COMMENT ON COLUMN public.objets_poste.opost_obj_cod IS 'cod de l''objet';
COMMENT ON COLUMN public.objets_poste.opost_emet_perso_cod IS 'cod du personnage emetteur';
COMMENT ON COLUMN public.objets_poste.opost_dest_perso_cod IS 'cod du personnage destinataire';

INSERT INTO public.lieu_type (tlieu_libelle, tlieu_url)  VALUES ( 'Relais de la poste', 'relais_poste.php');

INSERT INTO type_evt (  tevt_libelle , tevt_texte )  VALUES
('Transaction', '[perso_cod1] a déposé un objet au relais de la poste.'),
('Transaction', '[perso_cod1] a retiré un objet au relais de la poste.'),
('Transaction', 'Un objet de [perso_cod1] a été consfisqué par le relais de la poste.');

INSERT INTO public.lieu_type (tlieu_libelle, tlieu_url)  VALUES ( 'Relais de la poste', 'relais_poste.php');


INSERT INTO public.type_evt (  tevt_libelle , tevt_texte )  VALUES
('Relais poste', '[perso_cod1] a déposé un objet au relais de la poste.'),
('Relais poste', '[perso_cod1] a retiré un objet au relais de la poste.'),
('Relais poste', 'Un objet de [perso_cod1] a été consfisqué par le relais de la poste.');