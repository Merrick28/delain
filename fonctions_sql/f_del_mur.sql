CREATE OR REPLACE FUNCTION public.f_del_mur()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/**************************/
/* trigger mod_lpos      */
/**************************/
declare	
	v_mur_pos_cod integer;
begin
	insert into pos_modifie (pmod_pos_cod) values (OLD.mur_pos_cod);
	return OLD;
end;
$function$

