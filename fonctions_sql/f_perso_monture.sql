
--
-- Name: f_perso_monture(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_perso_monture(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_perso_montures                      */
/*************************************************/
declare
  v_perso_cod alias for $1;
  v_monture integer ;
begin

	v_monture:= null ;  -- par défaut
  select m.perso_cod into v_monture
      from perso as p
      join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
      where p.perso_cod=v_perso_cod and p.perso_type_perso=1 ;

  return v_monture ;

end;$$;


ALTER FUNCTION public.f_perso_monture(integer) OWNER TO delain;
COMMENT ON FUNCTION f_perso_monture(integer) IS 'Retourne le perso_cod de la monture.';



