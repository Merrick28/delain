
--
-- Name: f_perso_monture_race(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_perso_monture_race(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_perso_monture_races                      */
/*************************************************/
declare
  v_perso_cod alias for $1;
  v_monture_race_cod integer ;
begin

	v_monture_race_cod:= null ;  -- par défaut
  select m.perso_race_cod into v_monture_race_cod
      from perso as p
      join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
      where p.perso_cod=v_perso_cod and p.perso_type_perso=1 ;

  return v_monture_race_cod ;

end;$$;


ALTER FUNCTION public.f_perso_monture_race(integer) OWNER TO delain;
COMMENT ON FUNCTION f_perso_monture_race(integer) IS 'Retourne le race_cod de la monture du perso (s''il en a une).';



