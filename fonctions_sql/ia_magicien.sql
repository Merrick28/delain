
--
-- Name: ia_magicien(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION ia_magicien(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction ia_magicien                              */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres magiciens purs                      */
/*****************************************************/
/* 22/04/2006 : Création                             */
/* 15/11/2012 : Reivax : ramassage de runes, début de*/
/*  factorisation de code, utilisation des réceptacles*/
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
	v_etage integer;				-- etage
	pos_actuelle integer;		-- position
	v_x integer;					-- X
	v_y integer;					-- Y
	actif varchar(2);				-- actif ?
	distance_limite integer;	-- distance max des sorts connus
	nb_lock integer;				-- nombre de locks combat
	v_int integer;					-- intelligence du monstre
	nb_sort_aggressif integer;	-- nombre de sorts aggressifs du monstre
	nb_sort_soutien integer;	-- nombre de sorts soutien du monstre
	chance_sort integer;			-- chance de lancer un sort
	num_sort integer;				-- numéro du sort à lancer
	fonction_sort text;			-- fonction à lancer
	v_pv integer;					-- pv du monstre
	v_pv_max integer;				-- pv_max du monstre
	v_soutien text;				-- peut il soutenir d’autres monstres ?
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
	v_portee_max integer;		-- Si la vue est trop grande, on majore.
	nb_receptacles_vides integer;	-- Nombre de réceptacles vides
	v_gmon_nom text;     -- nom du mosntre generique pour débuggage
-------------------------------------------------------
-- variables temporaires ou de calcul
-------------------------------------------------------
	temp integer;					-- fourre tout
	temp_txt text;					-- texte temporaire
	compt_loop integer;			-- comptage de boucle pour sortie
	compt_loop2 integer;			-- comptage de boucle pour sortie
	dep_aleatoire integer;		-- variable de calcul de dep aleatoire
	distance_cible integer;		-- distance de la cible
	des Integer;                    -- Lancer de dés 100
	des2 Integer;                    -- Lancer de dés 100
-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
	nb_joueur_en_vue integer;	-- nombre de joueurs en vue
	nb_monstre_a_distance integer;  -- nombre de monstres en vue
	nb_cible_en_vue integer;	-- nombre de cibles en vue
	pos_cible integer;			-- position de la cible
	pos_dest integer;				-- destination
	pos_rune integer;		-- position de la rune à ramasser
	cod_rune integer;		-- code de la rune à ramasser
	v_seuil_cible_monstre integer;

	donnees ia_donnees; -- les données d'IA, et le retour des fonctions incluses

begin
	compt_loop := 0;
	doit_jouer := 0;
	code_retour := E'IA magicien\nMonstre ' || trim(to_char(v_monstre, '999999999999')) || E'\n';
	v_seuil_cible_monstre := getparm_n(124);
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
			v_soutien,
			v_gmon_nom
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
		gmon_soutien,
		gmon_nom
	from perso, perso_position, positions,monstre_generique
	where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod
		and perso_gmon_cod = gmon_cod;

  /* Ajout d'info sur le monstre pour débuggage */
  code_retour := code_retour||v_gmon_nom||': actif='||actif||', Soutien='||v_soutien||E'\n';

	if actif != 'O' then
		return 'inactif !';
	end if;
	i_temps_tour := trim(to_char(v_temps_tour, '9999999')) || ' minutes';
	if v_dlt + i_temps_tour - '10 minutes'::interval >= now() then
		doit_jouer := 1;
		code_retour := code_retour||E'DLT dans 10 minutes, le perso doit jouer.\n';
	end if;
	temp := lancer_des(1, 100);
	if temp > 50 then
		if doit_jouer = 0 then
			code_retour := code_retour || 'Perso non joué.';
			return code_retour;
		end if;
	end if;


/***********************************/
/* passage de niveau si possible   */
/***********************************/
-- on lance la procédure de passage de niveau
	if (v_exp >= v_niveau and v_pa >= getparm_n(8)) then
		temp_niveau := lancer_des(1, 6);
		temp_txt := f_passe_niveau(v_monstre, temp_niveau);
		select into v_pa perso_pa from perso where perso_cod = v_monstre;
		code_retour := code_retour || E'Passage niveau.\n';
	end if;

	/* on se soigne */
	if v_pv * 100 / v_pv_max <= 75 then
		select into temp_txt sort_fonction
		from perso_sorts
		inner join sorts on sort_cod = psort_sort_cod
		where psort_perso_cod = v_monstre
			and psort_sort_cod IN (5, 129, 10)
			and sort_cout <= v_pa
		order by random()
		limit 1;
		if found then
			chance_mercu := round((v_pv_max - v_pv) / v_pv_max);
			if lancer_des(1, 100) > chance_mercu then
				fonction_sort := 'select nv_' || temp_txt || '(' || trim(to_char(v_monstre, '9999999999')) || ', ' || trim(to_char(v_monstre, '9999999999')) || ', 1)';
				execute fonction_sort;
				code_retour := code_retour || E'Lancement sort de soin sur lui-même : ' || temp_txt || '.\n';
				select into v_pa perso_pa from perso where perso_cod = v_monstre;
			end if;
		end if;
	end if;
/************************************/
/* On regarde si il y a             */
/*  des joueurs en vue              */
/************************************/
	select into nb_joueur_en_vue count(perso_cod)
	from perso, perso_position, positions
	where pos_x between (v_x - v_vue) and (v_x + v_vue)
		and pos_y between (v_y - v_vue) and (v_y + v_vue)
		and pos_etage = v_etage
		and ppos_perso_cod = perso_cod
		and ( perso_type_perso in (1, 3)
			OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
		and perso_actif = 'O'
		and perso_tangible = 'O'
		and perso_cod <> v_monstre
		and ppos_pos_cod = pos_cod
		and not exists
			(select 1 from lieu, lieu_position
			where lpos_pos_cod = ppos_pos_cod
				and lpos_lieu_cod = lieu_cod
				and lieu_refuge = 'O')
		and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1;
	code_retour := code_retour || 'Nombre de persos en vue : ' || trim(to_char(nb_joueur_en_vue, '9999999999')) || E'\n';

/* si personne, on sort..... ==> le monstre bouge */
-- modif Reivax 15/11/2012 : on remplit ses réceptacles, puis on regarde s’il n’y a pas des runes à ramasser, avant de bouger au hasard.
	if nb_joueur_en_vue = 0 then
		update perso set perso_cible = null where perso_cod = v_monstre;
		code_retour := code_retour || 'Aucun joueur en vue...<br />\n';

		-- remplissage d’un réceptacle
		select into nb_receptacles_vides perso_nb_receptacle - coalesce(rec_nombre, 0)
		from perso
		left outer join (
			select recsort_perso_cod, count(*) as rec_nombre
			from recsort
			where recsort_perso_cod = v_monstre
			group by recsort_perso_cod
		) rec on recsort_perso_cod = perso_cod
		where perso_cod = v_monstre;

		-- si on a des réceptacles vides, on met un sort au pif en réceptacle
		if nb_receptacles_vides > 0 then
			select into num_sort sort_cod
			from perso_sorts
			inner join sorts on sort_cod = psort_sort_cod
			where psort_perso_cod = v_monstre
				and sort_cout <= v_pa
			order by random()
			limit 1;
			-- on construit la chaine de lancement du sort et on lance le sort
			if found then
				temp_txt := cree_receptacle(v_monstre, num_sort, 1);
				code_retour := code_retour || 'On remplit un réceptacle...<br />\n';
			end if;
		end if;

		if statique_hors_combat = 'N' then
			-- TODO : transporter l’objet DONNEES dans toute la procédure et non juste à cet emplacement.
			-- Permettra, comme ici, de faciliter l’utilisation de fonctions modulaires sans recharger toujours les mêmes données.
			donnees.monstre_cod := v_monstre;
			donnees.pos_cod := pos_actuelle;
			donnees.pos_x := v_x;
			donnees.pos_y := v_y;
			donnees.pos_etage := v_etage;
			donnees.perso_pa := v_pa;
			donnees.code_retour := '';

			-- on lance le ramassage de runes.
			-- v_tobj_cod = 5 (runes) ;
			-- inventaire_max = 6 (runes max en inventaire) ;
			-- nb_ramassage = 2 (nombre de runes max à ramasser à chaque DLT)
			donnees := ia_include_ramasse_objet(donnees, v_vue, 5, 6, 2);

			-- avec les PAs qui reste, on se déplace
			donnees := ia_include_deplacement(donnees, 2, 6, NULL, 0);

			-- TODO : À supprimer à terme : on remet à jour les variables de la procédure
			pos_actuelle := donnees.pos_cod;
			v_x := donnees.pos_x;
			v_y := donnees.pos_y;
			v_etage := donnees.pos_etage;
			v_pa := donnees.perso_pa;
			code_retour := code_retour || donnees.code_retour;
		end if;
		return code_retour;
	end if;
/* sinon, on reste....*/
	if v_cible is null then
		code_retour := code_retour || E'Pas de cible offensive de départ.\n';
	else
		code_retour := code_retour || 'Cible offensive départ : ' || trim(to_char(v_cible, '9999999999999')) || E'\n';
	end if;

/*************************************************************/
/* 1 Boucle sur les PA                                       */
/* 2 Sort Offensif                                           */
/*    2.1 Sort Offensif										 */
/*          2.1.1 Calcul de la distance limite				 */
/*          2.1.2 Choix de la cible (on garde 50% sur cible  */
/*				initiale si toujours présente et à distance, */
/*				 sinon nouvelle cible)						 */
/*          2.1.3 On se déplace si pas à distance			 */
/*          2.1.4 Si à distance on lance le sort			 */
/*   2.2 Sort de soutien									 */
/*      Choix de la cible, idem actuel (pas de préférence)	 */
/* 3 Fin de boucle si plus de PA, sinon boucle fois 12		 */
/*************************************************************/
/* Début de boucle */
	compt_loop := 0;
	while (v_pa >= 2) loop
		compt_loop := compt_loop + 1;
		exit when compt_loop >= 12;

		/* on tente de lancer un sort offensif avec une base 50 entre sorts offensifs et de soutien */
		des := lancer_des(1, 100);
		chance_sort := 50;
		select into nb_sort_aggressif count(psort_cod)
		from perso_sorts, sorts
		where psort_perso_cod = v_monstre
			and psort_sort_cod = sort_cod
			and sort_aggressif = 'O';
		if (des <= chance_sort) and nb_sort_aggressif != 0 then
			code_retour := code_retour || E'Lancement de sort offensif.\n';
			/* Calcul de la distance limite max par rapport aux sorts offensifs */
			/* initialisation de la variable */
			distance_limite := 0;
			select into distance_limite max(sort_distance)
			from perso_sorts, sorts
			where psort_perso_cod = v_monstre
				and sort_aggressif = 'O'
				and psort_sort_cod = sort_cod;
			/* Choix de la cible avec 50% de chance de garder l’initial pour un sort offensif*/
			des2 = lancer_des(1, 100);
			if (des2 <= 50) then
				select into nb_cible_en_vue count(perso_cod)
				from perso_position, perso, positions
				where ppos_perso_cod = v_cible
					and pos_x between (v_x - distance_limite) and (v_x + distance_limite)
					and pos_y between (v_y - distance_limite) and (v_y + distance_limite)
					and pos_etage = v_etage
					and perso_cod = v_cible
					and perso_actif = 'O'
					and perso_tangible = 'O'
					and ( perso_type_perso != 2
						OR perso_monstre_attaque_monstre >= v_seuil_cible_monstre)
					and perso_cod <> v_monstre
					and not exists
						(select 1 from lieu, lieu_position
						where lpos_pos_cod = ppos_pos_cod
							and lpos_lieu_cod = lieu_cod
							and lieu_refuge = 'O')
					and ppos_pos_cod = pos_cod
					and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1;
	/* si cible initiale pas à distance ou pas de cible, on en choisit une autre */
				if nb_cible_en_vue = 0 then
					code_retour := code_retour || E'Pas de cible initiale ou la cible choisie n’est pas à portée.\n';
	/*Choix d’une cible*/
					v_cible := choix_cible(v_monstre, pos_actuelle, v_etage, nb_joueur_en_vue, v_vue);
					code_retour := code_retour || 'Nouvelle cible : ' || trim(to_char(v_cible, '9999999999999')) || E'\n';
				end if;
			/* Sinon on conserve la cible initiale et dans 50% des cas, changement de cible*/
			else
				v_cible := choix_cible(v_monstre, pos_actuelle, v_etage, nb_joueur_en_vue, v_vue);
				code_retour := code_retour || 'Nouvelle cible : ' || trim(to_char(v_cible, '9999999999999')) || E'\n';
			end if;
	/******************************************************/
	/* On récupère la position de la nouvelle cible cible */
	/******************************************************/
			select into pos_cible ppos_pos_cod from perso_position
			where ppos_perso_cod = v_cible;
	/****************************************************************/
	/* On se déplace vers la cible si celle ci n’est pas à distance */
	/****************************************************************/
			if (distance(pos_actuelle, pos_cible) > distance_limite) then
				code_retour := code_retour || E'Déplacement vers la cible.\n';
				if statique_combat = 'N' then
					pos_actuelle := ia_include_deplacement_vers_portee(v_monstre, pos_actuelle, pos_cible, distance_limite);
					select into v_pa perso_pa from perso where perso_cod = v_monstre;
				end if;
			end if;
	/*****************************************/
	/* Etape 6 : on regarde si on est à      */
	/* distance de la cible                  */
	/*****************************************/
			if (distance(pos_actuelle, pos_cible) <= distance_limite) then
				/* on regarde si on peut lancer un sort aggressif ==> A priori étape qui n’est plus nécessaire */
				distance_cible := distance(pos_actuelle, pos_cible);
				select into nb_sort_aggressif count(psort_cod)
				from perso_sorts, sorts
				where psort_perso_cod = v_monstre
					and psort_sort_cod = sort_cod
					and sort_aggressif = 'O'
					and sort_distance >= distance_cible;
				if nb_sort_aggressif != 0 then
					code_retour := code_retour || E'Lancement de sort offensif.\n';
		/* on commence par choisir le sort qu’on va lancer */
					temp := lancer_des(1, nb_sort_aggressif);
					temp := temp - 1;
					select into num_sort, fonction_sort
						sort_cod, sort_fonction
					from perso_sorts, sorts
					where psort_perso_cod = v_monstre
						and psort_sort_cod = sort_cod
						and sort_aggressif = 'O'
						and sort_distance >= distance_cible
					offset temp
					limit 1;

					-- on regarde si on n’aurait pas ce sort en réceptacle
					-- et on construit la chaine de lancement du sort
					fonction_sort := 'select nv_' || fonction_sort || '(' || trim(to_char(v_monstre, '9999999999')) || ', ' || trim(to_char(v_cible, '9999999999'));
					select into temp recsort_sort_cod from recsort
					where recsort_perso_cod = v_monstre
						and recsort_sort_cod = num_sort
					limit 1;
					if found then
						fonction_sort := fonction_sort || ', 2)';
					else
						fonction_sort := fonction_sort || ', 1)';
					end if;
					/* on lance le sort proprement dit */
					execute fonction_sort;
				end if;
			end if;
		/*Fin de lancement d’un sort offensif */
		else
		/* Lancement d’un sort de soutien à 50% de chances avec un sort offensif */
			code_retour := code_retour || E'Lancement de sort de soutien.\n';
	/*on choisit la cible du sort de soutien ==> Si il s’agit d’un monstre de soutien, */
	/* il va lancer un sort sur un autre monstre, sinon sur lui même                   */
			select into v_soutien gmon_soutien
			from monstre_generique, perso
			where perso_cod = v_monstre
				and perso_gmon_cod = gmon_cod;
			if not found then
				cible_soutien := v_monstre;
				code_retour := code_retour || E'v_soutien pas trouvé\n';
			else
				if v_soutien = 'N' then
					cible_soutien := v_monstre;
					code_retour := code_retour || E'pas un monstre de soutien\n';
				else
				  code_retour := code_retour || E'Faire du soutien aux autres monstres\n';
					/* On est dans le cas d’un soutien à un autre monstre, initialisation de variable */
					distance_limite := 0;
					select into distance_limite max(sort_distance)
					from perso_sorts, sorts
					where psort_perso_cod = v_monstre
						and psort_sort_cod = sort_cod
						and sort_soutien = 'O';
					/* On sélectionne d’abord une cible qui est à distance de sort */
					--- Marlysa 2018-05-28: perso_monstre_attaque_monstre peux être NULL => COALESCE
					select into nb_monstre_a_distance count(perso_cod)
					from perso, perso_position, positions
					where pos_x between (v_x - distance_limite) and (v_x + distance_limite)
						and pos_y between (v_y - distance_limite) and (v_y + distance_limite)
						and pos_etage = v_etage
						and ppos_perso_cod = perso_cod
						and perso_type_perso = 2
						and COALESCE(perso_monstre_attaque_monstre,0) < v_seuil_cible_monstre
						and perso_actif = 'O'
						and perso_tangible = 'O'
						and ppos_pos_cod = pos_cod
						and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1;
					code_retour := code_retour || 'Nombre de cibles à distance (' || distance_limite::text || ') pour soutien: ' || trim(to_char(nb_monstre_a_distance, '9999999999')) || E'\n';

					/* Si pas de monstre à distance sélection d’un nouveau monstre cible */
					if nb_monstre_a_distance = 0 then
					/*Choix d’une nouvelle cible en vue, en tant que monstre */
						v_portee_max := 4;
						if v_vue < 4 then
							v_portee_max := v_vue;
						end if;
						select into cible_soutien, pos_cible perso_cod, ppos_pos_cod
						from perso, perso_position, positions
						where pos_x >= (v_x - v_portee_max) and pos_x <= (v_x + v_portee_max)
							and pos_y >= (v_y - v_portee_max) and pos_y <= (v_y + v_portee_max)
							and ppos_perso_cod = perso_cod
							and pos_etage = v_etage
							and ppos_pos_cod = pos_cod
							and perso_type_perso = 2
							and COALESCE(perso_monstre_attaque_monstre,0) < v_seuil_cible_monstre
							and perso_actif = 'O'
							and perso_tangible = 'O'
							and not exists
								(select 1 from lieu, lieu_position
								where lpos_pos_cod = ppos_pos_cod
									and lpos_lieu_cod = lieu_cod
									and lieu_refuge = 'O')
							and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
						limit 1
						offset nb_cible_en_vue;
						update perso set perso_cible = cible_soutien where perso_cod = v_monstre;
						code_retour := code_retour || 'Nouvelle cible pour soutien : ' || trim(to_char(cible_soutien, '9999999999999')) || E'\n';

	/****************************************************************/
	/* On se déplace vers la cible si celle ci n’est pas à distance */
	/****************************************************************/
						code_retour := code_retour || E'Déplacement vers la cible.\n';
						if statique_combat = 'N' then
							pos_actuelle := ia_include_deplacement_vers_portee(v_monstre, pos_actuelle, pos_cible, distance_limite);
							select into v_pa perso_pa from perso where perso_cod = v_monstre;
						end if;
			/* Fin du déplacement vers la nouvelle cible */
					else
		/* On sélectionne le monstre à soutenir et on lance le sort */
						nb_monstre_a_distance := lancer_des(1, nb_monstre_a_distance);
						nb_monstre_a_distance := nb_monstre_a_distance - 1;
						select into cible_soutien perso_cod
						from perso, perso_position, positions
						where pos_x between (v_x - distance_limite) and (v_x + distance_limite)
							and pos_y between (v_y - distance_limite) and (v_y + distance_limite)
							and pos_etage = v_etage
							and ppos_perso_cod = perso_cod
							and perso_type_perso = 2
							and COALESCE(perso_monstre_attaque_monstre,0) < v_seuil_cible_monstre
							and perso_actif = 'O'
							and perso_tangible = 'O'
							and ppos_pos_cod = pos_cod
							and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
						limit 1
						offset nb_monstre_a_distance;
						code_retour := code_retour || 'Nouvelle cible2 de soutien : ' || trim(to_char(cible_soutien, '9999999999999')) || E'\n';
					/* Si vraiment pas d’autre monstre, il se soutient tout seul */
						if cible_soutien is null then
							cible_soutien := v_monstre;
							code_retour := code_retour || E'Pas d’autre monstre à soutenir.\n';
						end if;
					end if;
				end if;
			end if;
			select into pos_cible ppos_pos_cod from perso_position
			where ppos_perso_cod = cible_soutien;

			distance_cible := distance(pos_actuelle, pos_cible);
			select into nb_sort_soutien count(psort_cod)
			from perso_sorts, sorts
			where psort_perso_cod = v_monstre
				and psort_sort_cod = sort_cod
				and sort_soutien = 'O'
				and sort_distance >= distance_cible;
			code_retour := code_retour || 'Nombre de sorts de soutien: ' || trim(to_char(nb_sort_soutien, '9999999999')) || E'\n';
			if nb_sort_soutien != 0 then
				/* on commence par choisir le sort qu’on va lancer */
				temp := lancer_des(1, nb_sort_soutien);
				temp := temp - 1;
				select into num_sort, fonction_sort
					sort_cod, sort_fonction
				from perso_sorts, sorts
				where psort_perso_cod = v_monstre
					and psort_sort_cod = sort_cod
					and sort_soutien = 'O'
					and sort_distance >= distance_cible
				offset temp
				limit 1;

				-- on regarde si on n’aurait pas ce sort en réceptacle
				-- et on construit la chaine de lancement du sort
				fonction_sort := 'select nv_' || fonction_sort || '(' || trim(to_char(v_monstre, '9999999999')) || ', ' || trim(to_char(cible_soutien, '9999999999'));
				select into temp recsort_sort_cod from recsort
				where recsort_perso_cod = v_monstre
					and recsort_sort_cod = num_sort
				limit 1;
				if found then
					fonction_sort := fonction_sort || ', 2)';
				else
					fonction_sort := fonction_sort || ', 1)';
				end if;
				/* on lance le sort proprement dit */
				execute fonction_sort;
				code_retour := code_retour || 'Lancement de sort de soutien magicien (' || fonction_sort || ') sur ' || trim(to_char(cible_soutien, '9999999999999')) || E'.\n';
	/* Fin des sorts de soutien */
			end if;
		end if;
-- On finit la boucle, en relançant la procédure, si PA suffisants
		select into v_pa perso_pa from perso where perso_cod = v_monstre;
	end loop;
	return code_retour;
end;$_$;


ALTER FUNCTION public.ia_magicien(integer) OWNER TO delain;

--
-- Name: FUNCTION ia_magicien(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ia_magicien(integer) IS 'Gère l’IA pour les monstres magiciens (soutien ou agressifs, suivant les sorts qu’ils ont)';
