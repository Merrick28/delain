CREATE OR REPLACE FUNCTION public.f_trg_old_objet()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/**************************************/
/* trigger f_trg_old_objet            */
/*   destruction d'un objet           */
/**************************************/
declare	
	v_pos_cod integer;
	v_obj_cod integer;
begin
/******************************************************/
/* partie 1 : les valeurs de base                     */
/******************************************************/
	/*insert into log_objet
		(llobj_obj_cod,llobj_type_action)
		values
		(OLD.obj_cod,'Destruction objet');*/
	return OLD;
end;
 $function$

