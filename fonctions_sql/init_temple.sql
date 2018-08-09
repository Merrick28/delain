CREATE OR REPLACE FUNCTION public.init_temple()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	v_ptemple_cod integer;
	compt integer;
	
	
begin
	
	compt := 0;
	for ligne in select ptemple_perso_cod,count(*) from perso_temple
		group by ptemple_perso_cod
		having count(*) > 1 loop
	compt := compt + 1;
	select into v_ptemple_cod ptemple_cod from perso_temple
		where ptemple_perso_cod = ligne.ptemple_perso_cod
		order by ptemple_cod asc
		limit 1;
	delete from perso_temple
		where ptemple_cod = v_ptemple_cod;	
		
	end loop;
	return compt;
end;$function$

