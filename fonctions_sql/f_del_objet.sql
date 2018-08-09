CREATE OR REPLACE FUNCTION public.f_del_objet(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	num_objet alias for $1;
begin
	delete from perso_objets where perobj_obj_cod = num_objet;
	delete from perso_identifie_objet where pio_obj_cod = num_objet;
	delete from objet_position where pobj_obj_cod = num_objet;
	delete from perso_glyphes where pglyphe_obj_cod = num_objet;
	delete from transaction where tran_obj_cod = num_objet;
	delete from objets where obj_cod = num_objet;
	return 0;
end;$function$

