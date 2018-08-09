CREATE OR REPLACE FUNCTION public.init_compteur()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***************************/
/* init_compteur           */
/***************************/
declare
	code_retour integer;
begin
	update parametres
		set parm_valeur = 0 where parm_cod in (64,65,66);
	return 0;
end;

$function$

