CREATE OR REPLACE FUNCTION public.f_cree_mur()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/**************************/
/* trigger cree_murs      */
/**************************/
declare	
	v_mur_pos_cod integer;
begin
	insert into pos_modifie (pmod_pos_cod) values (NEW.mur_pos_cod);
	return NEW;
end;$function$

