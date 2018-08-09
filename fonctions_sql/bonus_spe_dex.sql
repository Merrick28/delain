CREATE OR REPLACE FUNCTION public.bonus_spe_dex(integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/***************************************/
/* bonus_spe_dex                       */
/***************************************/
declare
  code_retour numeric;
  personnage alias for $1;
  v_dex integer;
  v_bonus numeric;

begin

  select into v_dex perso_dex
  from perso
  where perso_cod = personnage;
  v_bonus := (v_dex - 11) * 0.01;
  if v_bonus < 0 then
    return 0;
  else
    return v_bonus;
  end if;
end;$function$

