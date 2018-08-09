CREATE OR REPLACE FUNCTION public.trg_new_perso()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* trigger cree_perso : à la création de perso        */
/******************************************************/
declare
	v_race integer;
begin
	NEW.perso_capa_repar := (NEW.perso_int + NEW.perso_dex) * 3;
	v_race := NEW.perso_race_cod;
	if v_race = 53 then
		NEW.perso_tangible := 'N';
		NEW.perso_nb_tour_intangible := 9999999;
	elsif v_race = 54 then
		NEW.perso_tangible := 'N';
		NEW.perso_nb_tour_intangible := 9999999;
		NEW.perso_dirige_admin := 'O';
	end if;
		
		
	return NEW;
end;$function$

