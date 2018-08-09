CREATE OR REPLACE FUNCTION public.trg_update_compte()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$/******************************/
/* Trigger pour mettre à jour */
/* la table de compatibilité  */
/* Compte / Ip                */
/******************************/
declare
begin
  if (NEW.compt_der_connex != OLD.compt_der_connex) then -- Changing last connection date
    insert into compte_ip (icompt_compt_cod , icompt_compt_ip)
    values (NEW.compt_cod , NEW.compt_ip);
  end if;
  return NEW;
end;$function$

