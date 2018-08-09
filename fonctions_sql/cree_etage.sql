CREATE OR REPLACE FUNCTION public.cree_etage(integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	v_etage alias for $1;
	x_min alias for $2;
	x_max alias for $3;
	y_min alias for $4;
	y_max alias for $5;
	x_initial integer;
	y_initial integer;
	test integer;
	
begin
	select into test distinct(pos_etage) from positions
		where pos_etage = v_etage;
	if found then
		code_retour := 'Etage déjà existant !';
		return code_retour;
	end if;
	if x_min >= x_max then
		code_retour := 'Problème sur X !';
		return code_retour;
	end if;
	if y_min >= y_max then
		code_retour := 'Problème sur Y !';
		return code_retour;
	end if;
	x_initial := x_min;
	while x_initial <= x_max loop
		y_initial := y_min;
		while y_initial <= y_max loop
			insert into positions (pos_x,pos_y,pos_etage) values (x_initial,y_initial,v_etage);
			y_initial := y_initial + 1;
		end loop;
	x_initial := x_initial + 1;
	end loop;
	return 'Etage créé !';
end;$function$

