ALTER TABLE public.fonction_specifique_perso
   ALTER COLUMN pfonc_fonc_cod SET NOT NULL;

ALTER TABLE public.fonction_specifique_perso
   ALTER COLUMN pfonc_perso_cod SET NOT NULL;


DROP FUNCTION public.execute_fonction_specifique(integer, integer, integer);