CREATE OR REPLACE FUNCTION public.init_renom_magie()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	total_sort integer;
	temp_renommee numeric;
	
	
begin
	for ligne in select perso_cod from perso
		where perso_actif = 'O' loop
		select into total_sort count(pnbst_nombre)
			from perso_nb_sorts_total
			where pnbst_perso_cod = ligne.perso_cod;
		temp_renommee := (total_sort*0.4)::numeric;
		update perso
			set perso_renommee_magie = perso_renommee_magie + temp_renommee
			where perso_cod = ligne.perso_cod;
			
			
	end loop;
	return 0;
end;$function$

