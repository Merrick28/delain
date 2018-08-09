CREATE OR REPLACE FUNCTION public.f_new_msg()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/*****************************************/
/* trigger new_msg_dest                  */
/* balaye les messages pour les envois   */
/*****************************************/
declare
	v_msg_init integer;
	v_msg_cod integer;
begin
	v_msg_init := NEW.msg_init;
	v_msg_cod := NEW.msg_cod;
	if (NEW.msg_init is null) then
		NEW.msg_init := NEW.msg_cod;
	end if;
	return NEW;
end;$function$

