CREATE OR REPLACE FUNCTION public.f_num_obj_sol(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	num_gobj alias for $1;
begin
	select into code_retour count(pobj_cod) from objet_position,objets where obj_gobj_cod = num_gobj and pobj_obj_cod = obj_cod;
	return code_retour;
end;$function$

