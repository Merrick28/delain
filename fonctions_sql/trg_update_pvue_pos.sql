CREATE OR REPLACE FUNCTION public.trg_update_pvue_pos()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/****************************************/
/* trigger pour mettre à jour l''automap*/
/****************************************/
declare
	code_retour integer;
	personnage integer;
	v_pos integer;
	v_dist integer;
	v_x integer;
	v_y integer;
	v_etage integer;
	l_position record;
	v_pvue integer;
	v_familier integer;
	v_type_perso integer;
	v_query text;
	v_result_query text;
	v_result_query_i integer;
	v_num_etage integer;
begin
	personnage = NEW.ppos_perso_cod;
	v_pos = NEW.ppos_pos_cod;
	select into v_type_perso perso_type_perso
		from perso
		where perso_cod = personnage;
	code_retour := 0;
	if v_type_perso != 2 then
		v_dist := distance_vue(personnage);
		select into v_x,v_y,v_etage pos_x,pos_y,pos_etage
			from positions
			where pos_cod = v_pos;
		select into v_num_etage etage_cod
			from etage
			where etage_numero = v_etage;
		for l_position in select pos_cod from positions
				where pos_x between (v_x - v_dist) and (v_x + v_dist)
				and pos_y between (v_y - v_dist) and (v_y + v_dist)
				and pos_etage = v_etage loop
	--
		v_query = 'select pvue_perso_cod
			from perso_vue_pos_'||trim(to_char(v_num_etage,'9999999'))||' 
			where pvue_perso_cod = '||to_char(personnage,'99999999999999')||' 
			and pvue_pos_cod = '||to_char(l_position.pos_cod,'99999999999999')||';';
		execute v_query;
		get diagnostics v_result_query_i = ROW_COUNT;
		if v_result_query_i = 0 then
			v_query = 'insert into perso_vue_pos_'||trim(to_char(v_num_etage,'9999999'))||' (pvue_perso_cod,pvue_pos_cod)
				values ('||to_char(personnage,'99999999999999')||','||to_char(l_position.pos_cod,'99999999999999')||');';
			execute v_query;
		
		end if;
		
		/*select into v_pvue pvue_perso_cod
			from perso_vue_pos
			where pvue_perso_cod = personnage
			and pvue_pos_cod = l_position.pos_cod;
		if not found then
			insert into perso_vue_pos (pvue_perso_cod,pvue_pos_cod)
				values (personnage,l_position.pos_cod);
		end if;*/
		select into v_pvue vet_cod
			from etage_visite
			where vet_perso_cod = personnage
			and vet_etage = v_etage;
		if not found then
			insert into etage_visite (vet_perso_cod,vet_etage)
				values (personnage,v_etage);
		end if;
		end loop;
	-- familiers
		select into v_familier
			pfam_familier_cod
			from perso_familier,perso
			where pfam_perso_cod = personnage
			and pfam_familier_cod = perso_cod
			and perso_actif = 'O'
			and perso_type_perso = 3;
		if found then
			update perso_position
			set ppos_pos_cod = v_pos
			where ppos_perso_cod = v_familier;
		end if;
	end if;
	return NEW;
end;
$function$

