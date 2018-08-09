CREATE OR REPLACE FUNCTION public.temp_init_esc()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	l_lieu record;
	pos_lieu integer;
	v_x integer;
	v_y integer;
	v_etage integer;
	
begin
	for l_lieu in select * from lieu
		where lieu_url = 'escalier_m.php' loop
		select into v_x,v_y,v_etage
			pos_x,pos_y,pos_etage
			from lieu_position,positions
			where lpos_lieu_cod = l_lieu.lieu_cod
			and lpos_pos_cod = pos_cod;
		select into pos_lieu
			pos_cod from positions
			where pos_x = v_x
			and pos_y = v_y
			and pos_etage = (v_etage + 1);
		update lieu set lieu_dest = pos_lieu
			where lieu_cod = l_lieu.lieu_cod;	
		
		
	end loop;
	return 0;
end;
$function$

