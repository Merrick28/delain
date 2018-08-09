CREATE OR REPLACE FUNCTION public.pos_par_etage(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	v_etage alias for $1;
	ligne record;
begin
	code_retour := '0';
	for ligne in select pos_cod from positions where pos_etage = v_etage loop
		code_retour := code_retour||'|'||trim(to_char(ligne.pos_cod,'99999999'));
	end loop;
	return code_retour;
end;$function$

