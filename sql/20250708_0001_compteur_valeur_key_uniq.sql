ALTER TABLE IF EXISTS public.compteur_valeur
    ADD CONSTRAINT comptval_perso_cod_compteur_cod_uniq UNIQUE (comptval_perso_cod, comptval_compteur_cod);