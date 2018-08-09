CREATE OR REPLACE FUNCTION public.f_num_obj_perso(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	num_gobj alias for $1;
begin
	select into code_retour
		count(perobj_cod)
			from perso_objets,objets,perso
			where obj_gobj_cod = num_gobj
			and perobj_obj_cod = obj_cod
			and perobj_perso_cod = perso_cod
			and perso_actif = 'O';
	return code_retour;
end;$function$

