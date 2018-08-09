CREATE OR REPLACE FUNCTION public.cree_riposte(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* fonction cree_riposte : cree un enregistrement dans   */
/*   riposte en fonction de l'existant                   */
/* On passe en paramètres :                              */
/*   $1 = attaquant                                      */
/*   $2 = cible                                          */
/* on a un entier en sortie                              */
/*********************************************************/
declare
	code_retour integer;
	v_attaquant alias for $1;
	v_cible alias for $2;
	v_riposte integer;

begin
	select into v_riposte riposte_cod
		from riposte
		where riposte_attaquant = v_cible
		and riposte_cible = v_attaquant;
	if v_riposte is null then
	-- nouvel enregistrement
	-- on efface les enregistremens éventuellement existants
		delete from riposte
			where riposte_attaquant = v_attaquant
			and riposte_cible = v_cible;
		insert into riposte (riposte_attaquant,riposte_cible,riposte_nb_tours)
			values (v_attaquant,v_cible,getparm_n(26));
	-- sinon, on ne fait rien
	end if;
	code_retour := 0;
	return code_retour;
end;
$function$

