CREATE OR REPLACE FUNCTION public.update_automap(integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* update_pvue_pos                                   */
/*****************************************************/
declare
	personnage alias for $1;
	v_pos integer;		--position du perso
	v_dist integer;
	v_x integer;
	v_y integer;
	v_etage integer;
	l_position record;
	v_pvue integer;
	v_num_etage integer;
	v_query text;
	v_result_query text;

	
	
begin
	v_dist := distance_vue(personnage);
	select into v_pos ppos_pos_cod from perso_position where ppos_perso_cod = personnage;
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
			from perso_vue_pos_'||trim(to_char(v_num_etage,'99999999'))||' 
			where pvue_perso_cod = '||to_char(personnage,'99999999999999')||' 
			and pvue_pos_cod = '||to_char(l_position.pos_cod,'99999999999999')||';';
		execute v_query;
		get diagnostics v_result_query = ROW_COUNT;
		if v_result_query = 0 then
			v_query = 'insert into perso_vue_pos_'||trim(to_char(v_num_etage,'9999999'))||' (pvue_perso_cod,pvue_pos_cod)
				values ('||to_char(personnage,'99999999999999')||','||to_char(l_position.pos_cod,'99999999999999')||');';
			execute v_query;
		
		end if;
		
		select into v_pvue vet_cod
			from etage_visite
			where vet_perso_cod = personnage
			and vet_etage = v_etage;
		if not found then
			insert into etage_visite (vet_perso_cod,vet_etage)
				values (personnage,v_etage);
		end if;
	end loop;
end;
$function$

