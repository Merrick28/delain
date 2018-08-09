CREATE OR REPLACE FUNCTION public.f_vue_renommee(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction f_vue_renommee                */
/*   retourne la renommee du perso        */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_renom numeric;
	v_renom_magie numeric;
	v_renom_art numeric;
	v_renommee numeric;
begin
	select into v_renom, v_renom_magie, v_renom_art
		perso_renommee, perso_renommee_magie, perso_renommee_artisanat
	from perso
	where perso_cod = personnage;

	if v_renom > v_renom_magie AND v_renom > v_renom_art then
		code_retour := get_renommee(v_renom);
	elsif v_renom_magie > v_renom AND v_renom_magie > v_renom_art then
		code_retour := get_renommee_magie(v_renom_magie);
	else
		code_retour := get_renommee_artisanat(v_renom_art);
	end if;
	
	return code_retour;
end;
$function$

