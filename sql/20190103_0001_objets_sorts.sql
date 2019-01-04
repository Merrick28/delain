CREATE SEQUENCE public.seq_objsort_cod
  INCREMENT 1
  MINVALUE 1
  START 1;
ALTER TABLE public.seq_objsort_cod
  OWNER TO delain;


CREATE TABLE public.objets_sorts
(
  objsort_cod integer NOT NULL DEFAULT nextval('seq_objsort_cod'::regclass),
  objsort_gobj_cod integer, -- Le code de l'objet générique sur lequel est rattaché le sort
  objsort_obj_cod integer, -- Le code de l'obet sur lequel est rattaché le sort  (le sort peu être rattaché à un objet spécifique)
  objsort_sort_cod integer NOT NULL, -- Code du sortilège utilisé
  objsort_nom character varying(50), -- Le nom du sort (si null le nom sera celui du sort réel)
  objsort_cout integer, -- Nombre de PA (si null le cout sera celui du sort réel)
  objsort_malchance numeric NOT NULL, -- Pourcentage d'échec possible (0=toujours réussi)
  objsort_nb_utilisation integer, -- Nombre d'utilisation possible (illimité si null)
  objsort_equip_requis boolean NOT NULL DEFAULT false, -- si vrai: l'objet doit être équipé pour être utilisé
  CONSTRAINT objets_sorts_pkey PRIMARY KEY (objsort_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.objets_sorts
  OWNER TO delain;
COMMENT ON TABLE public.objets_sorts
  IS 'Permet d’associer un sort à un objet spécifique ou a un générique';
COMMENT ON COLUMN public.objets_sorts.objsort_gobj_cod IS 'Le code de l''objet générique sur lequel est rattaché le sort';
COMMENT ON COLUMN public.objets_sorts.objsort_obj_cod IS 'Le code de l''obet sur lequel est rattaché le sort  (le sort peu être rattaché à un objet spécifique)';
COMMENT ON COLUMN public.objets_sorts.objsort_sort_cod IS 'Code du sortilège utilisé ';
COMMENT ON COLUMN public.objets_sorts.objsort_nom IS 'Le nom du sort (si null le nom sera celui du sort réel) ';
COMMENT ON COLUMN public.objets_sorts.objsort_cout IS 'Nombre de PA (si null le cout sera celui du sort réel)';
COMMENT ON COLUMN public.objets_sorts.objsort_malchance IS 'Pourcentage d''échec possible (0=toujours réussi)';
COMMENT ON COLUMN public.objets_sorts.objsort_nb_utilisation IS 'Nombre d''utilisation possible (illimité si null)';
COMMENT ON COLUMN public.objets_sorts.objsort_equip_requis IS 'si vrai: l''objet doit être équipé pour être utilisé';

