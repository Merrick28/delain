CREATE OR REPLACE FUNCTION public.get_karma(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_karma                     */
/*   retourne la réputation du param      */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_karma alias for $1;
begin
	select into code_retour karma_libelle from karma
		where karma_min <= v_karma
		and karma_max > v_karma;
	return code_retour;
end;
$function$

CREATE OR REPLACE FUNCTION public.get_karma(numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_karma                     */
/*   retourne la réputation du param      */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_karma alias for $1;
begin
	select into code_retour karma_libelle from karma
		where karma_min <= v_karma
		and karma_max > v_karma;
	return code_retour;
end;
$function$

