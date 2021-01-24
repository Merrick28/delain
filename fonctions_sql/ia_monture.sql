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
/*    => 0 : docile n'utilise pas les PA             */
/*    =>  1 : course avance dans le sens du cavalier */
/*    => -1 : indompté déplacement aléatoire         */
/*    => -2 : bourrique avance en sens inverse       */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 25/01/2004 : changement pour adaptation à la      */
/*  nouvelle fonction attaque(int4,int4)             */
/* 20/02/2004 : ajout des sorts aggressifs           */
/* 05/07/2004 : IA non obligatoirement jouée         */
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
	pos_actuelle integer;		-- position
	v_x integer;					-- X
	v_y integer;					-- Y
	actif varchar(2);				-- actif ?
	distance_attaque integer;	-- portee attaque
	nb_lock integer;				-- nombre de locks combat
	v_int integer;					-- intelligence du monstre
	nb_sort_aggressif integer;	-- nombre de sorts aggressifs du monstre
	chance_sort integer;			-- chance de lancer un sort
	num_sort integer;				-- numéro du sort à lancer
	fonction_sort text;			-- fonction à lancer
	v_pv integer;					-- pv du monstre
	v_pv_max integer;				-- pv_max du monstre
	v_soutien text;				-- peut il soutenir d'autres monstres ?
	cible_soutien integer;		-- perso_cod a soutenir
	chance_mercu integer;	   -- chance de lancer mercu
	statique_combat text;		-- statique en combat ?
	statique_hors_combat text;	-- statique hors combat ?
	doit_jouer integer;			-- 0 pour non, 1 pour oui
	v_dlt timestamp;				-- dlt du monstre
	v_temps_tour integer;		-- temps du tour
	i_temps_tour interval;		-- temps du tour en intervalle
	temp_niveau integer;			-- random pour passage niveau
	limite_surcharge integer;
-------------------------------------------------------
-- variables temporaires ou de calcul
-------------------------------------------------------
	temp integer;					-- fourre tout
	temp_txt text;					-- texte temporaire
	compt_loop integer;			-- comptage de boucle pour sortie
	dep_aleatoire integer;		-- variable de calcul de dep aleatoire

-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
	v_posm integer;				-- derniere position de la monture
	v_posp integer;				-- derniere position ciblé par le perso
	v_posc integer;				-- derniere position ou c'est déplacé la monture
  v_param json;   -- parametre divers du perso (permet de sauvegarder des données pour l'IA)

begin
	doit_jouer := 0;
	code_retour := 'IA monture<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';
	limite_surcharge := 1;
/***********************************/
/* Etape 1 : on récupère les infos */
/* du monstre                      */
/***********************************/
	temp_txt := calcul_dlt2(v_monstre);
	select into 	v_niveau,
						v_exp,
						v_pa,
						v_vue,
						pos_actuelle,
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
					(perso_misc_param->>'ia_monture')::json
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;
	if actif != 'O' then
		return 'inactif !';
	end if;

  if v_pa = 0 then
    code_retour := code_retour||'Perso non joué (pa de PA).';
    return code_retour;
  end if;

  if v_pa = 12 and v_type_ia = -2 then
      -- on utilise seulement la moitié des PA pour les bourriques
      update perso set perso_pa=6 where perso_cod = v_monstre ;
  end if;

	i_temps_tour := trim(to_char(v_temps_tour,'999999999'))||' minutes';
	if v_dlt + i_temps_tour - '10 minutes'::interval >= now() then
		doit_jouer := 1;
	end if;
	temp := lancer_des(1,100);
	if temp > 50  then
		if doit_jouer = 0 then
			code_retour := code_retour||'Perso non joué.';

			return code_retour;
		end if;
	end if;



/***********************************/
/* Etape 2 : on regarde si passage */
/*  de niveau                      */
/***********************************/
-- on lance la procédure de passage de niveau
	if (v_exp >= v_niveau and v_pa >= getparm_n(8)) then
		temp_niveau := lancer_des(1,6);
		temp_txt := f_passe_niveau(v_monstre,temp_niveau);
		select into v_pa perso_pa from perso where perso_cod = v_monstre;
		code_retour := code_retour||'Passage niveau.<br>';
	end if;

/***********************************/
/* Etape 3: soin                   */
/*  si blessée                     */
/***********************************/
-- on se soigne (au mercu)
	if v_pv != v_pv_max then
		select into temp psort_cod
			from perso_sorts
			where psort_perso_cod = v_monstre
			and psort_sort_cod = 5;
		if found then
			chance_mercu := round((v_pv_max - v_pv)/v_pv_max);
			if lancer_des(1,100) > chance_mercu then
				fonction_sort := 'select nv_magie_mercurochrome('||trim(to_char(v_monstre,'9999999999'))||','||trim(to_char(v_monstre,'9999999999'))||',1)';
				execute fonction_sort;
				code_retour := code_retour||'Lancement mercurochrome.<br>';
			end if;
		end if;
	end if;

/***********************************/
/* Etape 4 : on regarde si monture */
/*  est chevauché et dompté        */
/***********************************/
  if v_type_ia != -1 then

      select perso_cod into temp from perso where perso_monture = v_monstre ;
      if found  then

          if v_type_ia = 0 then
              code_retour := code_retour||'Pas, d''action, le monstre (docile) est chevauché par perso #' ||trim(to_char(temp,'999999999999'))|| '.<br>';
              return code_retour;
          end if;

          v_posm := COALESCE( (((v_param)->>'posm')::text)::numeric , pos_actuelle);
          v_posp := COALESCE( (((v_param)->>'posp')::text)::numeric , pos_actuelle);
          v_posc := COALESCE( (((v_param)->>'posc')::text)::numeric , pos_actuelle);

          if pos_actuelle != v_posc then
              -- le joueur c'et déplacé, on donc une nouvelle direction pour bouger axe v_posc => pos_actuelle
              v_posm := v_posc ;
              v_posp := pos_actuelle ;
              v_posc := pos_actuelle ;
          end if;

          if v_posm != v_posp then
              -- bouger suivant le dernier axe de déplacement connu!
              if v_type_ia < -1 then
                  v_posc := trajectoire_projection(pos_actuelle, v_posp, v_posm, 1) ;   -- monture bourique, bouge dans l'autre sens
                  -- v_posc := trajectoire_projection(v_posm, v_posp, v_posm, distance(v_posp, v_posm)+1 ) ;   -- monture bourique, bouge dans l'autre sens
              else
                 v_posc := trajectoire_projection(pos_actuelle, v_posm, v_posp, 1) ;  -- monture course, bouge dans le même sen sque le cavalier
                 -- v_posc := trajectoire_projection(v_posm, v_posm, v_posp, distance(v_posp, v_posm)+1 ) ;  -- monture course, bouge dans le même sen sque le cavalier
              end if;

              if v_posc < 0 then
                  v_posc := pos_actuelle ;
                  code_retour := code_retour||'Pas, d''action, le monstre chevauché par perso #' ||trim(to_char(temp,'999999999999'))|| ' n''a pas trouvé où se déplacer.<br>';
              else

                  code_retour := code_retour|| 'la monture se déplace avec son cavalier' || deplace_code(v_monstre,v_posc)|| '.<br>';
                  -- on récupère les nouvelles infos
                  select ppos_pos_cod into v_posc  from perso,perso_position  where perso_cod = v_monstre and ppos_perso_cod = perso_cod;
              end if;

          else
              code_retour := code_retour||'Pas, d''action, le monstre chevauché par perso #' ||trim(to_char(temp,'999999999999'))|| ' ne connait pas la direction.<br>';
          end if ;

          -- sauvgarder les nouvelle infos de ia_monture
          update perso
            set perso_misc_param=COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || json_build_object( 'ia_monture' , json_build_object( 'posm' , v_posm, 'posp' , v_posp, 'posc' , v_posc ))::jsonb
            where perso_cod=v_monstre ;

          return code_retour;
      end if;

  end if;


/**********************************************/
/* Etape 4 : monture libre ou indompté        */
/*  deplacement aléatoire                     */
/**********************************************/
  if statique_hors_combat = 'N' then
    compt_loop := 0;
    while (v_pa >= getparm_n(9)) loop
      compt_loop := compt_loop + 1;
      exit when compt_loop >= 5;
      dep_aleatoire := f_deplace_aleatoire(v_monstre,pos_actuelle);
      select into pos_actuelle ppos_pos_cod
        from perso_position
        where ppos_perso_cod = v_monstre;
      select into v_pa perso_pa from perso
        where perso_cod = v_monstre;
    end loop;
  end if;
  code_retour := code_retour||'Monture libre ou indomptée, déplacement aléatoire.<br>';


/*************************************************/
/* Etape 5 : tout semble fini                    */
/*************************************************/

	return code_retour;
end;
$_$;


ALTER FUNCTION public.ia_monture(integer, integer) OWNER TO delain;
