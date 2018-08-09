CREATE OR REPLACE FUNCTION public.poison_araignee(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	v_monstre alias for $1;
	v_pos integer;
	v_poison integer;
	v_for integer;
	cpt_perso integer;
	v_perso integer;
	c_perso integer;
	v_pv_cible integer;
	texte_evt text;
begin
	select into v_pos,v_for
		ppos_pos_cod,perso_for
		from perso_position,perso
		where ppos_perso_cod = v_monstre
		and perso_cod = ppos_perso_cod;
	v_poison := floor(v_for / 3);
	select into cpt_perso
		count(perso_cod)
		from perso,perso_position
		where perso_type_perso in (1,3)
		and perso_actif = 'O'
		and perso_cod = ppos_perso_cod
		and ppos_pos_cod = v_pos
		and perso_tangible = 'O';
	if cpt_perso != 0 then
		c_perso := lancer_des(1,cpt_perso) - 1;
		select into v_perso,v_pv_cible
			perso_cod,perso_pv_max
			from perso,perso_position
			where perso_type_perso in (1,3)
			and perso_actif = 'O'
			and perso_tangible = 'O'
			and perso_cod = ppos_perso_cod
			and ppos_pos_cod = v_pos
			limit 1
			offset c_perso;
		perform ajoute_bonus(v_perso, 'POI', 5, v_poison);
		insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (2,v_monstre,v_perso,5*ln(v_pv_cible));
		texte_evt := '[attaquant] a empoisonné [cible] ';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     		values(nextval('seq_levt_cod'),14,now(),1,v_monstre,texte_evt,'O','O',v_monstre,v_perso);
   	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
	     	values(nextval('seq_levt_cod'),14,now(),1,v_perso,texte_evt,'N','O',v_monstre,v_perso);
		return v_perso;
	else
		return 0;	
	end if;
end;	$function$

