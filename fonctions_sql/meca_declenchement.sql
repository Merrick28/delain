
--
-- Name: meca_declenchement(integer,integer,integer,integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION meca_declenchement(integer,integer,integer,integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction meca_declenchement                      */
/*************************************************/
declare
  v_meca_cod alias for $1;
  v_sens alias for $2;
  v_meca_pos_cod alias for $3;
  v_perso_pos_cod alias for $4;

begin


  return 0;

end;$$;

ALTER FUNCTION public.meca_declenchement(integer,integer,integer,integer) OWNER TO delain;



