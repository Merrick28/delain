CREATE OR REPLACE FUNCTION public.efface_guilde_vide()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	v_compt integer;
begin
	v_compt := 0;
	for ligne in
		select guilde_cod from guilde
		where not exists
		(select 1 from guilde_perso where pguilde_guilde_cod = guilde_cod) loop
		-- rangs
		delete from guilde_rang where rguilde_guilde_cod = ligne.guilde_cod;
		-- messages
		update messages set msg_guilde_cod = null where msg_guilde_cod = ligne.guilde_cod;
		-- guilde
		delete from guilde where guilde_cod = ligne.guilde_cod;
		v_compt := v_compt + 1;
	end loop;
	return v_compt;
end;$function$

