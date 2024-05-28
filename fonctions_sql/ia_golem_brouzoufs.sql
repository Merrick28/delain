
--
-- Name: ia_golem_brouzoufs(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION ia_golem_brouzoufs(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction ia_golem_brouzoufs                    */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 06/01/2010 : 																		 */
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
	v_pv integer;					-- pv du monstre
	v_pv_max integer;				-- pv_max du monstre
	statique_combat text;		-- statique en combat ?
	statique_hors_combat text;	-- statique hors combat ?
	doit_jouer integer;			-- 0 pour non, 1 pour oui
	v_dlt timestamp;				-- dlt du monstre
	v_temps_tour integer;		-- temps du tour
	i_temps_tour interval;		-- temps du tour en intervalle
	temp_niveau integer;			-- random pour passage niveau
-------------------------------------------------------
-- variables temporaires ou de calcul
-------------------------------------------------------
	temp integer;					-- fourre tout
	temp_txt text;					-- texte temporaire
	compt_loop integer;			-- comptage de boucle pour sortie
	compt_loop2 integer;		-- comptage de boucle pour sortie
	dep_aleatoire integer;		-- variable de calcul de dep aleatoire
	distance_cible integer;		-- distance de la cible
-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
	nb_tas_brouzoufs integer;	-- nombre de joueurs en vue
	nb_cible_en_vue integer;	-- nombre de cibles en vue
	pos_cible integer;			-- position de la cible
	pos_dest integer;				-- destination
	quantite integer;				--nombre de brouzoufs

begin
	doit_jouer := 0;
	code_retour := E'IA golem brouzoufs \n Monstre '||trim(to_char(v_monstre,'999999999999'))||E'\n';
	pos_dest := 0;
/***********************************/
/* Etape 1 : on récupère les infos */
/* du monstre                      */
/***********************************/
	--temp_txt := calcul_dlt2(v_monstre);
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
						v_pv,
						v_pv_max,
						statique_combat,
						statique_hors_combat,
						v_dlt,
						v_temps_tour
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
					perso_pv,
					perso_pv_max,
					perso_sta_combat,
					perso_sta_hors_combat,
					perso_dlt,
					perso_temps_tour
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;
	if actif != 'O' then
		return 'inactif !';
	end if;
	i_temps_tour := trim(to_char(v_temps_tour,'99999999999'))||' minutes'; --y'a un truc étrange ici les minutes ne sont pas déclarées comme interval !!!
	if v_dlt + i_temps_tour - '10 minutes'::interval >= now() then
		doit_jouer := 1;
	end if;
	temp := lancer_des(1,100);
	if temp > 50 then
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
		code_retour := code_retour||E'Passage niveau.\n';
	end if;
/************************************/
/* Etape 3 : on regarde si il y a   */
/*  des brouzoufs dans la vue       */
/************************************/
	select into nb_tas_brouzoufs count(por_pos_cod)
		from or_position,positions
		where pos_x between (v_x - v_vue) and (v_x + v_vue)
		and pos_y between (v_y - v_vue) and (v_y + v_vue)
		and pos_etage = v_etage
		and por_palpable = 'O'
		and por_pos_cod = pos_cod
		and trajectoire_vue_murs(pos_actuelle,pos_cod, false) = 1;
	code_retour := code_retour||'Nombre de tas de brouzoufs en vue : '||trim(to_char(nb_tas_brouzoufs,'9999999999'))||E'\n';


-- si pas de tas de brouzoufs, on sort.....
	if nb_tas_brouzoufs = 0 then
		update perso set perso_cible = null where perso_cod = v_monstre;
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
		code_retour := code_retour||E'Aucun tas de brouzoufs en vue, déplacement aléatoire.\n';
		return code_retour;
	end if;
-- sinon, on reste....
	if v_cible is null then
		code_retour := code_retour||E'Pas de cible départ.\n';
	else
		code_retour := code_retour||'Cible départ : '||trim(to_char(v_cible,'9999999999999'))||E'. On va faire un déplacement aléatoire avant\n';
		dep_aleatoire := f_deplace_aleatoire(v_monstre,pos_actuelle);
	end if;
/*************************************/
/* Etape 4 : on regarde si la cible  */
/*  est sur la même case             */
/*************************************/
	compt_loop := 0;
	while (v_pa > 2) loop
		compt_loop := compt_loop + 1;
				exit when compt_loop >= 15;
		-- On va chercher quel est le tas de brouzoufs le plus proche ainsi que sa position
		select into nb_tas_brouzoufs,pos_cible,quantite por_cod,por_pos_cod,por_qte
			from or_position,positions
			where pos_x between (v_x - v_vue) and (v_x + v_vue)
			and pos_y between (v_y - v_vue) and (v_y + v_vue)
			and pos_etage = v_etage
			and por_palpable = 'O'
			and por_pos_cod = pos_cod
			and trajectoire_vue_murs(pos_actuelle,pos_cod,false) = 1
			order by distance(pos_actuelle,pos_cod) asc
			limit 1;
		--si sur la case, on le ramasse, on remet les pa dépensés, et on augmente les pxs
		if (distance(pos_actuelle,pos_cible) = 0) then
			temp_txt := ramasse_or(v_monstre,nb_tas_brouzoufs);
			quantite := min(quantite,50);
			if v_pa < 12 then
				update perso set perso_pa = perso_pa + 1,perso_px = perso_px + quantite where perso_cod = v_monstre;
			end if;
		--On se déplace si le tas n'est pas sur la case
		else
			code_retour := code_retour||'Déplacement vers la cible '||trim(to_char(pos_cible,'999999999999999'))||' ('||trim(to_char(pos_dest,'999999999999999'))||E')\n';
				compt_loop2 := 0;
				while (distance(pos_actuelle,pos_cible) > 0) and (v_pa >= getparm_n(9)) loop
					compt_loop2 := compt_loop2 + 1;
					exit when compt_loop2 >= 6;
					-- on récupère la case vers laquelle on se déplace
					pos_dest := dep_vers_cible(pos_actuelle,pos_cible);
					-- on va sur cette nouvelle case
					temp_txt := deplace_code(v_monstre,pos_dest);
					-- on récupère les nouvelles infos
					select into v_pa,pos_actuelle
						perso_pa,ppos_pos_cod
						from perso,perso_position
						where perso_cod = v_monstre
						and ppos_perso_cod = perso_cod;
				end loop;
		end if;
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
						v_pv,
						v_pv_max,
						statique_combat,
						statique_hors_combat,
						v_dlt,
						v_temps_tour
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
					perso_pv,
					perso_pv_max,
					perso_sta_combat,
					perso_sta_hors_combat,
					perso_dlt,
					perso_temps_tour
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;
	end loop;

/*************************************************/
/* Etape 5 : tout semble fini                    */
/*************************************************/
	return code_retour;
end;
$_$;


ALTER FUNCTION public.ia_golem_brouzoufs(integer) OWNER TO delain;
