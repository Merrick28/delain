
--
-- Name: f_perso_cavalier(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_perso_cavalier(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_perso_cavaliers                      */
/*************************************************/
declare
  v_perso_cod alias for $1;
  v_cavalier integer ;
begin

	v_cavalier:= null ;  -- par défaut
  select p.perso_cod into v_cavalier
      from perso as m
      join perso as p on p.perso_monture=m.perso_cod and p.perso_actif = 'O' and p.perso_type_perso=1
      where m.perso_cod=v_perso_cod and m.perso_type_perso=2 ;

  return v_cavalier ;

end;$$;


ALTER FUNCTION public.f_perso_cavalier(integer) OWNER TO delain;
COMMENT ON FUNCTION f_perso_cavalier(integer) IS 'Retourne le perso_cod du cavalier.';



