CREATE OR REPLACE FUNCTION public.get_renommee_artisanat(numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_renommee_artisanat        */
/*   retourne la renommee du param        */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_renommee alias for $1;
begin
	select into code_retour renart_libelle from renommee_artisanat
	where renart_min <= v_renommee
		and renart_max > v_renommee;
	return code_retour;
end;	$function$

