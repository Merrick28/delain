CREATE OR REPLACE FUNCTION public.type_arme(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function type_arme : donne le type d arme equipe              */
/* On passe en paramètres                                        */
/*    $1 = perso                                                 */
/* En sortie, on a un entier                                     */
/*     0 = pas d arme                                            */
/*     1 = contact                                               */
/*     2 = distance                                              */
/*****************************************************************/
/* Créé le 15/09/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer; -- sert de code retour
	personnage alias for $1;
	type_arme varchar(2);
	code_objet integer;
	distance integer;	
	
begin
	-- etape 1 : on regarde si une arme est équipée
	select into code_objet,type_arme gobj_cod,gobj_distance
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
			code_retour := 1;
			return code_retour;
		else
			code_retour := 2;
			return code_retour;
		end if;
	end if;
end;
$function$

