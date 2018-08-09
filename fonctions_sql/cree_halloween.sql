CREATE OR REPLACE FUNCTION public.cree_halloween()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	v_pos integer;
	temp integer;
	v_perso integer;
temp2 integer;
begin
temp2 := 0;
	for ligne in select * from objets
		where obj_gobj_cod = 178 loop
temp2 := temp2 + 1;
		select into v_pos

			pobj_pos_cod
			from objet_position
			where pobj_obj_cod = ligne.obj_cod;
		if found then
			temp := cree_monstre_pos(29,v_pos);
			temp := f_del_objet(ligne.obj_cod);
		end if;
		select into v_pos,v_perso
			ppos_pos_cod,ppos_perso_cod
			from perso_objets,perso_position
			where perobj_obj_cod = ligne.obj_cod
			and perobj_perso_cod = ppos_perso_cod;
		if found then
			temp := cree_monstre_pos(29,v_pos);
			update perso set perso_cible = v_perso where perso_cod = temp;
			temp := f_del_objet(ligne.obj_cod);
		end if;
	end loop;
temp2 := 0;
	for ligne in select * from objets
		where obj_gobj_cod = 179 loop
temp2 := temp2 + 1;
		select into v_pos
			pobj_pos_cod
			from objet_position
			where pobj_obj_cod = ligne.obj_cod;
		if found then
			temp := cree_monstre_pos(30,v_pos);
			temp := f_del_objet(ligne.obj_cod);
		end if;
		select into v_pos,v_perso
			ppos_pos_cod,ppos_perso_cod
			from perso_objets,perso_position
			where perobj_obj_cod = ligne.obj_cod
			and perobj_perso_cod = ppos_perso_cod;
		if found then
			temp := cree_monstre_pos(29,v_pos);
			update perso set perso_cible = v_perso where perso_cod = temp;
			temp := f_del_objet(ligne.obj_cod);
		end if;


	end loop;
	return 0;
end;$function$

