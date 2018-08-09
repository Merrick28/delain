CREATE OR REPLACE FUNCTION public.f_trg_mod_perso_objet()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/***********************************/
/* trigger f_trg_mod_perso_objet   */
/*   transaction d'un objet        */
/***********************************/
declare	
	v_perso_cod integer;
	v_obj_cod integer;
	v_dest integer;
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
		where perobj_cod = OLD.perobj_cod;
	select into v_dest
		perobj_perso_cod
		from perso_objets
		where perobj_cod = NEW.perobj_cod;
	if v_dest != v_perso_cod then
		insert into log_objet
			(llobj_obj_cod,llobj_perso_cod,llobj_type_action,lobj_dest_tran)
			values
			(v_obj_cod,v_perso_cod,'Transaction',v_dest);
	end if;
	return NEW;
end;
 $function$

