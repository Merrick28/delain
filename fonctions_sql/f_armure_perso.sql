CREATE OR REPLACE FUNCTION public.f_armure_perso(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function f_armure_perso : retourne la valeur de l armure du   */
/*   perso passé en $1                                           */
/* Le code sortie est un entier                                  */
/*****************************************************************/
/* Créé le 06/05/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour text;
	v_personnage alias for $1;
	compt integer;
	v_amelioration_armure integer;
	v_armure integer;
	v_armure_casque integer;
	v_bonus integer;
        v_bonus_baton integer;
begin
	select into v_amelioration_armure, v_armure
		perso_amelioration_armure, f_armure_perso_physique(perso_cod)
	from perso
	where perso_cod = v_personnage;

	v_armure := v_armure + v_amelioration_armure;
	v_armure := v_armure + valeur_bonus(v_personnage, 'ARM') + valeur_bonus(v_personnage, 'PAR');
	if v_armure < 0 then
		v_armure := 0;
	end if;
	code_retour := v_armure;
	return code_retour;
end;
$function$

