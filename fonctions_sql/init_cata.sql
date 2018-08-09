CREATE OR REPLACE FUNCTION public.init_cata()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	perso record;
	pos record;
begin
	for perso in select * from perso 
		where perso_actif = 'O' and perso_cod = 50 loop
		insert into perso_vue_pos2 values (perso.perso_cod,'{0}');
		for pos in select * from perso_vue_pos	where pvue_perso_cod = perso.perso_cod loop
			update perso_vue_pos2 set pvue_positions = array_append(pvue_positions::int[],pos.pvue_pos_cod) where pvue_perso_cod = perso.perso_cod;
		end loop;
	end loop;
	return 0;
end;$function$

