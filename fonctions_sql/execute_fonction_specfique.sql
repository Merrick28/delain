--
-- Name: execute_fonction_specifique(integer, integer, character varying(3), json); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION execute_fonction_specifique(integer, integer, integer, json) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction execute_fonction_specifique                                */
/*   Exécute les fonctions spécifiques liées à un monstre    */
/*    et/ou un personnage                                    */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de la source              */
/*   $2 = cible_cod : si nécessaire, le numéro de la cible   */
/*   $3 = fonc_cod : la fonction specifique                  */
/*   $4 = params : divers paramètre (en fonction des besoins)*/
/*************************************************************/
declare
	v_perso_cod alias for $1;                   -- Le code de la source
	v_cible_cod alias for $2;                   -- Le numéro de la cible
	v_fonc_cod alias for $3;                    -- LA fonction qui c'est déclenchée!
	v_param alias for $4;                       -- Les données (si besoin) à injecter pour l'effet de l'EA

	code_retour text;                           -- Le retour de la fonction
	retour_fonction text;                       -- Le résultat de l’exécution d’une fonction
	ligne_fonction record;                      -- Les données de la fonction
	code_fonction text;                         -- Le code SQL lançant la fonction
  v_pos integer;                              -- Le code de la position où se déroule l'effet
  v_deda timestamp without time zone;         -- le temps entre 2 actions pour la fonction en cours.
  v_ddda timestamp without time zone;         -- date de dernière action
  v_encours integer;                          -- nombre d'action déjà en cours pour cette fonction pour ce perso.

begin

  -- code de retour par defaut
	code_retour := '';

  -- ---------------------------------------------------------------------------
  -- récupérer les infos du dernier declenchement
  select pfonc_ddda, pfonc_encours into v_ddda,v_encours from fonction_specifique_perso where pfonc_fonc_cod=v_fonc_cod and pfonc_perso_cod=v_perso_cod ;
  if not found then
      -- premier déclenchement de cette fonction pour ce perso, on créé une entrée pour les futurs déclenchements
      insert into fonction_specifique_perso(pfonc_fonc_cod, pfonc_perso_cod, pfonc_ddda, pfonc_encours) VALUES (v_fonc_cod, v_perso_cod, now(), 1);
  else
    -- avant toute chose on vérifie le paramètre DEDA (Délai Entre 2 Actions) s'il est définit
      -- ainsi que les protections de recursivité (une action qui déclenche cette même action directement ou indirectement)
      if v_encours > 0 then
          return code_retour;
      end if;

      select NOW() - (COALESCE(NULLIF(fonc_trigger_param->>'fonc_trig_deda'::text, ''),'0')||' minutes')::interval into v_deda from fonction_specifique where fonc_cod=v_fonc_cod ;
      if v_deda < v_ddda then
            return code_retour;
      end if;

      -- c'est tout bon -- on ajuste les compteurs maintenant!
      update fonction_specifique_perso set pfonc_ddda=now(), pfonc_encours=pfonc_encours+1 where pfonc_fonc_cod=v_fonc_cod and pfonc_perso_cod=v_perso_cod ;

  end if;


  -- ---------------------------------------------------------------------------
  -- paramètre necessaire pour fonction inovation, si pas de cible on invoque sur le perso porteur de l'EA
  select into v_pos ppos_pos_cod from perso_position where ppos_perso_cod = COALESCE(v_cible_cod, v_perso_cod);


  -- ---------------------------------------------------------------------------
  -- déclenchement par lui même !
  select * into ligne_fonction from fonction_specifique where fonc_cod=v_fonc_cod ;
  if found then

    code_fonction := ligne_fonction.fonc_nom;
    retour_fonction := '';


    if code_fonction = 'deb_tour_generique' then
      select into retour_fonction deb_tour_generique(v_perso_cod, ligne_fonction.fonc_effet, ligne_fonction.fonc_force, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_duree, ligne_fonction.fonc_message,  v_cible_cod, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elseif code_fonction = 'ea_ajoute_bm' then
      select into retour_fonction ea_ajoute_bm(v_perso_cod, v_cible_cod, ligne_fonction.fonc_force, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'deb_tour_degats' then
      select into retour_fonction deb_tour_degats(v_perso_cod, ligne_fonction.fonc_force, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, v_cible_cod, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'titre' then
      select into retour_fonction effet_auto_ajout_titre(v_cible_cod, ligne_fonction.fonc_message, v_perso_cod);

    elsif code_fonction = 'necromancie' then
      select into retour_fonction necromancie(v_perso_cod, v_cible_cod);

    elsif code_fonction = 'deb_tour_rouille' then
      select into retour_fonction deb_tour_rouille(v_perso_cod, ligne_fonction.fonc_proba::integer, ligne_fonction.fonc_force::integer, ligne_fonction.fonc_effet::integer);

    elsif code_fonction = 'deb_tour_invocation' then
      if v_pos is not null then
        select into retour_fonction deb_tour_invocation(v_perso_cod, ligne_fonction.fonc_effet::integer, ligne_fonction.fonc_proba::integer, ligne_fonction.fonc_message, v_pos);
      end if;

    elsif code_fonction = 'poison_araignee' then
      select into retour_fonction poison_araignee(v_perso_cod);

    elsif code_fonction = 'deb_tour_degats_case' then
      select into retour_fonction deb_tour_degats_case(v_perso_cod, ligne_fonction.fonc_force::integer, ligne_fonction.fonc_proba::integer, ligne_fonction.fonc_message);

    elsif code_fonction = 'deb_tour_esprit_damne' then
      select into retour_fonction deb_tour_esprit_damne(v_perso_cod);

    elsif code_fonction = 'trans_crap' then
      select into retour_fonction trans_crap(v_perso_cod);

    elsif code_fonction = 'deb_tour_necromancie' then
      select into retour_fonction deb_tour_necromancie(v_perso_cod, coalesce(nullif(ligne_fonction.fonc_force,''),'1')::numeric, ligne_fonction.fonc_proba::integer);

    elsif code_fonction = 'deb_tour_haloween' then
      select into retour_fonction deb_tour_haloween(v_perso_cod, ligne_fonction.fonc_nombre_cible::integer, ligne_fonction.fonc_proba::integer);

    elsif code_fonction = 'valide_quete_avatar' then
      select into retour_fonction valide_quete_avatar(v_perso_cod, v_cible_cod);

    elsif code_fonction = 'invoque_rejetons' then
      select into retour_fonction invoque_rejetons(v_perso_cod, ligne_fonction.fonc_nombre_cible::integer, ligne_fonction.fonc_effet::integer);

    elsif code_fonction = 'resurrection_monstre' then
      select into retour_fonction resurrection_monstre(v_perso_cod, ligne_fonction.fonc_nombre_cible::integer, ligne_fonction.fonc_effet::integer, ligne_fonction.fonc_proba::integer);

    elsif code_fonction = 'ea_supprime_bm' then
      select into retour_fonction ea_supprime_bm(v_perso_cod, v_cible_cod, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'ea_lance_sort' then
      select into retour_fonction ea_lance_sort(v_perso_cod, v_cible_cod, ligne_fonction.fonc_effet, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'ea_projection' then
      select into retour_fonction ea_projection(v_perso_cod, v_cible_cod, ligne_fonction.fonc_force, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json );

    elsif code_fonction = 'ea_glissade' then
      select into retour_fonction ea_glissade(v_perso_cod, v_cible_cod, ligne_fonction.fonc_force, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json );

    elsif code_fonction = 'ea_saut_sur_cible' then
      select into retour_fonction ea_saut_sur_cible(v_perso_cod, v_cible_cod, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json );

    elsif code_fonction = 'ea_drop_objet' then
      select into retour_fonction ea_drop_objet(v_perso_cod, v_cible_cod, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json );

    elsif code_fonction = 'ea_invocation' then
      select into retour_fonction ea_invocation(v_perso_cod, v_cible_cod, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'ea_metamorphe' then
      select into retour_fonction ea_metamorphe(v_perso_cod, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb || ('{"ea_fonc_cod":'||v_fonc_cod::text||'}')::jsonb)::json);

    elsif code_fonction = 'ea_implantation_ea' then
      select into retour_fonction ea_implantation_ea(v_perso_cod, v_cible_cod, ligne_fonction.fonc_effet, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'ea_meca' then
      select into retour_fonction ea_meca(v_perso_cod, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'ea_teleportation' then
      select into retour_fonction ea_teleportation(v_perso_cod, v_cible_cod, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json );

    elsif code_fonction = 'ea_modification_ea' then
      select into retour_fonction ea_modification_ea(v_perso_cod, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json);

    elsif code_fonction = 'ea_message' then
      select into retour_fonction ea_message(v_perso_cod, v_cible_cod, ligne_fonction.fonc_portee, ligne_fonction.fonc_type_cible, ligne_fonction.fonc_nombre_cible, ligne_fonction.fonc_proba/100, ligne_fonction.fonc_message, (coalesce(ligne_fonction.fonc_trigger_param, '{}')::jsonb || coalesce(v_param, '{}')::jsonb)::json );

    end if;

    if coalesce(retour_fonction, '') != '' then
      -- code_retour := code_retour || code_fonction || ' : ' || coalesce(retour_fonction, '') || '<br />';
      code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
    end if;

  end if;

  -- ---------------------------------------------------------------------------
  -- en fin de déclenchement libération du jeton d'action en cours (et d'eventuelles données injectées)
  update fonction_specifique_perso set pfonc_encours=pfonc_encours-1 where pfonc_fonc_cod=v_fonc_cod and pfonc_perso_cod=v_perso_cod ;

  -- ---------------------------------------------------------------------------
  -- texte de retour !
	return code_retour;

end;$_$;


ALTER FUNCTION public.execute_fonction_specifique(integer, integer, integer, json) OWNER TO delain;

--
-- Name: FUNCTION execute_fonction_specifique(integer, integer, integer, json); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION execute_fonction_specifique(integer, integer, integer, json) IS 'Exécute une fonction spécifique';
