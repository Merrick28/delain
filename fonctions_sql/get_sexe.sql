CREATE OR REPLACE FUNCTION public.get_sexe(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_sexe                      */
/*   retourne le sexe d'un perso          */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_perso alias for $1;
begin
	select into code_retour perso_sex from perso where perso_cod = v_perso;
	return code_retour;
end;$function$

