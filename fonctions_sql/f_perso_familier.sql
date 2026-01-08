
--
-- Name: f_perso_familier(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_perso_familier(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_perso_familiers                      */
/*************************************************/
declare
  v_perso_cod alias for $1;
  v_familier integer ;
begin

	v_familier:= null ;  -- par défaut

    select pfam_familier_cod into v_familier
        from perso_familier
        join perso on perso_cod=pfam_familier_cod
        where pfam_perso_cod=v_perso_cod and perso_actif='O' order by perso_dcreat desc limit 1 ;

  return v_familier ;

end;$$;


ALTER FUNCTION public.f_perso_familier(integer) OWNER TO delain;
COMMENT ON FUNCTION f_perso_familier(integer) IS 'Retourne le perso_cod du familier.';



