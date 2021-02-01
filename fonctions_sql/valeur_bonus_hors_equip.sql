--
-- Name: valeur_bonus_hors_equip(integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION valeur_bonus_hors_equip(integer, text) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$-- Retourne la valeur numérique du bonus choisi pour le perso.
-- $1 = Le code du perso
-- $2 = Le type de bonus
-- Retour: La valeur numérique, somme des bonus positif et négatif

declare
    v_perso alias for $1;
    v_type alias for $2;
    v_retour numeric;
begin

  -- bonus du type caracs
  if v_type in ('DEX', 'INT', 'FOR', 'CON')  THEN

    select into v_retour
          (case corig_type_carac
              when 'FOR' then perso_for
              when 'DEX' then perso_dex
              when 'INT' then perso_int
              when 'CON' then perso_con
              else NULL end ) - corig_carac_valeur_orig
    from carac_orig join perso on perso_cod=corig_perso_cod
    where corig_perso_cod = v_perso and corig_type_carac = v_type and corig_mode != 'E' LIMIT 1;
    if not found then
      v_retour := 0;
    end if;

  -- autres bonus que les caracs!
  else

    select into v_retour coalesce(sum(bonus_valeur), 0) from bonus where bonus_perso_cod = v_perso and bonus_tbonus_libc = v_type and bonus_mode != 'E';
    if not found then
      v_retour := 0;
    end if;

  end if;

  return v_retour;
end;$_$;


ALTER FUNCTION public.valeur_bonus_hors_equip(integer, text) OWNER TO delain;