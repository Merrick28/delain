CREATE OR REPLACE FUNCTION public.is_riposte(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/**********************************************/
/* fonction is_riposte : dit si une riposte   */
/*  est en cours                              */
/* on passe en paramètres :                   */
/*  $1 = cible                                */
/*  $2 = attaquant                            */
/* on a en retour un entier :                 */
/*   0 = pas de riposte                       */
/*   1 = riposte                              */
/**********************************************/
declare
	code_retour integer;
	v_cible alias for $1;
	v_attaquant alias for $2;
	v_riposte integer;
	type_attaquant integer;
begin
	select into v_riposte riposte_cod
		from riposte
		where riposte_cible = v_cible
		and riposte_attaquant = v_attaquant;
	if not found then
	-- aucune riposte
		code_retour := 0;
	else
	-- riposte
		code_retour := 1;
	end if;
	/*select into type_attaquant perso_type_perso from perso where perso_cod = v_attaquant;
	if type_attaquant = 2 then
		code_retour := 0;
	end if;*/
	return code_retour;
end;
$function$

