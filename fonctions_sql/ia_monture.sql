--
-- Name: ia_monture(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or REPLACE FUNCTION ia_monture(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction ia_monture                             */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/* $2 = type d'IA                                    */
/*    =>  0 : docile n'utilise pas les PA            */
/*    =>  1 : à ordre                                */
/*    =>  2 : mixte (comme le 1 pour l'IA)           */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
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
  v_param json;   -- parametre divers du perso (permet de sauvegarder des données pour l'IA)
  v_ordre json;   -- 1 ordre a executer extrait de la liste des ordres
  dir_x integer ;     -- ordre: direction en x
  dir_y integer ;     --  ordre: direction en y
  dist integer ;     --  ordre: distance
  v_num_ordre integer ;     --  N° d'ordre actuel
  v_pos_ordre integer ;     --  pos_cod ciblé par l'ordre
  v_perso_pa integer ;     -- PA après deplacement
  v_hors_map integer ;  -- 1 si essaye d'aller hors map

begin
	code_retour := 'IA monture<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';

/***********************************/
/* Etape 1 : on récupère les infos */
/* du monstre                      */
/***********************************/
	temp_txt := calcul_dlt2(v_monstre);
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
						v_param
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
					f_temps_tour_perso(perso_cod),
					(perso_misc_param->>'ia_monture_ordre')::json
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;
	if actif != 'O' then
		return 'inactif !';
	end if;

  if v_pa = 0 then
    return code_retour||'Perso non joué (pa de PA).';
  end if;

	i_temps_tour := trim(to_char( floor(v_temps_tour_actuel * v_pa / 12) ,'999999999'))||' minutes';
	if (v_dlt - i_temps_tour) >= now() then
		return code_retour||'Perso non joué (égrainage de PA).';
	end if;


/***********************************/
/* Etape 2 : on regarde si monture */
/*  est chevauché                  */
/***********************************/
  select perso_cod into v_cavalier from perso where perso_monture = v_monstre ;
  if not found  then
      if statique_hors_combat = 'N' then
          dep_aleatoire := f_deplace_aleatoire(v_monstre,v_pos);
          return code_retour||'Monture libre, déplacement aléatoire.<br>';
      else
          return code_retour||'Monture libre, mais statique!<br>';
      end if;
  end if;

/***********************************/
/* Etape 3 : action suivant type ia*/
/***********************************/

  -- -------------------------------------------------------------------------------------------------------------------
  -- monture docile, ne fait rien quand elle est chevauché
  if v_type_ia = 0 then
      code_retour := code_retour||'Pas, d''action, le monstre (docile) est chevauché par perso #' ||trim(to_char(temp,'999999999999'))|| '.<br>';
      return code_retour;
  end if;

  -- -------------------------------------------------------------------------------------------------------------------
  -- monture à ordre ou mixte, reéalise un ordre, recherche de l'ordre à executer
  select v into v_ordre from ( select json_array_elements(v_param) as v from perso where perso_cod=v_monstre ) as s order by v->>'ordre' limit 1 ;
  if not found  then
      return code_retour||'Monture chevauchée mais en attente d''ordre.<br>';
  end if;

  v_num_ordre := f_to_numeric(v_ordre->>'ordre') ;
  dist :=  f_to_numeric(v_ordre->>'dist') ;     --  ordre: distance
  dir_x :=  f_to_numeric(v_ordre->>'dir_x') ;     --  ordre: dir x
  dir_y :=  f_to_numeric(v_ordre->>'dir_y') ;     --  ordre: dir y

  temp_txt := '';
  if dir_x = 0 and dir_y = 0 then
      -- le perso a fait un echech critique, il a programmé un mauvais ordre, on realise un déplacement aléatoire
      dep_aleatoire := f_deplace_aleatoire(v_monstre,v_pos);
      code_retour := code_retour||'réalisation d''un ordre aléatoire.<br>';
  else
      -- recherche de la position où aller
      select pos_cod into v_pos_ordre from positions where (pos_x = v_x + dir_x) and (pos_y = v_y + dir_y) and pos_etage=v_etage ;
      if found then
          temp_txt := deplace_code(v_monstre, v_pos_ordre);
      else
          code_retour := code_retour||'Positions de l''ordre non trouvé.<br>';
          temp_txt := '0#0#9';
      end if;
  end if;

  -- verifier si le déplacement a été effectué (non effectué en cas: de mur, de PA insuffisants, ou hors map)
  select perso_pa into v_perso_pa  from perso,perso_position,positions where perso_cod = v_monstre 	and ppos_perso_cod = v_monstre and ppos_pos_cod = pos_cod;
	if v_perso_pa = v_pa then

	      -- le monstre a essayé de ce déplacer, il n'a pas réussi, on lui consomme quand même des PA liés à la tentative
          text_evt := '[cible] refuse d’emmener [attaquant] vers ' || trim(to_char(v_x + dir_x,'99999999')) || ',' || trim(to_char(v_y + dir_y,'99999999')) || ',' || trim(to_char(v_etage,'99999999'));
          if temp_txt = '' then
              temp := '0' ;
          else
              temp := split_part(temp_txt,'#',3) ;
          end if;

          if temp = '2' then
              text_evt := text_evt || ' (mur)';
          elsif temp = '6' then
              text_evt := text_evt || ' (innacessible)';
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
          end if;


	    update perso set perso_pa = GREATEST(0, perso_pa - get_pa_dep(v_monstre) ) where perso_cod = v_monstre ;
      code_retour := code_retour||'Consommation de PA.<br>';
	end if;

  -- maintenant on lui supprime/modifie l'ordre de sa liste (calcul des ordres restants): v_param_ia
  select coalesce(jsonb_agg(v)::json, '[]'::json) into v_param_ia from (  select  json_array_elements( v_param ) as v ) s where v->>'ordre' <> v_num_ordre ;
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


/*************************************************/
/* Etape 4 : tout semble fini                    */
/*************************************************/
	return code_retour||'La monture a traité un ordre.<br>';

end;
$_$;


ALTER FUNCTION public.ia_monture(integer, integer) OWNER TO delain;
