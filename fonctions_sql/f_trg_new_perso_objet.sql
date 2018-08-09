CREATE OR REPLACE FUNCTION public.f_trg_new_perso_objet()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/***********************************/
/* trigger f_trg_new_perso_objet   */
/*   mise en inventaire d'un objet */
/***********************************/
declare	
	v_perso_cod integer;
	v_obj_cod integer;
begin
/******************************************************/
/* partie 1 : les valeurs de base                     */
/******************************************************/
	select into
		v_perso_cod,
		v_obj_cod
		perobj_perso_cod,
		perobj_obj_cod
		from perso_objets
		where perobj_cod = NEW.perobj_cod;
	insert into log_objet
		(llobj_obj_cod,llobj_perso_cod,llobj_type_action)
		values
		(v_obj_cod,v_perso_cod,'Mise en inventaire');
	return NEW;
end;
 $function$

