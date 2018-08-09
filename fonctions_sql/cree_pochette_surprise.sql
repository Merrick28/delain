CREATE OR REPLACE FUNCTION public.cree_pochette_surprise()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_pochette_surprise : Procédure de création       */
/*         des pochettes surprises de Léno                       */
/*                                                               */
/* La sortie donne le nombre réussites et d échecs à l ajout de  */
/*pochettes                                                      */
/*****************************************************************/
/* Liste des modifications :                                     */
/* Reivax 27/01/2012 : Création                                  */
/*****************************************************************/
begin
	-- Suppression des pochettes existantes
--	select f_del_objet(obj_cod) from objets where obj_gobj_cod = 642;
	delete from perso_pochette;

	-- Création des nouvelles pochettes
	return cree_objet_tous_persos(642, 1);
end; $function$

