CREATE OR REPLACE FUNCTION public.cree_objet_tous_persos(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet_tous_persos : Procédure de création       */
/*         d objet dans l inventaire de chaque personnage        */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet   générique)                        */
/*   $2 = le type de persos à qui donner les objets              */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*****************************************************************/
/* Liste des modifications :                                     */
/* Reivax 27/01/2012 : Création                                  */
/*****************************************************************/

declare
	code_retour text;
	v_gobj alias for $1;
	v_type_perso alias for $2;
	ligne record;
	erreurs integer;
	reussites integer;

begin
	erreurs := 0;
	reussites := 0;
	for ligne in select perso_cod 
			from perso
			where perso_type_perso = v_type_perso
				AND perso_pnj = 0
	loop
		if cree_objet_perso(v_gobj, ligne.perso_cod) = -1 then
			erreurs := erreurs + 1;
		else
			reussites := reussites + 1;
		end if;
	end loop;
	code_retour := cast(reussites as varchar(6)) || ' ajouts réussis ; ' 
			|| cast(erreurs as varchar(6)) || ' ajouts échoués.';
	return code_retour;
end; $function$

