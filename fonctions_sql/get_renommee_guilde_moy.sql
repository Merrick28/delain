CREATE OR REPLACE FUNCTION public.get_renommee_guilde_moy(integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/******************************************/
/* fonction get_renommee_guilde_moy       */
/*   retourne la réputation du param      */
/*   passé en $1                          */
/******************************************/
declare
	code_retour text;
	v_guilde alias for $1;
	nb_perso integer;
	renomme_tot numeric;
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
	select into renomme_tot sum(perso_renommee)
		from guilde,guilde_perso,perso
		where guilde_cod = v_guilde
		and pguilde_guilde_cod = guilde_cod
		and pguilde_valide = 'O'
		and pguilde_perso_cod = perso_cod
		and perso_type_perso = 1
		and perso_actif = 'O';
	v_renommee = renomme_tot / nb_perso;
	return v_renommee;
end;
$function$

