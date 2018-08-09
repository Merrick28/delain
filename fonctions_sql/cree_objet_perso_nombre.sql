CREATE OR REPLACE FUNCTION public.cree_objet_perso_nombre(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet  :Procédure de création d objet     en    */
/*   dans l'inventaire d'un perso                                */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet   générique)                        */
/*   $2 = le perso qui va récupérer l'objet                      */
/*   $3 = le nombre d'objets de ce type à créer                  */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*   -1 = le gmon_cod n existe pas                               */
/*    2 = cree perso anomalie                                    */
/*****************************************************************/
/* Liste des modifications :                                     */
/* Blade 22/11/2009 : intégration du cas particulier des         */
/* parchemins pour indice sur la composition des potions         */
/*****************************************************************/
declare
	code_retour text;
	v_gobj alias for $1;
	v_perso alias for $2;
	v_nombre alias for $3;

-- variables globales
	compt integer;
	v_code_objet integer;
	retour_cree_perso integer;
	texte text;

begin
/**********************************************/
/* Etape 1 : on insère dans perso les valeurs */
/**********************************************/
	code_retour := '';
	
	select into compt gobj_cod from objet_generique
		where gobj_cod = v_gobj;
	if not found then
		code_retour := '-1';
		return code_retour;
	end if;
	
	for compt in 1..v_nombre loop
		v_code_objet := nextval('seq_obj_cod');
		insert into objets (obj_cod,obj_gobj_cod) values (v_code_objet,v_gobj);
		insert into perso_objets (perobj_perso_cod,perobj_obj_cod,perobj_equipe,perobj_identifie) 
			values (v_perso,v_code_objet,'N','O');
		if v_gobj in ('723','829') then
			select into texte obj_text_texte from potions.objet_texte where obj_text_gobj_cod = v_gobj order by random() limit 1;
			update objets set obj_description = texte where obj_cod = v_code_objet;
		end if;
		code_retour := code_retour||trim(to_char(v_code_objet,'999999999'))||';';
	end loop;	
return code_retour;
end;$function$

