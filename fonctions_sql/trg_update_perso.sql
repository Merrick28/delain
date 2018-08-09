CREATE OR REPLACE FUNCTION public.trg_update_perso()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/******************************/
/* Trigger pour mettre à jour */
/* la table de persos après   */
/* modifications              */
/******************************/
declare
  blessures_old integer;
  blessures_new integer;
  temp numeric;
begin
  -- En cas de soins, régénération, etc...
  if (NEW.perso_pv > OLD.perso_pv and OLD.perso_pv > 0) then
    temp := OLD.perso_pv / OLD.perso_pv_max;
    blessures_old := floor(4 - 4*temp) + cast(temp < 0.15 as integer); -- On convertit le ratio en seuil de blessure
    temp := NEW.perso_pv / NEW.perso_pv_max;
    blessures_new := floor(4 - 4*temp) + cast(temp < 0.15 as integer);
  -- On regarde si on a gagné des seuils.
    if (blessures_old > blessures_new) then
      NEW.perso_compt_pvp := max(0 , OLD.perso_compt_pvp + blessures_new - blessures_old);
    end if;
  end if;

  return NEW;
end;$function$

