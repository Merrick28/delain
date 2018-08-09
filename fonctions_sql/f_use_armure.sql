CREATE OR REPLACE FUNCTION public.f_use_armure(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction f_use_armure : use l'armure du perso passé en $1 */
/*  en fonction du parametre de dégats passés en $2          */
/*************************************************************/
/* on a en retour :                                          */
/*   1 = OK                                                  */
/*   2 = armure brisée                                       */
/*************************************************************/
declare
	code_retour integer;
	v_armure integer;					-- valeur armure physique
	num_objet integer;				-- obj_cod de l'armure
	personnage alias for $1;
	degats alias for $2;
	v_usure numeric;					-- usure de l'arme
	temp integer;
	v_etat numeric;					-- etat de l'objet après coup
	texte_evt text;
begin
	select into v_armure,num_objet,v_usure
		obj_armure,obj_cod,obj_usure
		from perso_objets,objets,objet_generique,objets_caracs
		where perobj_perso_cod = personnage
		and perobj_equipe = 'O'
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 2
		and gobj_obcar_cod = obcar_cod;
	if not found then
		return 1;
	end if;
	if degats <= v_armure then
		return 1;
	end if;
-- on commence à abimer
	update objets
		set obj_etat = obj_etat - v_usure
		where obj_cod = num_objet;
	if degats > (v_armure * 2) then
		update objets
			set obj_etat = obj_etat - v_usure
			where obj_cod = num_objet;
	end if;
	if degats > (v_armure * 3) then
		update objets
			set obj_etat = obj_etat - v_usure
			where obj_cod = num_objet;
	end if;
	select into v_etat obj_etat
		from objets
		where obj_cod = num_objet;
	if v_etat <= 0 then
		temp := f_del_objet(num_objet);
		return 2;
		texte_evt := 'L''armure de [perso_cod1] s''est brisée !';
		insert into ligne_evt (levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values
			(36,now(),personnage,texte_evt,'N','O');
	else
		return 1;
	end if;
end;$function$

