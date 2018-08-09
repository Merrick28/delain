CREATE OR REPLACE FUNCTION public.ch_automap()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	v_etage integer;
	v_query text;
	v_result_query integer;
	v_temp integer;
begin
	v_temp := 0;
	for ligne in select * from perso_vue_pos order by pvue_perso_cod asc limit 20000 loop
		v_temp := v_temp + 1;
		if v_temp >= 20000 then
			return 1;
		end if;
		select into v_etage
			etage_cod
			from positions,etage
			where pos_cod = ligne.pvue_pos_cod
			and pos_etage = etage_numero;

		v_query = 'select pvue_perso_cod
			from perso_vue_pos_'||v_etage||'
			where pvue_perso_cod = '||to_char(ligne.pvue_perso_cod,'99999999999999')||'
			and pvue_pos_cod = '||to_char(ligne.pvue_pos_cod,'99999999999999')||';';
		execute v_query;
		get diagnostics v_result_query = ROW_COUNT;
		if v_result_query = 0 then
			v_query = 'insert into perso_vue_pos_'||v_etage||' (pvue_perso_cod,pvue_pos_cod)
				values ('||to_char(ligne.pvue_perso_cod,'99999999999999')||','||to_char(ligne.pvue_pos_cod,'99999999999999')||');';
			execute v_query;
		end if;
		delete from perso_vue_pos where pvue_perso_cod = ligne.pvue_perso_cod
		and pvue_pos_cod = ligne.pvue_pos_cod;
	end loop;
	return 99;
end;$function$

