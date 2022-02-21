ALTER TABLE public.objets_bm
   ADD COLUMN objbm_equip_requis boolean NOT NULL DEFAULT true;

ALTER TABLE quetes.aquete_perso_notes
  OWNER TO delain;
