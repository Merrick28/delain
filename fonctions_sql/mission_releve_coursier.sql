CREATE OR REPLACE FUNCTION public.mission_releve_coursier(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_releve_coursier                          */
/*   Actions à exécuter lorsqu’une mission                   */
/*   de type « coursier » est relevée par un perso           */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de l’instance de mission   */
/*   $2 = perso_cod : le perso qui prend la mission          */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 06/02/2013                                        */
/*************************************************************/
declare
	v_mpf_cod alias for $1;      -- Le code de l’instance de mission
	v_personnage alias for $2;   -- Le perso qui relève la mission

	v_gobj_cod integer;          -- Le gobj_cod du type d’objet à transporter

begin
	v_gobj_cod := 892;           -- Document confidentiel
	
	-- création de l’objet, et insertion dans la mission
	update mission_perso_faction_lieu
	set mpf_obj_cod = cree_objet_perso(v_gobj_cod, v_personnage)
	where mpf_cod = v_mpf_cod;
	
	return found;
end;$function$

