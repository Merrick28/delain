CREATE OR REPLACE FUNCTION public.trg_modif_perso()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/******************************************************/
/* trigger modif_perso : à la modif de perso          */
/******************************************************/
declare
	personnage integer;
begin
	NEW.perso_lower_perso_nom = lower(NEW.perso_nom);
	return NEW;
end;$function$

