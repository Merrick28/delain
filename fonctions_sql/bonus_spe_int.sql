CREATE OR REPLACE FUNCTION public.bonus_spe_int(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***************************************/
/* bonus_spe_int                       */
/***************************************/
declare
  code_retour numeric;
  personnage alias for $1;
  v_int integer;
  v_bonus integer;
begin
  select into v_int perso_int
  from perso
  where perso_cod = personnage;
  v_bonus := v_int - 15;
  if v_bonus < 0 then
    v_bonus := 0;
  elseif v_bonus > 5 then
    v_bonus := 5;
  else return v_bonus;
  end if;
  return v_bonus / 100;
end;$function$

