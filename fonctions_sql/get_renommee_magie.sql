CREATE OR REPLACE FUNCTION public.get_renommee_magie(numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_rennommee_magie           */
/*   retourne la rennommee du param       */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_renommee alias for $1;
begin
	select into code_retour grenommee_libelle from renommee_magie
		where grenommee_min <= v_renommee
		and grenommee_max > v_renommee;
	return code_retour;
end;
$function$

