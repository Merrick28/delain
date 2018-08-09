CREATE OR REPLACE FUNCTION public.init_aff()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
begin
	for ligne in select * from positions
		where pos_etage = -1 loop
	update positions set pos_type_aff = 2 
	where (pos_x+pos_y)%2 = 0;		
			
			
	end loop;
	return 0;
end;
$function$

