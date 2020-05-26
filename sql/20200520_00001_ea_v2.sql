ALTER TABLE public.fonction_specifique_perso
   ALTER COLUMN pfonc_fonc_cod SET NOT NULL;

ALTER TABLE public.fonction_specifique_perso
   ALTER COLUMN pfonc_perso_cod SET NOT NULL;


DROP FUNCTION public.execute_fonction_specifique(integer, integer, integer);

DROP FUNCTION public.deb_tour_generique(integer, text, text, text, text, numeric, text) ;
DROP FUNCTION public.deb_tour_generique(integer, text, text, text, text, numeric, integer, text) ;
DROP FUNCTION public.deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text) ;
DROP FUNCTION public.deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text, integer) ;
DROP FUNCTION public.ea_lance_sort(integer, integer, text, integer, character, text, numeric, text);


DROP FUNCTION public.deb_tour_degats(integer) ;
DROP FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text) ;
DROP FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text, integer) ;

-- DROP FUNCTION public.execute_fonctions_ext(integer, integer, character, json);
DROP FUNCTION  public.execute_effet_auto_bmc(integer, text, numeric, numeric) ;
DROP FUNCTION  public.execute_effet_auto_mag(integer, integer, integer, text) ;