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
		where corig_perso_cod = v_perso and (corig_dfin < now() or corig_nb_tours = 0)
	loop
	  if ligne.carac_actuelle is not null then

	    perform f_modif_carac_perso(v_perso, ligne.corig_type_carac)  ;

/*
      -- on regarde quels sont les bonus restant maintenant que certains bonus ont expirer
      select into v_modificateur coalesce(sum(corig_valeur),0) from carac_orig  where corig_perso_cod = v_perso and corig_type_carac = ligne.corig_type_carac and (corig_dfin >= now() or corig_nb_tours > 0) ;

      v_nouvelle_valeur := f_modif_carac_limit(ligne.corig_type_carac, ligne.corig_carac_valeur_orig, ligne.corig_carac_valeur_orig + v_modificateur);

      if v_nouvelle_valeur <> ligne.carac_actuelle  then
        v_diff := v_nouvelle_valeur - ligne.carac_actuelle ;

        if ligne.corig_type_carac = 'FOR' then
          update perso set perso_for = perso_for + v_diff, perso_enc_max = perso_enc_max + (v_diff * 3) where perso_cod = v_perso;

        elsif ligne.corig_type_carac = 'DEX' then
          update perso set perso_dex = perso_dex + v_diff, perso_capa_repar = perso_capa_repar + (v_diff * 3) where perso_cod = v_perso;

        elsif ligne.corig_type_carac = 'INT' then
          update perso set perso_int = perso_int + v_diff, perso_capa_repar = perso_capa_repar + (v_diff * 3) where perso_cod = v_perso;

        elsif ligne.corig_type_carac = 'CON' then
          update perso set perso_con = perso_con + v_diff, perso_pv_max = perso_pv_max + (v_diff * 3), perso_pv = perso_pv + (v_diff * 3) where perso_cod = v_perso;

        end if;
      end if;

      -- supprimer les bonus maintenant que les caracs ont été rétablies
      delete from carac_orig where corig_type_carac=ligne.corig_type_carac and corig_perso_cod=ligne.corig_perso_cod and (corig_dfin < now() or corig_nb_tours = 0) ;

      select into v_pv perso_pv  from perso  where perso_cod = ligne.corig_perso_cod;
      if v_pv <= 0 then
        temp_tue := 'Un bonus de constitution a pris fin. La perte des PV temporaires vous a été fatale.';
        perform insere_evenement(ligne.corig_perso_cod, ligne.corig_perso_cod, 10, temp_tue, 'N', NULL);
        temp_tue := tue_perso_final(ligne.corig_perso_cod, ligne.corig_perso_cod);
      end if;
*/
      -- supprimer les bonus maintenant que les caracs ont été rétablies
      delete from carac_orig where corig_type_carac=ligne.corig_type_carac and corig_perso_cod=ligne.corig_perso_cod and (corig_dfin < now() or corig_nb_tours = 0) ;

    end if;


	end loop;
	return 'OK';
end;$_$;


ALTER FUNCTION public.f_remise_caracs(integer) OWNER TO delain;

--
-- Name: FUNCTION f_remise_caracs(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_remise_caracs(integer) IS 'Remets les caractéristiques d’origine suite aux bonus/malus qui se terminent, pour le perso $1.';

