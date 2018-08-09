CREATE OR REPLACE FUNCTION public.f_trg_new_objet_position()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/**************************************/
/* trigger f_trg_new_objet_position   */
/*   mise au sol d'un objet           */
/**************************************/
declare	
	v_pos_cod integer;
	v_obj_cod integer;
begin
/******************************************************/
/* partie 1 : les valeurs de base                     */
/******************************************************/
	select into
		v_pos_cod,
		v_obj_cod
		pobj_pos_cod,
		pobj_obj_cod
		from objet_position
		where pobj_cod = NEW.pobj_cod;
	/*insert into log_objet
		(llobj_obj_cod,llobj_pos_cod,llobj_type_action)
		values
		(v_obj_cod,v_pos_cod,'Mise au sol');*/
	return NEW;
end;
 $function$

