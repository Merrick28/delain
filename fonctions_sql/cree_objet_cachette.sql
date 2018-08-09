CREATE OR REPLACE FUNCTION public.cree_objet_cachette(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_objet_cachette : Procédure de création d objets */
/*   dans une cachette, afin de l'alimenter                      */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gobj_cod (objet   générique)                        */
/*   $2 = le numéro de la cachette dans laquelle il faut         */
/*        l'intégrer                                             */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
	v_gobj alias for $1;        -- code de l'objet
	v_cache alias for $2;       -- numéro de la cachette
	v_nombre alias for $3;      -- nombre d'objets

-- variables globales
	compt integer;
	v_code_objet integer;
	retour_cree_perso integer;
texte text;
begin

	code_retour := 0;
	
	select into compt gobj_cod from objet_generique
		where gobj_cod = v_gobj;
	if not found then
		code_retour := -1;
		return code_retour;
	end if;
	
	for compt in 1..v_nombre loop
		v_code_objet := nextval('seq_obj_cod');
		insert into objets (obj_cod,obj_gobj_cod) values (v_code_objet,v_gobj);
		if v_gobj in ('723','829') then
			select into texte obj_text_texte from potions.objet_texte where obj_text_gobj_cod = v_gobj order by random() limit 1;
			update objets set obj_description = texte where obj_cod = v_code_objet;
		end if;
		insert into cachettes_objets (objcache_cod_cache_cod,objcache_obj_cod) 
			values (v_cache,v_code_objet);
	end loop;	
return 0;
end;$function$

