CREATE OR REPLACE FUNCTION potions.pot_test(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
  personnage alias for $1;
  v_nom text;
begin
  select into v_nom perso_nom from perso where perso_cod = personnage;
  return v_nom;
end;$function$

