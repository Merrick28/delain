CREATE OR REPLACE FUNCTION public.dlt_passee(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function dlt_passee : dit si la dlt d un jeour est passe      */
/*                                                               */
/* On passe en paramètres                                        */
/*    $1 = perso à effacer                                       */
/* Le code sortie est :                                          */
/*    0 = dlt non passe                                          */
/*    1 = dlt passee                                             */
/*****************************************************************/
/* Créé le 26/05/2003                                            */
/*****************************************************************/
declare
	code_retour integer;
	personnage alias for $1; 
	dlt perso.perso_dlt%type;
	
begin
	code_retour := 0; /* par défaut, tout s est bien passé */
	select into dlt perso_dlt from perso where perso_cod = personnage;
	if dlt < now() then
		code_retour := 1;
	end if;
	return code_retour;
end;
$function$

