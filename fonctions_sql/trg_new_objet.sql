CREATE OR REPLACE FUNCTION public.trg_new_objet()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* trigger cree_objet : à la création des objets      */
/******************************************************/
declare	
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
	v_sort_cod integer;
	v_vue integer;
	v_desequipable text;
-- combat
	v_des_degats integer;
	v_val_des_degats integer;
	v_bonus_degats integer;
	v_armure integer;
	v_distance text;
	v_chute numeric;
	v_portee integer;
-- bonus à la création
	v_obon_cod integer;
	v_poison numeric;
	v_vampire numeric;
	v_degats integer;
	v_regen integer;
	v_aura_feu numeric;
	v_critique integer;
	v_bonus_armure integer;
	v_chance_drop integer;
	v_deposable text;
-- enchantement
	v_gobj_chance_enchant integer;
	v_des integer;
--
	v_type_objet integer;
	v_nom_famille text;
	v_frune_cod integer;
	v_niveau_min integer;
begin
/******************************************************/
/* partie 1 : les valeurs de base                     */
/******************************************************/
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
		v_obon_cod,
		v_vue,
		v_poison,
		v_vampire,
		v_bonus_armure,
		v_aura_feu,
		v_regen,
		v_distance,
		v_portee,
		v_critique,
		v_sort_cod,
		v_chance_drop,
		v_gobj_chance_enchant,v_deposable,
		v_type_objet,v_frune_cod,
		v_niveau_min,
		v_desequipable
		gobj_nom,
		gobj_nom_generique,
		gobj_poids,
		gobj_description,
		gobj_usure,
		gobj_seuil_force,
		gobj_seuil_dex,
		gobj_valeur,
		gobj_obcar_cod,
		gobj_obon_cod,
		gobj_bonus_vue,
		gobj_poison,
		gobj_vampire,
		gobj_bonus_armure,
		gobj_aura_feu,
		gobj_regen,
		gobj_distance,
		gobj_portee,
		gobj_critique,
		gobj_sort_cod,
		gobj_chance_drop,
		gobj_chance_enchant,gobj_deposable,gobj_tobj_cod,gobj_frune_cod,
		gobj_niveau_min,
		gobj_desequipable
		from objet_generique
		where gobj_cod = NEW.obj_gobj_cod;
	update objets
		set
		obj_nom = v_nom,
		obj_nom_generique = v_nom_generique,
		obj_nom_porte = v_nom_generique,
		obj_poids = v_poids,
		obj_usure = v_usure,
		obj_seuil_force = v_seuil_force,
		obj_seuil_dex = v_seuil_dex,
		obj_valeur = v_valeur,
		obj_description = v_description,
		obj_bonus_vue = v_vue,
		obj_poison = v_poison,
		obj_vampire = v_vampire,
		obj_aura_feu = v_aura_feu,
		obj_regen = v_regen,
		obj_critique = v_critique,
		obj_sort_cod = v_sort_cod,
		obj_chance_drop = v_chance_drop,
		obj_deposable = v_deposable,
		obj_portee = v_portee,
		obj_niveau_min = v_niveau_min,
		obj_desequipable = v_desequipable
		where obj_cod = NEW.obj_cod;
/***************************************/
	if v_type_objet = 5 then
		select into v_nom_famille
			frune_desc
			from 
			rune_famille
			where frune_cod = v_frune_cod;
		update objets
			set obj_famille_rune = v_nom_famille,
			obj_frune_cod =  v_frune_cod where obj_cod = NEW.obj_cod;
			
	end if;
	
/******************************************************/
/* partie 2 : spécifique combat                       */
/******************************************************/		
	select into v_des_degats,
		v_val_des_degats,
		v_bonus_degats,
		v_armure,
		v_chute
		obcar_des_degats,
		obcar_val_des_degats,
		obcar_bonus_degats,
		obcar_armure,
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
			where obj_cod = NEW.obj_cod;	
	end if;
	update objets 
		set obj_armure = obj_armure + v_bonus_armure where obj_cod = NEW.obj_cod;	
/***************************************************/
/* partie 3 : enchantements                        */
/***************************************************/
	if v_gobj_chance_enchant > 0 then
		v_des := lancer_des(1,100);
		if v_des <= v_gobj_chance_enchant then
			update objets
				set obj_enchantable = 1
				where obj_cod = NEW.obj_cod;	
		end if;
	end if;
/*********************************************/
/* logs                                      */
/*********************************************/
	/*insert into log_objet
		(llobj_obj_cod,llobj_type_action)
		values
		(NEW.obj_cod,'Création objet');*/
	return NEW;
end;$function$

