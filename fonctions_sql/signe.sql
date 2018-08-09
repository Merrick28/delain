CREATE OR REPLACE FUNCTION public.signe(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	nombre alias for $1;
begin
	if nombre > 0 then
		return 1;
	elsif nombre < 0 then
		return -1;
	elsif nombre = 0 then
		return 0;
	end if;
end;$function$

