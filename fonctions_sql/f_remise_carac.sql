--
-- Name: f_remise_carac(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_remise_carac() RETURNS text
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction f_remise_caracs                      */
/*  permet de remettre les caracs à l’origine    */
/*************************************************/
declare
	code_retour text;
	ligne record;
	v_pv integer;
	v_con_actu integer;
	v_diff_pv integer;
	temp_tue text;
begin
	for ligne in
		select distinct corig_perso_cod from carac_orig where (corig_dfin < now() or corig_nb_tours = 0)
	loop
		perform f_remise_caracs(ligne.corig_perso_cod) ; 	  -- mise à jour de caracs pour ce perso!
	end loop;
	return 'OK';
end;$$;


ALTER FUNCTION public.f_remise_carac() OWNER TO delain;

--
-- Name: FUNCTION f_remise_carac(); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_remise_carac() IS 'Annule tous les bonus / malus de caractéristiques arrivés à terme.';

