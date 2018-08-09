CREATE OR REPLACE FUNCTION public.trans_crap(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	v_monstre alias for $1;
	v_nb_perso integer;
	v_pos integer;
	v_perso_transforme integer;
	v_des integer;
	texte_evt text;
	v_nom_attaquant text;
	v_nom_cible text;
	
begin
	select into v_pos
		ppos_pos_cod
	from perso_position
	where ppos_perso_cod = v_monstre;

	select into v_nb_perso
		count(perso_cod)
	from perso,perso_position
	where perso_crapaud = 0
		and perso_type_perso = 1
		and perso_actif = 'O'
		and perso_cod = ppos_perso_cod
		and perso_pnj <> 1
		and ppos_pos_cod = v_pos;
	if v_nb_perso != 0 then
		v_des := lancer_des(1,v_nb_perso) - 1;
		select into v_perso_transforme,v_nom_cible
			perso_cod,perso_nom
		from perso,perso_position
		where perso_crapaud = 0
			and perso_type_perso = 1
			and perso_actif = 'O'
			and perso_cod = ppos_perso_cod
			and ppos_pos_cod = v_pos
			and perso_pnj <> 1
		offset v_des
		limit 1;

		update perso
		set perso_crapaud = 1,
			perso_nb_crap = perso_nb_crap + 1,
			perso_ancien_avatar = perso_avatar
		where perso_cod = v_perso_transforme;

		update perso
		set perso_avatar = 'crapaud.jpg'
		where perso_cod = v_perso_transforme;

		texte_evt := '[attaquant] a transformé [cible] en crapaud !';
		perform insere_evenement(v_monstre, v_perso_transforme, 56, texte_evt, 'O', NULL);
	end if;
	return 0;
end;$function$

