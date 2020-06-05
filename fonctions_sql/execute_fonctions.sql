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

	v_protagoniste integer ;   -- Le numéro du protagoniste (cible ou perso lui-même)
	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	row record;                -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique
  v_gmon_nom text;           -- Le nom du monstre générique
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
  v_protagoniste := v_cible_cod ;   -- par défaut le protagoniste est celui fourni en paramètre

  -- Eventuellement les fonctions du monstre générique
	select into v_gmon_cod, v_gmon_nom, v_perso_nom perso_gmon_cod, gmon_nom, perso_nom from perso inner join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = v_perso_cod;
	if not found then
	    -- cas des aventuriers
      v_gmon_cod:= null ;
      v_gmon_nom:= null ;
  else
      -- pour les monstres s'il n'y a pas de protagoniste, on prend sa cible actuelle
      if v_protagoniste is null then
          select into v_protagoniste perso_cible from perso where perso_cod = v_perso_cod;
      end if;

      -- s'il n'y a pas de cible, on va en déterminer une, cela sera aussi notre protagoniste !
      if v_protagoniste is null then
          v_protagoniste := choix_perso_vue_aleatoire(v_perso_cod, 1);
          if v_protagoniste is not null then
              update perso set perso_cible=v_protagoniste where perso_cod = v_perso_cod;
          end if;
      end if;
	end if;


  -- code de retour
	code_retour := '';

	-- debug
	-- code_retour :=  'DEBUG EA('|| v_evenement || '): Perso='||v_perso_cod::text||' cible='||coalesce(v_protagoniste, 0)::text||'<br>' ;

  -- boucle sur toutes les fonctions specifiques sur l'évenement
	for row in (
		select * from fonction_specifique
		where (fonc_gmon_cod = coalesce(v_gmon_cod, -1) OR (fonc_perso_cod = v_perso_cod) OR (fonc_gmon_cod is null and fonc_perso_cod is null and v_evenement='BMC'))
			and fonc_type = v_evenement
			and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
		)
	loop

    -- par défaut on execute la fonction d'EA trouvée
    v_do_it := true;
    
	  -- on boucle sur tous les évenements qui déclenchent des effets, mais certains déclencheurs ont des paramètres supplémentaires à vérifier.
	  if v_evenement = 'BMC' then -- -------------------------------------------------------------------------------------
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
        -- Rechercher les infos sur le sorts
        select sort_aggressif, sort_soutien into v_sort_aggressif, v_sort_soutien from sorts where sort_cod=(v_param->>'num_sort'::text)::numeric ;

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
        -- Rechercher les infos sur le sorts
        select sort_aggressif, sort_soutien into v_sort_aggressif, v_sort_soutien from sorts where sort_cod=(v_param->>'num_sort'::text)::numeric ;

        if NOT (
                (row.fonc_trigger_param->>'fonc_trig_type_benefique'::text = 'O' and v_sort_soutien = 'O')
              or
                (row.fonc_trigger_param->>'fonc_trig_type_agressif'::text = 'O' and v_sort_aggressif = 'O')
              or
                (row.fonc_trigger_param->>'fonc_trig_type_neutre'::text = 'O' and v_sort_soutien = 'N' and v_sort_aggressif = 'N')
            )  then
            v_do_it := false ;    -- type MAC avec des conditions non-remplies pour cet EA (pas le bon type de sort)
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
        end if;


        -- --------------- maintenant executer la fonction de l'EA trouvée !
        -- retour_fonction := 'Exec fonc_cod=' || row.fonc_cod::text  || execute_fonction_specifique(v_perso_cod, v_protagoniste, row.fonc_cod, v_param) ;
        retour_fonction := execute_fonction_specifique(v_perso_cod, v_protagoniste, row.fonc_cod, v_param) ;

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
