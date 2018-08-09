CREATE OR REPLACE FUNCTION public.get_renommee_guilde(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_renommee_guilde           */
/*   retourne la réputation du param      */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	code_retour2 text;
	v_guilde alias for $1;
	nb_perso integer;
	renomme_tot numeric;
	renom_magie_tot numeric;
	v_renommee numeric;
begin
	select into nb_perso count(pguilde_perso_cod)
		from guilde,guilde_perso,perso
		where guilde_cod = v_guilde
		and pguilde_guilde_cod = guilde_cod
		and pguilde_valide = 'O'
		and pguilde_perso_cod = perso_cod
		and perso_type_perso = 1
		and perso_actif = 'O';
	select into renomme_tot,renom_magie_tot sum(perso_renommee),sum(perso_renommee_magie)
		from guilde,guilde_perso,perso
		where guilde_cod = v_guilde
		and pguilde_guilde_cod = guilde_cod
		and pguilde_valide = 'O'
		and pguilde_perso_cod = perso_cod
		and perso_type_perso = 1
		and perso_actif = 'O';
/*if renom_magie_tot > renomme_tot then
	v_renommee = renom_magie_tot/ nb_perso;
	select into code_retour grenommee_libelle from renommee_magie
		where grenommee_min <= v_renommee
		and grenommee_max > v_renommee;
else
	v_renommee = renomme_tot / nb_perso;
	select into code_retour renommee_libelle from renommee
		where renommee_min <= v_renommee
		and renommee_max > v_renommee;
end if;*/
	v_renommee = renom_magie_tot/ nb_perso;
	select into code_retour grenommee_libelle from renommee_magie
		where grenommee_min <= v_renommee
		and grenommee_max > v_renommee;
	v_renommee = renomme_tot / nb_perso;
	select into code_retour2 renommee_libelle from renommee
		where renommee_min <= v_renommee
		and renommee_max > v_renommee;
code_retour := code_retour||' / '||code_retour2;
	return code_retour;
end;
$function$

