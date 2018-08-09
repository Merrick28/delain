CREATE OR REPLACE FUNCTION public.f_trg_new_concenctration()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/********************************************/
/* nouvelle concentration                   */
/*  ajoute l'evt correspondant              */
/********************************************/
declare
	v_perso integer;
begin
	v_perso := NEW.concentration_perso_cod;
	insert into ligne_evt
		(levt_tevt_cod,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values
		(64,v_perso,'[perso_cod1] s''est concentré','O','N');
	return NEW;
end;$function$

