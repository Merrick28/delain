
ALTER TABLE public.objets_sorts_bm
   ADD COLUMN objsortbm_bonus_familier character varying(1) NOT NULL DEFAULT 'O'::character varying;

UPDATE objets_sorts_bm SET objsortbm_bonus_familier=objsortbm_bonus_monstre;