CREATE OR REPLACE FUNCTION public.f_trg_old_concentration()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/********************************************/
/* efface concentration                   */
/*  ajoute l'evt correspondant              */
/********************************************/
declare
	v_perso integer;
begin
	v_perso := OLD.concentration_perso_cod;
	insert into ligne_evt
		(levt_tevt_cod,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values
		(64,v_perso,'[perso_cod1] a perdu sa concentration','O','N');
	return OLD;
end;$function$

