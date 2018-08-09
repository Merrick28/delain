CREATE OR REPLACE FUNCTION public.get_renommee(numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_rennommee                 */
/*   retourne la rennommee du param       */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_renommee alias for $1;
begin
	select into code_retour renommee_libelle from renommee
		where renommee_min <= v_renommee
		and renommee_max > v_renommee;
	return code_retour;
end;
$function$

