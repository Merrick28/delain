CREATE OR REPLACE FUNCTION public.degats_perso(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction degats_perso : affiche les degats min et max  */
/*  d un perso en fonction de son arme et bonus           */
/* on passe en parametre :                                */
/*  $1 = perso_cod                                        */
/* on a en sortie une chaine séparée par ;                */
/*   0 = degats min                                       */
/*   1 = degats max                                       */
/**********************************************************/
declare
	code_retour text;
	personnage alias for $1;
	compt integer;
	nb_des_attaque integer;
	valeur_des_attaque integer;
	bonus_attaque integer;
	v_amelioration_degats integer;
	v_amel_dist integer;
	v_nb_des_degats integer;
	v_val_des_degats integer;
	is_distance varchar(2);
	bonus_perm integer;
	deg_min integer;
	deg_max integer;
	bonus_sort integer;
	v_bonus_toucher integer;

begin
	bonus_sort := valeur_bonus(personnage, 'DEG');
	select into v_amelioration_degats,v_amel_dist,v_nb_des_degats,v_val_des_degats
		perso_amelioration_degats,perso_amel_deg_dex,perso_nb_des_degats,perso_val_des_degats 
		from perso
		where perso_cod = personnage;
	select into nb_des_attaque,valeur_des_attaque,bonus_attaque,is_distance
			obj_des_degats,obj_val_des_degats,obj_bonus_degats,gobj_distance
			from perso_objets,objets,objet_generique,objets_caracs
			where perobj_perso_cod = personnage
			and perobj_equipe = 'O'
			and perobj_obj_cod = obj_cod
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod = 1
			and gobj_obcar_cod = obcar_cod;
	if not found then -- pas d arme equipee, on passe aux poings
		nb_des_attaque := v_nb_des_degats;
		valeur_des_attaque := v_val_des_degats;
		bonus_attaque := 0;

		deg_min := nb_des_attaque + bonus_attaque + v_amelioration_degats + bonus_degats_melee(personnage) + bonus_sort;
		deg_max := (nb_des_attaque*valeur_des_attaque) + bonus_attaque + v_amelioration_degats + bonus_degats_melee(personnage) + bonus_sort;
			deg_min := deg_min + valeur_bonus(personnage, 'PDC');
			deg_max := deg_max + valeur_bonus(personnage, 'PDC');
	else -- on a une arme equipee
/* Cas des armes à distance */
		if is_distance = 'O' then
			deg_min := nb_des_attaque  + bonus_attaque + v_amel_dist + bonus_sort;
			deg_max := (nb_des_attaque*valeur_des_attaque) + bonus_attaque + v_amel_dist + bonus_sort;
			deg_min := deg_min + valeur_bonus(personnage, 'PDD');
			deg_max := deg_max + valeur_bonus(personnage, 'PDD');
		else
			deg_min := nb_des_attaque + bonus_attaque + v_amelioration_degats + bonus_degats_melee(personnage) + bonus_sort;
			deg_max := (nb_des_attaque*valeur_des_attaque) + bonus_attaque + v_amelioration_degats + bonus_degats_melee(personnage) + bonus_sort;
			deg_min := deg_min + valeur_bonus(personnage, 'PDC');
			deg_max := deg_max + valeur_bonus(personnage, 'PDC');
		end if;
	end if; -- arme equipee
	code_retour := trim(to_char(deg_min,'9999'))||';'||trim(to_char(deg_max,'9999'))||';';
	return code_retour;
end;$function$

