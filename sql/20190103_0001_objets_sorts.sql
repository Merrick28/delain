CREATE SEQUENCE public.seq_objsort_cod
  INCREMENT 1
  MINVALUE 1
  START 1;
ALTER TABLE public.seq_objsort_cod
  OWNER TO delain;


CREATE TABLE public.objets_sorts
(
  objsort_cod integer NOT NULL DEFAULT nextval('seq_objsort_cod'::regclass),
  objsort_parent_cod integer, -- C'est un lien vers le objsort_cod d'un ensorcellement d'objet générique qui est le pere de celui-ci
  objsort_gobj_cod integer, -- Le code de l'objet générique sur lequel est rattaché le sort
  objsort_obj_cod integer, -- Le code de l'obet sur lequel est rattaché le sort  (le sort peu être rattaché à un objet spécifique)
  objsort_sort_cod integer NOT NULL, -- Code du sortilège utilisé
  objsort_nom character varying(50), -- Le nom du sort (si null le nom sera celui du sort réel)
  objsort_cout integer, -- Nombre de PA (si null le cout sera celui du sort réel)
  objsort_malchance numeric NOT NULL, -- Pourcentage d'échec possible (0=toujours réussi)
  objsort_nb_utilisation_max integer, -- Nombre d'utilisation max possible (illimité si null)
  objsort_nb_utilisation integer NOT NULL DEFAULT 0, -- Compteur du nombre d'utilisation
  objsort_equip_requis boolean NOT NULL DEFAULT false, -- si vrai: l'objet doit être équipé pour être utilisé
  CONSTRAINT objets_sorts_pkey PRIMARY KEY (objsort_cod),
  CONSTRAINT fk_objsort_cod FOREIGN KEY (objsort_parent_cod)
      REFERENCES public.objets_sorts (objsort_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.objets_sorts
  OWNER TO delain;
COMMENT ON TABLE public.objets_sorts
  IS 'Permet d’associer un sort à un objet spécifique ou a un générique';
COMMENT ON COLUMN public.objets_sorts.objsort_gobj_cod IS 'Le code de l''objet générique sur lequel est rattaché le sort';
COMMENT ON COLUMN public.objets_sorts.objsort_parent_cod IS 'C''est un lien vers le objsort_cod d''un ensorcellement d''objet générique qui est le pere de celui-ci ';
COMMENT ON COLUMN public.objets_sorts.objsort_obj_cod IS 'Le code de l''obet sur lequel est rattaché le sort  (le sort peu être rattaché à un objet spécifique)';
COMMENT ON COLUMN public.objets_sorts.objsort_sort_cod IS 'Code du sortilège utilisé ';
COMMENT ON COLUMN public.objets_sorts.objsort_nom IS 'Le nom du sort (si null le nom sera celui du sort réel) ';
COMMENT ON COLUMN public.objets_sorts.objsort_cout IS 'Nombre de PA (si null le cout sera celui du sort réel)';
COMMENT ON COLUMN public.objets_sorts.objsort_malchance IS 'Pourcentage d''échec possible (0=toujours réussi)';
COMMENT ON COLUMN public.objets_sorts.objsort_nb_utilisation_max IS 'Nombre d''utilisation max possible (illimité si null)';
COMMENT ON COLUMN public.objets_sorts.objsort_nb_utilisation IS 'Compteur du nombre d''utilisation';
COMMENT ON COLUMN public.objets_sorts.objsort_equip_requis IS 'si vrai: l''objet doit être équipé pour être utilisé';

CREATE TABLE public.objets_sorts_magie
(
  objsortm_perso_cod integer NOT NULL, -- Perso qui utilise un objet ensorcelé pour faire la magie
  objsortm_objsort_cod integer NOT NULL, -- Le sort de l'objet ensorcelé utilisé
  CONSTRAINT fk_objsortm_objsort_cod FOREIGN KEY (objsortm_objsort_cod)
      REFERENCES public.objets_sorts (objsort_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_objsortm_perso_cod FOREIGN KEY (objsortm_perso_cod)
      REFERENCES public.perso (perso_cod) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT objets_sorts_magie_objsortm_perso_cod_key UNIQUE (objsortm_perso_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.objets_sorts_magie
  OWNER TO delain;
GRANT ALL ON TABLE public.objets_sorts_magie TO delain;
COMMENT ON TABLE public.objets_sorts_magie
  IS 'Cette table sert à injecter de l''information dans les fonctions magie_commun, controle_sort et controle_sort_case pour pour les sorts réalisés à l''aide d''objet ensorcelé sans pour autant modifier toutes les fonctions nv_magie_*';
COMMENT ON COLUMN public.objets_sorts_magie.objsortm_perso_cod IS 'Perso qui utilise un objet ensorcelé pour faire la magie';
COMMENT ON COLUMN public.objets_sorts_magie.objsortm_objsort_cod IS 'Le sort de l''objet ensorcelé utilisé';
