CREATE OR REPLACE FUNCTION public.f_armure_perso_physique(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function f_armure_perso_physique : retourne la valeur de      */
/* l armure physique du perso passé en $1 (hors bonus et amél)   */
/* Le code sortie est un entier                                  */
/*****************************************************************/
/* Créé le 12/05/2004                                            */
/* Liste des modifications :                                     */
/* 29/01/2013 - Reivax - Calcul d’armure tout objet équipé confondus*/
/*****************************************************************/
declare
	v_personnage alias for $1;
	v_armure integer;
begin
	select into v_armure sum(obj_armure)
	from perso_objets
	inner join objets on obj_cod = perobj_obj_cod
	where perobj_perso_cod = v_personnage
		and perobj_equipe = 'O';
	if not found or v_armure is null then 
		v_armure := 0;
	end if;

	return v_armure;
end;
$function$

