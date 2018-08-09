CREATE OR REPLACE FUNCTION public.dissipation_magique()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************************************/
/* dissipation magique                                                 */
/***********************************************************************/
declare
	code_retour text;
	ligne record;
	v_magie integer;
	temp integer;
begin
	for ligne in select * from positions where pos_magie > 0 loop
		v_magie := ligne.pos_magie;
		if v_magie >= 100 then
			temp := 0;
		else
			update positions
				set pos_magie = pos_magie - 1
				where pos_cod = ligne.pos_cod;
		end if;
	code_retour := 'OK';
		
	end loop;
return code_retour;	
end;$function$

