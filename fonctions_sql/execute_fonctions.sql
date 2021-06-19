--
-- Name: execute_fonctions(integer, integer, character, json); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION execute_fonctions(integer, integer, character, json) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction execute_fonctions                            */
/*   comme  execute_fonctions mais avec injection de params. */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de la source              */
/*   $2 = cible_cod : si nécessaire, le numéro de la cible   */
/*   $3 = événement : D pour début de tour, M pour mort,     */
/*                    T pour Tueur, A pour Attaque, etc...   */
/*   $4 = params : divers paramètre (en fonction des besoins)*/
/* on a en sortie les sorties concaténées des fonctions.     */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code de la source
	v_cible_cod alias for $2;    -- Le numéro de la cible
	v_evenement alias for $3;  -- L’événement déclencheur
	v_param alias for $4;      -- Les données à injecter pour l'éffet de l'EA

	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	row record;                -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique
  v_gmon_nom text;           -- Le nom du monstre générique
  v_gobj_cod integer;         -- code generique d'un objet
  v_obj_cod integer;          -- code d'un objet
  v_sante_avant integer;     -- % de blessure au passage précédent
  v_etat_sante integer;      -- % de blessure actuel
  v_sante_min integer;       -- fourchette basse
  v_sante_max integer;       -- fourchette haute
  v_pfonc_param json;        -- Paramètre mémorisé pour cet EA
	v_do_it bool;              -- Executer la fonction

	-- variable specifique au BMC
	v_perso_nom text;          -- Nom du perso avan modification
	v_nom text;                -- Racine du nom du monstre (ie sans le N°)
	v_raz text;                -- Raz du compteur à réaliser?.

	-- variable specifique au MAL / MAC
	v_sort_aggressif text;     -- sort de agressif
	v_sort_soutien text;       -- sort de agressif

begin

  v_raz := 'N';                     -- pas de RAZ du compteur par défaut (pour type EA = BMC)

  -- Eventuellement les fonctions du monstre générique
	select into v_gmon_cod, v_gmon_nom, v_perso_nom perso_gmon_cod, gmon_nom, perso_nom from perso inner join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = v_perso_cod;
	if not found then
	    -- cas des aventuriers
      v_gmon_cod:= null ;
      v_gmon_nom:= null ;
	end if;

  -- code de retour
	code_retour := '';

  -- code_retour := code_retour ||  '<br> perso_cod=' || v_perso_cod::text ||  ' événement=' || v_evenement ;   -- DEBUG EA
->>'fonc_trig_pos_cods' like '% '||v_param->>'ancien_pos_cod'::text ||',%'
  -- boucle sur toutes les fonctions specifiques sur l'évenement
	for row in (
		select * from fonction_specifique
		where (fonc_gmon_cod = coalesce(v_gmon_cod, -1) OR (fonc_perso_cod = v_perso_cod) OR (fonc_gmon_cod is null and fonc_perso_cod is null and (v_evenement='BMC' OR v_evenement='DEP')))
			and (fonc_type = v_evenement OR fonc_type = 'CES' OR (fonc_type = 'POS' AND (fonc_trigger_param->>'fonc_trig_pos_cods' like '% '||v_param->>'ancien_pos_cod'::text ||',%' OR fonc_trigger_param->>'fonc_trig_pos_cods' like '% '||v_param->>'nouveau_pos_cod'::text ||',%' )))
			and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
		)
	loop

    -- par défaut on execute la fonction d'EA trouvée
    v_do_it := true;
    
	  -- on boucle sur tous les évenements qui déclenchent des effets, mais certains déclencheurs ont des paramètres supplémentaires à vérifier.
	  if row.fonc_type = 'POS' then -- -------------------------------------------------------------------------------------

        -- par défaut on ne déclenche pas
        v_do_it := false ;    -- type POS, on vérifie si les conditions sont remplies: arrive/quitte et condition perso

-- 'trig_sens', 'trig_rearme', 'trig_condition',

        if row.fonc_trigger_param->>'trig_rearme' != -1 then

            if row.fonc_trigger_param->>'fonc_trig_pos_cods' like '% '||v_param->>'nouveau_pos_cod'::text ||',%' then
                -- le perso arrive sur la case
                if row.fonc_trigger_param->>'trig_sens' = 0 then

                else

            else
                -- le perso quitte la case

            end if;

        end if;


	  elseif row.fonc_type = 'CES' then -- -------------------------------------------------------------------------------------
	      -- CES = Change d'Etat de Santé, au premier passage on memorise la santé, aux passages suivants on vérifie le seuil de déclenchement

        -- par défaut on ne déclenche pas
        v_do_it := false ;    -- type CES avec des conditions non-remplies pour cet EA (pas encore le passage de seuil ou premier passage)

        select pfonc_param into v_pfonc_param from fonction_specifique_perso where pfonc_fonc_cod=row.fonc_cod and pfonc_perso_cod=v_perso_cod ;
        if found then
            -- cet EA a déjà été déclenché on vérifie s'il y a un état de santé connu sinon on l'ajoute.

            -- on commence par récuperer l'état de santé actuel
            select ((100*perso_pv::numeric)/perso_pv_max)::integer, f_to_numeric(COALESCE(v_pfonc_param->>'etat_sante'::text, '0')) into v_etat_sante, v_sante_avant from perso where perso_cod=v_perso_cod ;
            if v_sante_avant > 0 then

                -- on connait l'état de santé précédent on verifie s'il y a un changement d'état par rapport à l'état actuel
                if v_etat_sante != v_sante_avant then

                    -- l'état de santé du perso a changé, on vérifie si cela déclenche l'EA
                    select f_to_numeric(split_part(row.fonc_trigger_param->>'fonc_trig_sante'::text, '-', 2)), f_to_numeric(split_part(row.fonc_trigger_param->>'fonc_trig_sante'::text, '-', 1)) into v_sante_min, v_sante_max ;

                    if (    ( (row.fonc_trigger_param->>'fonc_trig_sens'::text = '0') and ((v_etat_sante <= v_sante_max  and  v_sante_avant >  v_sante_max) or (v_etat_sante >= v_sante_min and v_sante_avant <  v_sante_min)))
                         or ( (row.fonc_trigger_param->>'fonc_trig_sens'::text = '-1') and (v_etat_sante <= v_sante_max) and (v_sante_avant >  v_sante_max) )
                         or ( (row.fonc_trigger_param->>'fonc_trig_sens'::text = '1') and (v_etat_sante >= v_sante_min) and (v_sante_avant <  v_sante_min) ) ) then
                        v_do_it := true ;   -- on déclenche le trigger
                    end if;

                    -- mise à jour de l'état de santé pour les prochains déclenchements
                    update fonction_specifique_perso set pfonc_param=json_build_object( 'etat_sante' , v_etat_sante) where pfonc_fonc_cod=row.fonc_cod and pfonc_perso_cod=v_perso_cod ;
                end if;
            else
                -- mise à jour de l'état de santé pour les prochains déclenchements (car premier passage)
                update fonction_specifique_perso set pfonc_param=json_build_object( 'etat_sante' , v_etat_sante) where pfonc_fonc_cod=row.fonc_cod and pfonc_perso_cod=v_perso_cod ;
            end if;

        else

            -- Première vérification (mais attention ce n'est pas un déclenchement, init des valeurs seulement!)
            select ((100*perso_pv::numeric)/perso_pv_max)::integer into v_etat_sante from perso where perso_cod=v_perso_cod ;
            insert into fonction_specifique_perso(pfonc_fonc_cod, pfonc_perso_cod, pfonc_ddda, pfonc_encours, pfonc_param) VALUES (row.fonc_cod, v_perso_cod, '2000-01-01 00:00:00', 0, json_build_object( 'etat_sante' , v_etat_sante));

        end if;

	  elseif v_evenement = 'BMC' then -- -------------------------------------------------------------------------------------
	      -- compteur globaux indépendant d'un monstre genérique ou d'un perso
        if NOT (
                (row.fonc_trigger_param->>'fonc_trig_compteur'::text = v_param->>'bonus_type'::text)
            and (
                  (       (row.fonc_trigger_param->>'fonc_trig_sens'::text = '1')
                      and (v_param->>'valeur_avant'::text)::numeric<(row.fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric
                      and (v_param->>'valeur_apres'::text)::numeric>=(row.fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric
                  )
                or
                  (
                          (row.fonc_trigger_param->>'fonc_trig_sens'::text = '-1')
                      and (v_param->>'valeur_avant'::text)::numeric>(row.fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric
                      and (v_param->>'valeur_apres'::text)::numeric<=(row.fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric
                  )
                )
            )  then
            v_do_it := false ;    -- type BMC avec des conditions non-remplies pour cet EA (pas le bon compteur ou pas encore le passage de seuil)
        end if;

    elseif v_evenement = 'MAL' then -- ---------------------------------------------------------------------------------
        -- Rechercher les infos sur le sorts si code fourni ou si on les a directement (cas des objets bonus)
        if (v_param->>'num_sort'::text)::numeric is not null then
            select sort_aggressif, sort_soutien into v_sort_aggressif, v_sort_soutien from sorts where sort_cod=(v_param->>'num_sort'::text)::numeric ;
        else
            select v_param->>'sort_aggressif'::text, v_param->>'sort_soutien'::text into v_sort_aggressif, v_sort_soutien ;
        end if;

        if NOT (
                (
                      (row.fonc_trigger_param->>'fonc_trig_type_benefique'::text = 'O' and v_sort_soutien = 'O')
                    or
                      (row.fonc_trigger_param->>'fonc_trig_type_agressif'::text = 'O' and v_sort_aggressif = 'O')
                    or
                      (row.fonc_trigger_param->>'fonc_trig_type_neutre'::text = 'O' and v_sort_soutien = 'N' and v_sort_aggressif = 'N')
                )
              and
                (
                      (row.fonc_trigger_param->>'fonc_trig_effet'::text = '1' and v_cible_cod is null)
                    or
                      (row.fonc_trigger_param->>'fonc_trig_effet'::text = 'N' and v_cible_cod is not null)
                )
            )  then
            v_do_it := false ;    -- type MAL avec des conditions non-remplies pour cet EA (pas le bon type de sort)
        end if;

    elseif v_evenement = 'MAC' then -- ---------------------------------------------------------------------------------
        -- Rechercher les infos sur le sorts si code fourni ou si on les a directement (cas des objets bonus)
        if (v_param->>'num_sort'::text)::numeric is not null then
            select sort_aggressif, sort_soutien into v_sort_aggressif, v_sort_soutien from sorts where sort_cod=(v_param->>'num_sort'::text)::numeric ;
        else
            select v_param->>'sort_aggressif'::text, v_param->>'sort_soutien'::text into v_sort_aggressif, v_sort_soutien ;
        end if;

        if NOT (
                (row.fonc_trigger_param->>'fonc_trig_type_benefique'::text = 'O' and v_sort_soutien = 'O')
              or
                (row.fonc_trigger_param->>'fonc_trig_type_agressif'::text = 'O' and v_sort_aggressif = 'O')
              or
                (row.fonc_trigger_param->>'fonc_trig_type_neutre'::text = 'O' and v_sort_soutien = 'N' and v_sort_aggressif = 'N')
            )  then
            v_do_it := false ;    -- type MAC avec des conditions non-remplies pour cet EA (pas le bon type de sort)
        end if;

    elseif v_evenement = 'OTR' then -- ---------------------------------------------------------------------------------
        -- Reception d'objet en transactio. Vérifier s'il s'agit d'objet attendu!!!
        v_do_it := false ;    -- par défaut on ne fait rien!

        select obj_cod, obj_gobj_cod into v_obj_cod, v_gobj_cod
            from objets where obj_cod = f_to_numeric(v_param->>'obj_cod') ;


        if ( f_est_dans_la_liste(v_gobj_cod, 'gobj_cod', (row.fonc_trigger_param->>'fonc_trig_objet')::json ) ) then

            -- l'objet est dans la liste on déclenche l'EA
            v_do_it := true ;

            -- traitement de la transation de l'objet dans la liste
            -- S=>Supprimer l’objet reçu , I=>Inventaire, L=>Laisser en transaction et R=>Refuser la transaction
            if row.fonc_trigger_param->>'fonc_trig_dans_liste' = 'S' then
                -- on récupère (pour les events) d'abord, puis on supprime l'objet
                perform accepte_transaction( f_to_numeric(v_param->>'tran_cod')::integer ) ;
                perform f_del_objet( v_obj_cod ) ;
            elseif row.fonc_trigger_param->>'fonc_trig_dans_liste' = 'I' then
                perform accepte_transaction( f_to_numeric(v_param->>'tran_cod')::integer ) ;
            elseif row.fonc_trigger_param->>'fonc_trig_dans_liste' = 'R' then
                delete from transaction where tran_cod=f_to_numeric(v_param->>'tran_cod') ;
            end if;

        else

            -- l'objet n'est dans la liste on ne déclenche pas l'EA mais on traite la transaction
            -- S=>Supprimer l’objet reçu , I=>Inventaire, L=>Laisser en transaction et R=>Refuser la transaction
            if row.fonc_trigger_param->>'fonc_trig_hors_liste' = 'S' then
                -- on récupère (pour les events) d'abord, puis on supprime l'objet
                perform accepte_transaction( f_to_numeric(v_param->>'tran_cod')::integer ) ;
                perform f_del_objet( v_obj_cod ) ;
            elseif row.fonc_trigger_param->>'fonc_trig_hors_liste' = 'I' then
                perform accepte_transaction( f_to_numeric(v_param->>'tran_cod')::integer ) ;
            elseif row.fonc_trigger_param->>'fonc_trig_hors_liste' = 'R' then
                delete from transaction where tran_cod=f_to_numeric(v_param->>'tran_cod') ;
            end if;

        end if;


	  end if;

    -- -------------------------------------------------------------------------------------
    -- seulement si tous les paramètres du triggers sont vérifiés
    if v_do_it then

        -- certaines EA on des déclencheurs qui font des actions -------------------------------------------------------
        if v_evenement = 'BMC' then -- changement de nom du perso (si monstre generique)
            if (coalesce(row.fonc_trigger_param->>'fonc_trig_nom'::text, '') != '') and (v_gmon_nom is not null) then

                v_nom:= substr(v_perso_nom, 1, COALESCE(NULLIF(strpos(v_perso_nom, ' (n°')-1,-1), char_length(v_perso_nom))) ;
                update perso set perso_nom = replace(replace(row.fonc_trigger_param->>'fonc_trig_nom'::text,'[nom_generique]',v_gmon_nom), '[nom]', v_nom) ||' (n° '||trim(to_char(perso_cod,'99999999'))||')' where perso_cod=v_perso_cod;
            end if;

            if trim(row.fonc_trigger_param->>'fonc_trig_raz'::text) = 'O' then
                v_raz := 'O' ;
            end if;
        elseif row.fonc_type = 'POS' and row.fonc_trigger_param->>''trig_rearme'' = -1 then

        ---  SELECT jsonb_set(   '{"nouveau_pos_cod":1, "ancien_pos_cod":140}', '{"nouveau_pos_cod"}', jsonb '3')

xxxxxxxxxxxxxxxxxxx
            update fonction_specifique set fonc_trigger_param= where fonc_cod=row.fonc_cod ;
        end if;


        -- --------------- maintenant executer la fonction de l'EA trouvée !
        -- retour_fonction := 'Exec fonc_cod=' || row.fonc_cod::text  || execute_fonction_specifique(v_perso_cod, v_cible_cod, row.fonc_cod, v_param) ;
        retour_fonction := execute_fonction_specifique(v_perso_cod, v_cible_cod, row.fonc_cod, v_param) ;

        if coalesce(retour_fonction, '') != '' then
          code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
        end if;
        
    end if;
    
	end loop;


  -- Pour les EA du type BMC, Post-traitement du raz (s'il y en a)
  if  v_evenement = 'BMC' and v_raz = 'O' then
      -- seulement pour les bonus du type compteur (et pas les bonus equipement)
      select tbonus_compteur into v_raz from bonus_type where tbonus_libc = v_param->>'bonus_type'::text and tbonus_compteur='O' ;
      if  v_raz = 'O' then
          delete from bonus where bonus_perso_cod = v_perso_cod and  bonus_mode != 'E' and  bonus_tbonus_libc =  v_param->>'bonus_type'::text ;
      end if;
  end if;


  -- gestion du texte de retour
	if code_retour != '' then
		code_retour := replace('<br /><b>Effets automatiques :</b><br />' || code_retour, '<br /><br />', '<br />') || '<br />';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.execute_fonctions(integer, integer, character, json) OWNER TO delain;

--
-- Name: FUNCTION execute_fonctions(integer, integer, character, json); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION execute_fonctions(integer, integer, character, json) IS 'Exécute les fonctions liées au perso_cod donné, pour le type d’événement donné : ''D'' pour Début de tour, ''M'' pour Mort, ''T'' pour Tueur, ''A'' pour Attaque, ''AC'' pour attaque subie, ''AE'' pour attaque esquivée, ''ACE'' pour Attaque subie Esquivée, ''AT'' pour attaque qui touche, ''ACT'' pour attaque subie qui touche.';
