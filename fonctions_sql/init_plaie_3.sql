CREATE OR REPLACE FUNCTION public.init_plaie_3()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction init_plaie_3 : plaie de Delain n° 3        */
/*  créé des monstres à la place des objets au sol     */
/*******************************************************/
declare
	v_type_objet integer;			-- type de l'objet
	ligne record;
	v_monstre integer;				-- gmon_cod du monstre créé
	temp integer;
	
begin
	for ligne in 
		select gobj_tobj_cod,obj_cod,pobj_pos_cod from objet_position,objets,objet_generique,positions 
			where pobj_obj_cod = obj_cod
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod in (1,2,4,8,11)
			and pobj_pos_cod = pos_cod
			and pos_etage = -6 loop
		if ligne.gobj_tobj_cod = 1 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 2 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 4 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 8 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 11 then
			v_monstre := 16;
		end if;
		-- on efface l'objet
		temp := f_del_objet(ligne.obj_cod);
		-- on créé un monstre
		temp := cree_monstre_pos(v_monstre,ligne.pobj_pos_cod);
	end loop;
	return 0;
end;$function$

CREATE OR REPLACE FUNCTION public.init_plaie_3(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction init_plaie_3 : plaie de Delain n° 3        */
/*  créé des monstres à la place des objets au sol     */
/*******************************************************/
declare
	v_type_objet integer;			-- type de l'objet
	ligne record;
	v_monstre integer;				-- gmon_cod du monstre créé
	v_etage alias for $1;
	temp integer;
	
begin
	for ligne in 
		select gobj_tobj_cod,obj_cod,pobj_pos_cod from objet_position,objets,objet_generique,positions 
			where pobj_obj_cod = obj_cod
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod in (1,2,4,8,11)
			and pobj_pos_cod = pos_cod
			and pos_etage = v_etage loop
		if ligne.gobj_tobj_cod = 1 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 2 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 4 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 8 then
			v_monstre := 16;
		end if;
		if ligne.gobj_tobj_cod = 11 then
			v_monstre := 16;
		end if;
		-- on efface l'objet
		temp := f_del_objet(ligne.obj_cod);
		-- on créé un monstre
		temp := cree_monstre_pos(v_monstre,ligne.pobj_pos_cod);
	end loop;
	return 0;
end;$function$

