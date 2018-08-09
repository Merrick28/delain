CREATE OR REPLACE FUNCTION public.f_chance_memo_plus(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* fonction f_chance_memo_plus : renvoie la chance de mémo */
/*   d'un sort                                        */
/* params :                                           */
/*   $1 = perso_cod                                   */
/*   $2 = sort_cod                                    */
/******************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	num_sort alias for $2;
	nb_lancer_sort integer;
	intelligence integer;
	proba_memo_temp numeric;			-- probabilité de mémoriser le sort (temp)
	proba_memo integer;					-- probabilité de mémoriser le sort
	v_modif_chance integer;
	
begin
	select into nb_lancer_sort,intelligence,v_modif_chance
		pnbst_nombre,perso_int,perso_nb_amel_chance_memo
		from perso_nb_sorts_total,perso
		where pnbst_perso_cod = personnage
		and pnbst_sort_cod = num_sort
		and perso_cod = personnage;
	nb_lancer_sort := nb_lancer_sort + 1;
	proba_memo_temp := (intelligence * 4 * nb_lancer_sort) * 0.01;
	proba_memo_temp := proba_memo_temp * ((0.1 * v_modif_chance) + 1);
	proba_memo := round(proba_memo_temp);
	code_retour := proba_memo;
	return code_retour;
end;$function$

