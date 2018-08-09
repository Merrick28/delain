CREATE OR REPLACE FUNCTION public.is_milice(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* is_milice                                          */
/*   on passe en param : perso_cod                    */
/*   retourne 1 si le perso fait partie de la milice, */
/*   0 sinon                                          */
/******************************************************/
declare
	code_retour integer;
	personnage alias for $1;
begin
	select into code_retour pguilde_cod
		from guilde_perso
		where pguilde_perso_cod = personnage
		and pguilde_guilde_cod = getparm_n(63)
		and pguilde_valide = 'O';
	if found then
		return 1;
	else
		return 0;
	end if;
end;$function$

