--
-- Name: bonus_degats_melee(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bonus_degats_melee(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/**********************************************************/
/* fonction bonus_degats_melee : donne le bonus en degats */
/*   pour le corps à corps pour un perso                  */
/* on passe en paramètres :                               */
/*   $1 = perso_cod                                       */
/* on a en retour un entier                               */
/**********************************************************/
declare
  code_retour integer;
  personnage alias for $1;
  force integer;
begin
  select into force perso_for from perso
  where perso_cod = personnage;
  code_retour := floor((force - 9) / 3 ::numeric);
  if code_retour < -1 then
    code_retour := -1;
  end if;
  return code_retour;
end;
$_$;


ALTER FUNCTION public.bonus_degats_melee(integer) OWNER TO delain;