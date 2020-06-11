--
-- Name: f_remise_caracs(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_remise_caracs(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************/
/* fonction f_remise_caracs                      */
/*  permet de remettre les caracs à l’origine    */
/*  Cette version fonctionne pour un perso précis*/
/* $1 = perso_cod                                */
/*************************************************/
declare
	v_perso alias for $1;
	code_retour text;
	ligne record;
	v_pv integer;
	temp_tue text;
	v_modificateur integer;
  v_diff integer;
	v_limit_max integer;
	v_nouvelle_valeur integer;
begin

	for ligne in
		select distinct corig_perso_cod, corig_type_carac, corig_carac_valeur_orig,
            case corig_type_carac
              when 'FOR' then perso_for
              when 'DEX' then perso_dex
              when 'INT' then perso_int
              when 'CON' then perso_con
          else NULL end as carac_actuelle
		from carac_orig join perso on perso_cod=corig_perso_cod
		where corig_perso_cod = v_perso and (corig_dfin <= now() or corig_nb_tours = 0)
	loop
	  if ligne.carac_actuelle is not null then

	    perform f_modif_carac_perso(v_perso, ligne.corig_type_carac)  ;

      -- supprimer les bonus maintenant que les caracs ont été rétablies
      delete from carac_orig where corig_type_carac=ligne.corig_type_carac and corig_perso_cod=ligne.corig_perso_cod and (corig_dfin <= now() or corig_nb_tours = 0) ;

    end if;


	end loop;
	return 'OK';
end;$_$;


ALTER FUNCTION public.f_remise_caracs(integer) OWNER TO delain;

--
-- Name: FUNCTION f_remise_caracs(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_remise_caracs(integer) IS 'Remets les caractéristiques d’origine suite aux bonus/malus qui se terminent, pour le perso $1.';

