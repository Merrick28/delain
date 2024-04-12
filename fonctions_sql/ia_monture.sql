--
-- Name: ia_monture(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or REPLACE FUNCTION ia_monture(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/**************************************************************/
/* fonction ia_monture                                         */
/*    reçoit en arguments :                                    */
/* $1 = perso_cod du monstre                                   */
/* $2 = type d'IA                                              */
/*    => -1 : action activation DLT seulement                     */
/*    =>  0 : docile n'utilise pas les PA                      */
/*    =>  1 : à ordre                                          */
/*    =>  2 : mixte (comme le 1 pour l'IA)                     */
/*    =>  3 : supermonture1 (comme le 1 + 1er Move gratuit)    */
/*    =>  4 : supermonture2 (comme le 1 + Move aléat. gratuit) */
/*    retourne en sortie en entier non lu                      */
/*    les évènements importants seront mis dans la             */
/*    table des evenemts admins                                */
/* Cette fonction effectue les actions automatiques            */
/*  des monstres pour éviter que le MJ qui a autre             */
/*  à faire jouer tout à la mimine....                         */
/**************************************************************/
declare
-------------------------------------------------------
-- variables E/S
-------------------------------------------------------
	code_retour text;				-- code sortie
-------------------------------------------------------
-- variables de renseignements du monstre
-------------------------------------------------------
	v_monstre alias for $1;		-- perso_cod du monstre
	v_type_ia alias for $2;		-- type de fonctionnement
	v_niveau integer;				-- niveau du monstre
	v_exp numeric;					-- xp du monstre
	v_pa integer;					-- pa du monstre
	v_vue integer;					-- distance de vue
	v_cible integer;				-- cible
	temp_cible integer;			-- cible temporaire
	v_etage integer;				-- etage
	v_pos integer;		-- position
	v_x integer;					-- X
	v_y integer;					-- Y
	actif varchar(2);				-- actif ?
	v_int integer;					-- intelligence du monstre
	v_pv integer;					-- pv du monstre
	v_pv_max integer;				-- pv_max du monstre
	statique_combat text;		-- statique en combat ?
	statique_hors_combat text;	-- statique hors combat ?
	v_dlt timestamp;				-- dlt du monstre
	v_temps_tour_actuel integer;		-- temps du tour actuel
	v_temps_tour integer;		-- temps du tour de base
	i_temps_tour interval;		-- temps du tour en intervalle

-------------------------------------------------------
-- variables temporaires ou de calcul
-------------------------------------------------------
	temp integer;					-- fourre tout
	temp_txt text;					-- texte temporaire
	text_evt text;					-- texte evenement
	compt_loop integer;			-- comptage de boucle pour sortie
	dep_aleatoire integer;		-- variable de calcul de dep aleatoire

-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
  v_cavalier integer;   -- perso_cod du cavalier
  v_param_ia json;   -- pour traiter la mise à jour des ordre
  v_param_perso json;   -- parametre du perso
  v_param_ordre json;   -- parametre divers du perso (permet de sauvegarder des données pour l'IA)
  v_ordre json;   -- 1 ordre a executer extrait de la liste des ordres
  v_type_ordre text ; -- type ordre à traiter
  dir_x integer ;     -- ordre: direction en x
  dir_y integer ;     --  ordre: direction en y
  dist integer ;     --  ordre: distance
  v_num_ordre integer ;     --  N° d'ordre actuel
  v_pos_ordre integer ;     --  pos_cod ciblé par l'ordre
  v_pos_saut integer ;     --  pos_cod ciblé par l'ordre 'sauter'
  v_perso_pa integer ;     -- PA après deplacement
  v_hors_map integer ;  -- 1 si essaye d'aller hors map
  v_etage_monture json ; -- carac de l'étage pour les monture
  v_count numeric ; -- compteur des actione speciale
  v_count_talonner numeric ; -- compteur des actione speciale
  v_count_sauter numeric ; -- compteur des actione speciale
  v_etage_talonner numeric ; -- compteur des actione speciale
  v_etage_sauter numeric ; -- compteur des actione speciale
  v_action_super numeric ; -- compteur des actione supermontures

begin
	code_retour := 'IA monture<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';

/***********************************/
/* Etape 1 : on récupère les infos */
/* du monstre                      */
/***********************************/
  if v_type_ia >= 0 then    -- calcul de DLT sauf si l'IA a até appelé à partir de la mise à jour DLT
	    temp_txt := calcul_dlt2(v_monstre);
  end if;

	select into 	v_niveau,
						v_exp,
						v_pa,
						v_vue,
						v_pos,
						v_cible,
						v_etage,
						actif,
						v_x,
						v_y,
						v_int,
						v_pv,
						v_pv_max,
						statique_combat,
						statique_hors_combat,
						v_dlt,
						v_temps_tour,
						v_temps_tour_actuel,
						v_param_ordre,
						v_param_perso,
						v_etage_monture
					limite_niveau(v_monstre),
					perso_px,
					perso_pa,
					distance_vue(v_monstre),
					ppos_pos_cod,
					perso_cible,
					pos_etage,
					perso_actif,
					pos_x,
					pos_y,
					perso_int,
					perso_pv,
					perso_pv_max,
					perso_sta_combat,
					perso_sta_hors_combat,
					perso_dlt,
					perso_temps_tour,
					coalesce(nullif(f_to_numeric(((perso_misc_param->>'calcul_dlt')::jsonb)->>'temps_tour')::integer, 0), f_temps_tour_perso(perso_cod)),
					(perso_misc_param->>'ia_monture_ordre')::json,
					perso_misc_param,
					etage_monture
		from perso,perso_position,positions,etage
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod
		and etage_cod = pos_etage;
	if actif != 'O' then
		return 'inactif !';
	end if;

  if (v_pa = 0) and (v_type_ia >= 0) then
    return code_retour||'Perso non joué (pas de PA).';
  end if;

  -- détection nouvelle DLT: décrementation des compteurs liés aux actions speciales de la monture
  if coalesce(f_to_numeric(((v_param_perso->>'calcul_dlt')::jsonb)->>'activation_dlt')::integer, 0) = 0 then
      -- décrementation des compteurs et réarmement pour prochaine détection dlt

      v_etage_talonner := coalesce( f_to_numeric(v_etage_monture->>'ordre_talonner') , 0) ;
      v_count_talonner := GREATEST(0, coalesce(f_to_numeric( ((v_param_perso->>'ia_monture')::jsonb)->>'nb_talonner')  , 0) - v_etage_talonner ) ;
      select count(*) into v_count from (  select json_array_elements( (perso_misc_param->>'ia_monture_ordre')::json ) as v from perso where perso_cod=v_monstre  ) as s where (v->>'type_ordre')='TALONNER'  ;
      v_count_talonner := v_count_talonner + v_count ;
      if v_etage_talonner < 1 and v_count > 0 then
          v_count_talonner := 1 ;
      end if;

      v_etage_sauter := coalesce( f_to_numeric(v_etage_monture->>'ordre_sauter') , 0) ;
      select count(*) into v_count from (  select json_array_elements( (perso_misc_param->>'ia_monture_ordre')::json ) as v from perso where perso_cod=v_monstre  ) as s where (v->>'type_ordre')='SAUTER'  ;
      v_count_sauter := GREATEST(0, coalesce(f_to_numeric( ((v_param_perso->>'ia_monture')::jsonb)->>'nb_sauter') , 0) - v_etage_sauter ) ;
      v_count_sauter := v_count_sauter + v_count ;
      if v_etage_sauter < 1 and v_count > 0 then
          v_count_sauter := 1 ;
      end if;

      update perso set
          perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb)
                                || (json_build_object( 'ia_monture' , (json_build_object( 'nb_talonner' , v_count_talonner, 'nb_sauter', v_count_sauter, 'nb_super', 0 )::jsonb))::jsonb)
                                || (json_build_object( 'calcul_dlt' ,  ((perso_misc_param->>'calcul_dlt')::jsonb || json_build_object('activation_dlt', 1 )::jsonb))::jsonb)
          where perso_cod=v_monstre ;
  end if;

    -- -------------------------------------------------------------------------------------------------------------------
  -- Si l'IA est actionné par le activation de DLT, ne fait rien faire de plus
  if v_type_ia = -1 then
      code_retour := code_retour||'Pas, d''action, le monstre a juste activé sa DLT.<br>';
      return code_retour;
  end if;


/***********************************/
/* Etape 2 : on regarde si monture */
/*  est chevauché                  */
/***********************************/
  select perso_cod into v_cavalier from perso where perso_monture = v_monstre ;
  if not found  then
      if statique_hors_combat = 'N' and v_pa = 12 then
          dep_aleatoire := f_deplace_aleatoire(v_monstre,v_pos);
          -- pour eviter une trop grosse dispersion pendant les courses de montures, on ne fait qu'un seul deplacement par DLT (celui ou la monture commence avec 12 PA)
          return code_retour||'Monture libre, déplacement aléatoire.<br>';
      else
          return code_retour||'Monture libre, mais statique!<br>';
      end if;
  end if;

  -- -------------------------------------------------------------------------------------------------------------------
  -- monture docile, ne fait rien quand elle est chevauché
  if v_type_ia = 0 then
      code_retour := code_retour||'Pas, d''action, le monstre (docile) est chevauché par perso #' ||trim(to_char(temp,'999999999999'))|| '.<br>';
      return code_retour;
  end if;


/******************************************/
/* Etape3: egrainnage des PA et talonnade */
/******************************************/
  -- premier ordre a traiter
  select v into v_ordre  from ( select  json_array_elements( v_param_ordre ) as v ) as s order by (v->>'ordre')::integer limit 1 ;
  if not found  then
      v_type_ordre := 'DIRIGER' ;
  else
      v_type_ordre := coalesce(v_ordre->>'type_ordre', 'DIRIGER' );
      v_num_ordre := f_to_numeric(v_ordre->>'ordre') ;
  end if;

  -- excepter l'ordre TALONNER, on traite les ordres en fonction de l'avancement de la DLT
  if v_type_ordre != 'TALONNER' then

      i_temps_tour := trim(to_char( floor(v_temps_tour_actuel * v_pa / 12) ,'999999999'))||' minutes';
      if (v_dlt - i_temps_tour) >= now() then
        return code_retour||'Perso non joué (égrainage de PA).';
      end if;

  else

      -- on verifie s'il n'y a qu'un ordre dans la pile, on ne va pas traiter le talonnage
      select count(*) into temp from ( select  json_array_elements( v_param_ordre ) as v  ) as s    ;
      if temp > 1 then

          -- on supprimer l'ordre talonner de la liste des ordres, ainsi la monture va traiter immédiatement l'ordre suivant !
          code_retour := code_retour||'Suppression de l''ordre TALONNER, traitement immédiat de l''ordre suivant.<br>';
          perform insere_evenement(v_cavalier, v_monstre, 107, '[cible] réagi à la talonnade de [attaquant].', 'O', NULL);

          select jsonb_agg(v) into v_param_ia from (  select  json_array_elements( v_param_ordre ) as v ) s where v->>'ordre' <> v_num_ordre ;
          v_param_ordre := v_param_ia ;
          update perso
              set perso_misc_param =  COALESCE(perso_misc_param::jsonb, '{}'::jsonb)
                                  || (json_build_object( 'ia_monture_ordre' , (v_param_ia::jsonb))::jsonb)
              where perso_cod=v_monstre ;

      end if;
  end if;



/***********************************/
/* Etape 4 : action les ordres     */
/***********************************/

  -- -------------------------------------------------------------------------------------------------------------------
  -- monture à ordre ou mixte, reéalise un ordre, recherche de l'ordre à executer
  select v into v_ordre from ( select json_array_elements(v_param_ordre) as v ) as s order by (v->>'ordre')::integer limit 1 ;
  if not found  then
      -- on consomme quand même des PA, la monture ne garde pas de PA réserve pour plus tard !
      update perso set perso_pa = GREATEST(0, perso_pa - 1) where perso_cod=v_monstre ;
      perform insere_evenement(v_monstre, v_monstre, 113, '[perso_cod1] n''a pas d''ordre à traiter et glande un peu.', 'O', 'N', null);
      return code_retour||'Monture chevauchée mais en attente d''ordre, elle rumine (glande)!<br>';
  end if;

  v_num_ordre := f_to_numeric(v_ordre->>'ordre') ;
  dist :=  f_to_numeric(v_ordre->>'dist') ;     --  ordre: distance
  dir_x :=  f_to_numeric(v_ordre->>'dir_x') ;     --  ordre: dir x
  dir_y :=  f_to_numeric(v_ordre->>'dir_y') ;     --  ordre: dir y
  v_type_ordre := coalesce(v_ordre->>'type_ordre', 'DIRIGER' );

  if v_type_ordre = 'TALONNER' then
        perform insere_evenement(v_monstre, v_monstre, 114, '[perso_cod1] n''a pas réussi à traiter un ordre "TALONNER".', 'O', 'N', null);
        code_retour := code_retour||'Impossible de traiter un TALONNAGE ici.<br>';
        return code_retour;
  end if;

  temp_txt := '';
  if dir_x = 0 and dir_y = 0 then
      -- le perso a fait un echech critique, il a programmé un mauvais ordre, on realise un déplacement aléatoire
      dep_aleatoire := f_deplace_aleatoire(v_monstre,v_pos);
      code_retour := code_retour||'réalisation d''un ordre aléatoire.<br>';
  else
      -- recherche de la position où aller
      select pos_cod into v_pos_ordre from positions where (pos_x = v_x + dir_x) and (pos_y = v_y + dir_y) and pos_etage=v_etage ;
      v_pos_saut := null ;  -- utilisé (not null) en cas de saut !
      if found then
          if v_type_ordre = 'SAUTER' then
              -- interdir les sauts par dessus les murs
               if not exists (select 1 from murs where mur_pos_cod = v_pos_ordre and mur_illusion!='O') then
                  -- chercher la position du saut
                  select pos_cod into v_pos_saut from positions where (pos_x = v_x + 2*dir_x) and (pos_y = v_y + 2*dir_y) and pos_etage=v_etage ;
               end if;
          end if;

          -- faire le deplacement normal ou saut
          if v_pos_saut is not null then
              -- saut à 2 cases!
              perform insere_evenement(v_monstre, v_monstre, 114, '[perso_cod1] a fait un saut.', 'O', NULL);
              temp_txt := deplace_code(v_monstre, v_pos_saut, 2);
          else
              -- deplacement basic
              temp_txt := deplace_code(v_monstre, v_pos_ordre);
          end if;
      else
          code_retour := code_retour||'Positions de l''ordre non trouvé.<br>';
          temp_txt := '0#0#9';
      end if;
  end if;

  -- verifier si le déplacement a été effectué (non effectué en cas: de mur, de PA insuffisants, ou hors map)
  select perso_pa into v_perso_pa  from perso,perso_position,positions where perso_cod = v_monstre 	and ppos_perso_cod = v_monstre and ppos_pos_cod = pos_cod;
	if v_perso_pa = v_pa then

        -- le monstre a essayé de ce déplacer, il n'a pas réussi, on lui consomme quand même des PA liés à la tentative
        text_evt := '[cible] ne peut pas emmener [attaquant] vers ' || trim(to_char(v_x + dir_x,'99999999')) || ',' || trim(to_char(v_y + dir_y,'99999999')) || ',' || trim(to_char(v_etage,'99999999'));
        if temp_txt = '' then
            temp := '0' ;
        else
            temp := split_part(temp_txt,'#',3) ;
        end if;

        if temp = '2' then
            text_evt := text_evt || ' (mur)';
        elsif temp = '6' then
            text_evt := text_evt || ' (inaccessible)';
        elsif temp = '7' then
            text_evt := text_evt || ' (terrain)';
        elsif temp = '8' then
            text_evt := text_evt || ' (poids)';
        elsif temp = '9' then
            text_evt := text_evt || ' (hors étage)';
        elsif temp = '10' then
            text_evt := text_evt || ' (fuite)';
        else
            text_evt := '';
        end if;

        if text_evt <> '' then
            perform insere_evenement(v_cavalier, v_monstre, 2, text_evt, 'O', 'N', null);
        else
            perform insere_evenement(v_monstre, v_monstre, 113, '[perso_cod1] ne peut pas traiter l''ordre de son cavalier et préfère glander.', 'O', 'N', null);
        end if;


        -- update perso set perso_pa = GREATEST(0, perso_pa - get_pa_dep(v_monstre) ) where perso_cod = v_monstre ;
        update perso set perso_pa = GREATEST(0, perso_pa - 1 ) where perso_cod = v_monstre ;
        code_retour := code_retour || text_evt ||'Consommation de PA.<br>';

  else

      -- maintenant on lui supprime/modifie l'ordre de sa liste (calcul des ordres restants): v_param_ia
      v_param_ia := null ;  -- null si plus d'autre ordres
      select coalesce(jsonb_agg(v)::json, '[]'::json) into v_param_ia from (  select  json_array_elements( v_param_ordre ) as v ) s where v->>'ordre' <> v_num_ordre ;
      if dist = 1 then
          -- sauvgarder les nouvelles infos de ia_monture (avec supression du premier ordre)
          code_retour := code_retour||'Suppression de l''ordre.<br>';
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'ia_monture_ordre' , (v_param_ia::jsonb))::jsonb)
              where perso_cod=v_monstre ;
      else
            -- sauvgarder les nouvelles infos de ia_monture (avec modification du premier ordre avec une distance de moins)
          code_retour := code_retour||'Modification de l''ordre.<br>';
          dist := dist - 1;
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'ia_monture_ordre' , ((v_param_ia::jsonb) || (json_build_object( 'ordre' , v_num_ordre, 'dir_x' , dir_x, 'dir_y' , dir_y , 'dist' , dist )::jsonb)))::jsonb)
              where perso_cod=v_monstre ;
      end if;


      -- cas des supermontures 1er deplacement gratuit pour le bip² ou un deplacement aléatoire gratuit  pour le Coyote
      -- on recrédite les PA depensés en lui redonnant ces PA initiaux
      if v_type_ia = 3 or v_type_ia = 4 then
          select coalesce(f_to_numeric( ((perso_misc_param->>'ia_monture')::jsonb)->>'nb_super') ,0) into v_action_super from perso where perso_cod = v_monstre ;

          -- comptage des proies sur la case d'arrivée! (race='Monture delicius' versus 'Monture vulgaris'
          SELECT count(*) into v_count from perso p1
						  	join perso_position ppos1 on ppos1.ppos_perso_cod=p1.perso_cod
							  join perso_position ppos2 on ppos2.ppos_pos_cod=ppos1.ppos_pos_cod and ppos2.ppos_perso_cod<>ppos1.ppos_perso_cod
						    join perso p2 on p2.perso_cod=ppos2.ppos_perso_cod
						    join race on race_cod=p2.perso_race_cod
						    where p1.perso_cod=v_monstre and p2.perso_type_perso=2 and race_nom='Monture delicius' ;

          -- si la superaction n'a pas déjà été réalisé cette DLT et que la supermonture est le Bip² ou le Coyote sur une proie, alors on fait la super action !
          if v_action_super = 0 and (v_type_ia = 3 or v_count > 0) then
              update perso set perso_pa = v_pa, perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb)  || (json_build_object( 'ia_monture', (
                                          '{"nb_super":1}'::jsonb
                                       || json_build_object('nb_sauter' , coalesce(f_to_numeric( ((perso_misc_param->>'ia_monture')::jsonb)->>'nb_sauter') , 0))::jsonb
                                       || json_build_object('nb_talonner' , coalesce(f_to_numeric( ((perso_misc_param->>'ia_monture')::jsonb)->>'nb_talonner') , 0))::jsonb
                        ))::jsonb) where perso_cod = v_monstre ;

              perform insere_evenement(v_monstre, v_monstre, 114, '[perso_cod1] a fait une super action!', 'O', NULL);
          end if;
      end if;

	end if;


/*************************************************/
/* Etape 4 : tout semble fini                    */
/*************************************************/
	return code_retour||'La monture a fini de traiter un ordre.<br>';

end;
$_$;


ALTER FUNCTION public.ia_monture(integer, integer) OWNER TO delain;
