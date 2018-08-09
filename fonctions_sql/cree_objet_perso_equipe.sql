CREATE OR REPLACE FUNCTION public.cree_objet_perso_equipe(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet  :Procédure de création d objet     en    */
/*   position aleatoire                                          */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet   générique)                        */
/*   $2 = le niveau ou il doit apparaitre                        */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*   -1 = l'objet n'existe pas                                   */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_gobj alias for $1;
	v_perso alias for $2;

-- variables globales
	compt integer;
	v_code_objet integer;
	retour_cree_perso integer;
	texte text;
begin
/************************************************/
/* Etape 1 : on insère dans l'étage les valeurs */
/************************************************/
	code_retour := 0;
	v_code_objet := nextval('seq_obj_cod');
	select into compt gobj_cod from objet_generique
		where gobj_cod = v_gobj;
	if not found then
		code_retour := -1;
		return code_retour;
	end if;
		insert into objets (obj_cod,obj_gobj_cod) values (v_code_objet,v_gobj);
		if v_gobj in ('723','829') then
			select into texte obj_text_texte from potions.objet_texte where obj_text_gobj_cod = v_gobj order by random() limit 1;
			update objets set obj_description = texte where obj_cod = v_code_objet;
		end if;

	
/*************************/
/* Etape 4 : on le place */
/*************************/
	insert into perso_objets (perobj_obj_cod,perobj_perso_cod,perobj_equipe,perobj_identifie) 
		values (v_code_objet,v_perso,'O','O');
	code_retour := v_code_objet;
return code_retour;
end;
 $function$

