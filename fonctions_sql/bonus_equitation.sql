--
-- Name: bonus_equitation(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bonus_equitation(integer) RETURNS numeric
LANGUAGE plpgsql
AS $_$/***************************************/
/* bonus_equitation                       */
/***************************************/
declare
  personnage alias for $1;
  v_bonus numeric;            -- valeur du bonus calculé
  v_monture integer;          -- perso_cod de la monture
  v_bonus_monture numeric;    -- competence de la monture
begin

  -- valeur de bonus (du perso)
  v_bonus := valeur_bonus(personnage, 'EQI');

  -- s'il a une monture on lui ajoute la compétence de la monture ! (ainsi une monture peu être plus ou moins docile)
  v_monture := f_perso_monture(personnage);
  if v_monture is not null then
      v_bonus := v_bonus + valeur_bonus(v_monture, 'EQI');    -- bonus/malus de la monture
      select pcomp_modificateur into v_bonus_monture from perso_competences where pcomp_perso_cod = v_monture and pcomp_pcomp_cod=104 ;
      if found then
        v_bonus:= v_bonus + v_bonus_monture ;   -- compétence de la monture
      end if;
  end if;

  return v_bonus;

end;$_$;


ALTER FUNCTION public.bonus_equitation(integer) OWNER TO delain;