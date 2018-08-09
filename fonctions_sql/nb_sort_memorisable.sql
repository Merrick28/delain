CREATE OR REPLACE FUNCTION public.nb_sort_memorisable(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction nb_sort_memorisable                        */
/*  donne le nombre de sorts mémorisables              */
/* on passe en paramètres le num de perso              */
/*******************************************************/
/* créé le 18/06/2004                                  */
/*******************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_race integer;
	v_nb_amel integer;
	temp integer;
	v_int integer;
begin
	select into v_race,v_nb_amel,v_int
		perso_race_cod,perso_amelioration_nb_sort,perso_int
		from perso
		where perso_cod = personnage;
	if v_race = 2 then
		temp := floor(v_int/2);
	else
		temp := v_int;
	end if;
	code_retour := temp + v_nb_amel;
	return code_retour;
end;$function$

