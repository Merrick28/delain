CREATE OR REPLACE FUNCTION public.tue_perso2(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction tue_perso2                                       */
/*   accomplit les actions consécutives à la mort d un perso */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod attaquant                                */
/*   $2 = perso_cod cible                                    */
/* on a en sortie un entier                                  */
/*   1 = px gagnés par l attaquant                           */
/*************************************************************/
/* Créé le 22/05/2003                                        */
/*************************************************************/
declare
	code_retour integer;
	v_attaquant alias for $1;
	v_cible alias for $2;
	temp_txt text;
begin
	temp_txt := tue_perso(v_attaquant,v_cible);
	code_retour := to_number(temp_txt,'99999');
	return code_retour;
end;
$function$

