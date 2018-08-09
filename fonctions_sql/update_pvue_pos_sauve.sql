CREATE OR REPLACE FUNCTION public.update_pvue_pos_sauve(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* update_pvue_pos                                   */
/*****************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_pos alias for $2;
	v_dist integer;
	v_x integer;
	v_y integer;
	v_etage integer;
	l_position record;
	v_pvue integer;
begin
	code_retour := 0;
	v_dist := distance_vue(personnage);
	select into v_x,v_y,v_etage pos_x,pos_y,pos_etage
		from positions
		where pos_cod = v_pos;
	for l_position in select pos_cod from positions
			where pos_x between (v_x - v_dist) and (v_x + v_dist)
			and pos_y between (v_y - v_dist) and (v_y + v_dist)
			and pos_etage = v_etage loop
--
	select into v_pvue pvue_perso_cod
		from perso_vue_pos
		where pvue_perso_cod = personnage
		and pvue_pos_cod = l_position.pos_cod;
	if not found then
		insert into perso_vue_pos (pvue_perso_cod,pvue_pos_cod)
			values (personnage,l_position.pos_cod);
	end if;
	end loop;
	return code_retour;
end;
$function$

