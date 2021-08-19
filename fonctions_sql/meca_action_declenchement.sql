
--
-- Name: meca_action_declenchement(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION meca_action_declenchement() RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction meca_action_declenchement   (caller)        */
/*************************************************/
declare
    ligne record ;
    v_date_action timestamp ;
begin
      -- date de traitement !
      v_date_action:= NOW();

      -- boucle sur les actions à réaliser
      for ligne in ( SELECT *  FROM meca_action where ameca_date_action <= v_date_action order by ameca_date_action  )
      loop
          perform meca_declenchement(ligne.ameca_meca_cod, ligne.ameca_sens_action, ligne.ameca_pos_cod, null );
      end loop;

      -- supprimer les entrées de la table
      DELETE FROM meca_action where ameca_date_action <= v_date_action ;

  return 0 ;

end;$$;

ALTER FUNCTION public.meca_action_declenchement() OWNER TO delain;


