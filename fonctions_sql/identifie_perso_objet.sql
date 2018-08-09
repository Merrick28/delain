CREATE OR REPLACE FUNCTION public.identifie_perso_objet(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare	
	v_perso alias for $1;
	v_obj_cod integer;
	ligne record;
begin
	for ligne in 
		select perobj_cod 
		from perso_objets,objets,objet_generique,type_objet
		where perobj_identifie != 'O'
		and perobj_obj_cod = obj_cod 
		and perobj_perso_cod = v_perso
		and obj_gobj_cod = gobj_cod 
		and gobj_tobj_cod = tobj_cod
		and tobj_identifie_auto = 1 loop
		update perso_objets
			set perobj_identifie = 'O'
			where perobj_cod = ligne.perobj_cod;
	end loop;
	return 0;
end;
	
	
	$function$

