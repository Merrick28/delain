CREATE OR REPLACE FUNCTION public.get_karma_guilde(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_karma_guilde              */
/*   retourne la réputation du param      */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_guilde alias for $1;
	nb_perso integer;
	karma_tot numeric;
	v_karma numeric;
begin
	select into nb_perso count(pguilde_perso_cod)
		from guilde,guilde_perso,perso
		where guilde_cod = v_guilde
		and pguilde_guilde_cod = guilde_cod
		and pguilde_valide = 'O'
		and pguilde_perso_cod = perso_cod
		and perso_type_perso = 1
		and perso_actif = 'O';
	select into karma_tot sum(perso_kharma)
		from guilde,guilde_perso,perso
		where guilde_cod = v_guilde
		and pguilde_guilde_cod = guilde_cod
		and pguilde_valide = 'O'
		and pguilde_perso_cod = perso_cod
		and perso_type_perso = 1
		and perso_actif = 'O';
	v_karma = karma_tot / nb_perso;
	select into code_retour karma_libelle from karma
		where karma_min <= v_karma
		and karma_max > v_karma;
	return code_retour;
end;
$function$

