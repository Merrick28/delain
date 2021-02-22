ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_distance integer NOT NULL DEFAULT 0;
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_aggressif character varying(1) NOT NULL DEFAULT 'N';
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_soutien character varying(1) NOT NULL DEFAULT 'N';
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_soi_meme character varying(1) NOT NULL DEFAULT 'O';
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_monstre character varying(1) NOT NULL DEFAULT 'O';
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_joueur character varying(1) NOT NULL DEFAULT 'O';
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_case character varying(1) NOT NULL DEFAULT 'N';
ALTER TABLE public.objets_sorts_bm  ADD COLUMN objsortbm_bonus_mode character varying(1) NOT NULL DEFAULT 'S';

