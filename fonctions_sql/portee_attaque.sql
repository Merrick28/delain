CREATE OR REPLACE FUNCTION public.portee_attaque(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function portee_attaque : donne la portée de l arme equipee   */
/* On passe en paramètres                                        */
/*    $1 = perso                                                 */
/* En sortie, on a un entier (portee)                            */
/*****************************************************************/
/* Créé le 07/03/2003                                            */
/* Liste des modifications :                                     */
/*   15/09/2003 : prise en compte des armes de jet               */
/*****************************************************************/
declare
	code_retour integer; -- sert de code retour
	personnage alias for $1;
	type_arme varchar(2);
	code_objet integer;
	distance integer;	
	
begin
	-- etape 1 : on regarde si une arme est équipée
	select into code_objet,type_arme obj_cod,gobj_distance
		from perso_objets,objets,objet_generique
		where perobj_perso_cod = personnage
		and perobj_equipe = 'O'
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 1;
	if code_objet is null then
		code_retour := 0;
		return code_retour;
	else
		if type_arme = 'N' then
			code_retour := 0;
			return code_retour;
		else
			select into distance obj_portee
				from objets
				where obj_cod = code_objet;
			code_retour := distance;
			return code_retour;
		end if;
	end if;
end;
$function$

