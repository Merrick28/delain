CREATE OR REPLACE FUNCTION public.f_del_objet_generique(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/**************************************************/
/* fonction f_del_objet_generique : détruit un    */
/*   objet générique d'un perso sans numéro       */
/* particulier																		*/
/* on passe en params :                           */
/*   $1 = gobj_cod de l'objet                     */
/*   $2 = perso_cod                               */
/**************************************************/
/* on a en retour un entier                       */
/**************************************************/
/* créé le 07/12/2009                             */
/* modifié le 05/07/2006 :                        */

/**************************************************/
declare
	code_retour integer;
	code_objet alias for $1;
	code_perso alias for $2;
	v_perobj_cod perso_objets.perobj_cod%type;
	num_objet integer;

	
begin
	code_retour := 0;
	select into v_perobj_cod,num_objet perobj_cod,perobj_obj_cod
		from perso_objets,objets
		where perobj_perso_cod = code_perso
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = code_objet
		limit 1;
	delete from transaction where tran_obj_cod = v_perobj_cod; -- On retire l'objet utilisée des transactions.
	delete from perso_objets where perobj_cod = v_perobj_cod;
	delete from perso_identifie_objet where pio_obj_cod = v_perobj_cod;
	delete from objet_position where pobj_obj_cod = v_perobj_cod;
	delete from objets where obj_cod = v_perobj_cod;
	return code_retour;
end;$function$

