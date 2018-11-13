ALTER TABLE public.bonus_type
  ALTER COLUMN tonbus_libelle TYPE character varying (255) COLLATE pg_catalog."default";


ALTER TABLE public.news
  ALTER COLUMN news_auteur TYPE character varying (255) COLLATE pg_catalog."default";


ALTER TABLE public.objet_generique
  ALTER COLUMN gobj_nom TYPE character varying (255) COLLATE pg_catalog."default";

ALTER TABLE public.race
  ALTER COLUMN race_nom TYPE character varying (255) COLLATE pg_catalog."default";

ALTER TABLE public.renommee_magie
  ALTER COLUMN grenommee_libelle TYPE character varying (100) COLLATE pg_catalog."default";
