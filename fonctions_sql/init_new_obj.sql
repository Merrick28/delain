CREATE OR REPLACE FUNCTION public.init_new_obj()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
		v_obon integer;
	v_nom text;
	v_nom_generique text;
	v_description text;
	v_poids numeric;
	v_usure numeric;
	v_seuil_force integer;
	v_seuil_dex integer;
	v_obcar_cod integer;
	v_valeur integer;
-- combat
	v_des_degats integer;
	v_val_des_degats integer;
	v_bonus_degats integer;
	v_armure integer;
	v_distance integer;
	v_chute numeric;
-- bonus à la création
	v_obon_cod integer;
	v_poison numeric;
	v_vampire numeric;
	v_degats integer;
	v_regen integer;
	v_aura_feu numeric;
	v_critique integer;
begin
	for ligne in select * from objets loop

		select into
		v_nom,
		v_nom_generique,
		v_poids,
		v_description,
		v_usure,
		v_seuil_force,
		v_seuil_dex,
		v_valeur,
		v_obcar_cod,
		v_obon_cod
		gobj_nom,
		gobj_nom_generique,
		gobj_poids,
		gobj_description,
		gobj_usure,
		gobj_seuil_force,
		gobj_seuil_dex,
		gobj_valeur,
		gobj_obcar_cod,
		gobj_obon_cod
		from objet_generique
		where gobj_cod = ligne.obj_gobj_cod;
	update objets
		set
		obj_nom = v_nom,
		obj_nom_generique = v_nom_generique,
		obj_poids = v_poids,
		obj_usure = v_usure,
		obj_seuil_force = v_seuil_force,
		obj_seuil_dex = v_seuil_dex,
		obj_valeur = v_valeur,
		obj_description = v_description
		where obj_cod = ligne.obj_cod;
/******************************************************/
/* partie 2 : spécifique combat                       */
/******************************************************/		
	select into v_des_degats,
		v_val_des_degats,
		v_bonus_degats,
		v_armure,
		v_distance,
		v_chute
		obcar_des_degats,
		obcar_val_des_degats,
		obcar_bonus_degats,
		obcar_armure,
		obcar_distance,
		obcar_chute
		from objets_caracs
		where obcar_cod = v_obcar_cod;
	if found then
		update objets
			set obj_des_degats = v_des_degats,
				obj_val_des_degats = v_val_des_degats,
				obj_bonus_degats = v_bonus_degats,
				obj_armure = v_armure,
				obj_distance = v_distance,
				obj_chute = v_chute
			where obj_cod = ligne.obj_cod;	
	end if;
	
	
	select into v_poison,
		v_vampire,
		v_degats,
		v_regen,
		v_aura_feu,
		v_critique,
		v_armure
		obon_poison,
		obon_vampire,
		obon_degats,
		obon_regen,
		obon_aura_feu,
		obon_critique,
		obon_armure
		from bonus_objets
		where obon_cod = v_obon_cod;
	if found then
			update objets
				set obj_obon_cod = v_obon,
				obj_poison = v_poison,
				obj_vampire = v_vampire,
				obj_regen = v_regen,
				obj_aura_feu = v_aura_feu,
				obj_critique = v_critique,
				obj_bonus_degats = obj_bonus_degats + v_degats,
				obj_armure = obj_armure + v_armure
				where obj_cod = ligne.obj_cod;
	end if;
		
	end loop;
	return 'OK';
end;$function$

