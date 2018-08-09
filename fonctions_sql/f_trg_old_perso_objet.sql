CREATE OR REPLACE FUNCTION public.f_trg_old_perso_objet()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/***********************************/
/* trigger f_trg_old_perso_objet   */
/*   mise en inventaire d'un objet */
/***********************************/
declare	
	v_perso_cod integer;
	v_obj_cod integer;
	v_identifie text;
begin
/******************************************************/
/* partie 1 : les valeurs de base                     */
/******************************************************/
	select into
		v_perso_cod,
		v_obj_cod,
		v_identifie
		perobj_perso_cod,
		perobj_obj_cod,
		perobj_identifie
		from perso_objets
		where perobj_cod = OLD.perobj_cod;
	/*insert into log_objet
		(llobj_obj_cod,llobj_perso_cod,llobj_type_action)
		values
		(v_obj_cod,v_perso_cod,'Suppression de l inventaire');*/
	if v_identifie = 'O' then
delete from perso_identifie_objet where pio_perso_cod = v_perso_cod
and pio_obj_cod = v_obj_cod;
		insert into perso_identifie_objet (pio_cod,pio_perso_cod,pio_obj_cod,pio_nb_tours)
			values (nextval('seq_pio_cod'),v_perso_cod,v_obj_cod,getparm_n(22));	
	end if; 
	return OLD;
end;
 $function$

