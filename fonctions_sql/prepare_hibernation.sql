CREATE OR REPLACE FUNCTION public.prepare_hibernation(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***************************************/
/* prepare hibernation                 */
/***************************************/
declare
	d_hiber date;
	v_compte alias for $1;
begin
	select into d_hiber compt_ddeb_hiber
		from compte where compt_cod = v_compte;
	if d_hiber is null then
		return 0;
	else
		return 1;
	end if;
end;$function$

