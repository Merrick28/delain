CREATE OR REPLACE FUNCTION public.f_cherche_compte(text)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function f_cherche_compt : recherche un perso_cod en fonction */
/*          du nom transormé en minuscule                        */
/* On passe en paramètres                                        */
/*    $1 = un texte contenant le nom du perso                    */
/* Le code sortie est un entier                                  */
/* 	-1 = perso non trouvé                                      */
/*    sinon, perso_cod                                           */
/*****************************************************************/
/* Créé le 23/04/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	v_code_retour integer;
	v_nom alias for $1;
	v_compt integer;
	
begin
	select into v_code_retour compt_cod
			from compte
			where lower(compt_nom) = lower(v_nom);
	if not found then
		v_code_retour := -1;
	end if;
	return v_code_retour;
end;
$function$

