CREATE OR REPLACE FUNCTION public.ia_gregaire(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* fonction ia_gregaire                              */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
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
/* 17/07/2006 : rajout de l'utilisation des          */
/*              compétences de combat                */
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
	distance_limite integer;	-- distance max attaque (min vue-dist attaque)
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
	distance_cible integer;		-- distance de la cible
-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
	nb_joueur_en_vue integer;	-- nombre de joueurs en vue
	nb_cible_en_vue integer;	-- nombre de cibles en vue
	pos_cible integer;			-- position de la cible
	pos_dest integer;				-- destination
	ligne record;
	nv_pos integer;
	nb_cible_case integer;
	v_race integer;

begin
	doit_jouer := 0;
	code_retour := 'IA grégaire<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';
	limite_surcharge := 1;
/***********************************/
/* Etape 1 : on récupère les infos */
/* du monstre                      */
/***********************************/
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
						nv_pos,
						v_race
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
					pos_cod,
					perso_race_cod
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;
	nv_pos := 0;
	if v_cible is null then
		v_cible := 0;
	end if;
	if actif != 'O' then
		return 'inactif !';
	end if;
	i_temps_tour := trim(to_char(v_temps_tour,'9999999999999'))||' minutes';
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
/***************************************/
/* On sait qu'on doit jouer le monstre */
/***************************************/
	temp_txt := calcul_dlt2(v_monstre);
	distance_limite := portee_attaque(v_monstre);
	if distance_limite > v_vue then
		distance_limite := v_vue;
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
/************************************/
/* Etape 3 : on regarde si il y a   */
/*  des joueurs en vue              */
/************************************/
	select into nb_joueur_en_vue count(perso_cod)
		from perso,perso_position,positions
		where pos_x between (v_x - v_vue) and (v_x + v_vue)
		and pos_y between (v_y - v_vue) and (v_y + v_vue)
		and pos_etage = v_etage
		and ppos_perso_cod = perso_cod
		and perso_type_perso = 1
		and perso_actif = 'O'
		and perso_tangible = 'O'
		and ppos_pos_cod = pos_cod
		and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = ppos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')
		and is_surcharge(perso_cod,v_monstre) < limite_surcharge
		and trajectoire_vue(pos_actuelle,pos_cod) = 1;
		
	code_retour := code_retour||'Nombre de persos en vue : '||trim(to_char(nb_joueur_en_vue,'9999999999'))||'<br>';
	
/**************************************************/
/* Personne en vue, on se balade puis on sort     */
/**************************************************/
	if nb_joueur_en_vue = 0 then
		update perso set perso_cible = null where perso_cod = v_monstre;
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
		code_retour := code_retour||'Aucun joueur en vue, déplacement aléatoire.<br>';
		
		return code_retour;
	end if;
/*******************************/
/* On a des persos en vue      */
/*******************************/
-- on se soigne
	if v_pv != v_pv_max then
		select into temp psort_cod
			from perso_sorts
			where psort_perso_cod = v_monstre
			and psort_sort_cod = 5;
		if found then
			chance_mercu := round((v_pv_max - v_pv)/v_pv_max);
			if lancer_des(1,100) > chance_mercu then
				fonction_sort := 'select nv_magie_mercurochrome('||trim(to_char(v_monstre,'999999'))||','||trim(to_char(v_monstre,'999999'))||',1)';
				code_retour := code_retour||'Lancement mercurochrome.<br>';
				
				execute fonction_sort;
			end if;
		end if;
	end if;	
	if v_cible is null then
		code_retour := code_retour||'Pas de cible départ.<br>';
		
	else
		code_retour := code_retour||'Cible départ : '||trim(to_char(v_cible,'9999999999999'))||'<br>';
		
	end if;
/*************************************/
/* Etape 4 : on regarde si la cible  */
/*  est sur la même case             */
/*************************************/
-- on va commencer par regarder si on ne pourrait pas choisir plutôt une autre cible
	select into v_cible,temp perso_cible,count(*) as nb_cible
		from perso_position,perso,positions
		where perso_race_cod = v_race
		and perso_cod != v_monstre
		and perso_actif = 'O'
		and perso_tangible = 'O'
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_cod
		and pos_x between (v_x - distance_limite) and (v_x + distance_limite)
		and pos_y between (v_y - distance_limite) and (v_y + distance_limite)
		and pos_etage = v_etage
		and perso_cible is not null
		and trajectoire_vue(pos_actuelle,ppos_pos_cod) = 1
		group by perso_cible
		order by nb_cible desc 
		limit 1;
	if not found then
		code_retour := code_retour||'Pas de cible dégagée par rapport au nombre.<br>';	
		if(lancer_des(1,100) < 1) then
			v_cible := choix_cible(v_monstre,pos_actuelle,v_etage,nb_joueur_en_vue,distance_limite);
			code_retour := code_retour||'Cible choisie au hasard : '||trim(to_char(v_cible,'9999999999999'))||'<br>';	
		else
			v_cible := 0;
		end if;
	else			 
		code_retour := code_retour||'Nouvelle cible dégagée par rapport au nombre : '||trim(to_char(v_cible,'9999999999999'))||'<br>';	
		
	end if;
-- ensuite, est on locké par cette cible ?
	select into temp_cible count(lock_cod) from lock_combat
		where lock_cible = v_monstre;
	if temp_cible != 0 then
		if v_cible not in 
			(select lock_attaquant from lock_combat where lock_cible = v_monstre
				union
			select lock_cible from lock_combat where lock_attaquant = v_monstre) then
-- on est locké, mais pas par la bonne cible
-- on tente de se désengager
			code_retour := code_retour||'Tentative de désengagement car locké par mauvaise cible.<br>';
			
			temp := 0;
			while v_pa >= 2 loop
				temp := temp + 1;
				exit when temp > 10;
				for ligne in (select lock_attaquant as d_perso from lock_combat where lock_cible = v_monstre
					union
				select lock_cible as d_perso from lock_combat where lock_attaquant = v_monstre) loop
					temp_txt := desengagement(v_monstre,ligne.d_perso);
					select into v_pa perso_pa from perso where perso_cod = v_monstre;
				end loop;		
			end loop;
		end if;
	end if;
/*****************************************************/
/* A partir de maintenant, on a la bonne cible       */
/*****************************************************/
	select into	nb_cible_en_vue count(perso_cod)
		from perso_position,perso,positions
		where ppos_perso_cod = v_cible
		and pos_x between (v_x - distance_limite) and (v_x + distance_limite)
		and pos_y between (v_y - distance_limite) and (v_y + distance_limite)
		and pos_etage = v_etage
		and perso_cod = v_cible
		and perso_actif = 'O'
		and perso_tangible = 'O'
		and perso_type_perso in (1,3)
			and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = ppos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')
		and ppos_pos_cod = pos_cod
		and trajectoire_vue(pos_actuelle,pos_cod) = 1;
	if nb_cible_en_vue != 0 then
		if (trajectoire_vue(pos_actuelle,(select ppos_pos_cod from perso_position
			where ppos_perso_cod = v_cible))) != 1 then
		nb_cible_en_vue := 0;
		end if;
	end if;		
-- si cible pas sur la case, on en choisit une autre
	if nb_cible_en_vue = 0 then
/***************************************************/
/* Pas de bol, notre cible n'est pas visible       */
/***************************************************/	
	code_retour := code_retour||'La cible choisie n''est pas en vue.<br>';
	
		v_cible := 0;
		nb_cible_case := 99;
		for ligne in select pos_cod,
			(select count(perso_cod)
			from perso,perso_position
			where ppos_pos_cod = pos_cod
			and ppos_perso_cod = perso_cod
			and perso_type_perso in (1,3)
			and perso_actif = 'O'
			and perso_tangible = 'O') as nbperso
		from positions
		where pos_x >= (v_x - v_vue) and pos_x <= (v_x + v_vue)
		and pos_y >= (v_y - v_vue) and pos_y <= (v_y + v_vue)
		and pos_etage = v_etage
		and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')
		and trajectoire_vue(pos_actuelle,pos_cod) = 1 loop
				if ligne.nbperso != 0 then		
				if ligne.nbperso < nb_cible_case then
					nb_cible_case := ligne.nbperso;
					nv_pos := ligne.pos_cod;
				end if;
			end if;	
		end loop;
		code_retour := code_retour||'Nouvelle destination : '||trim(to_char(nv_pos,'9999999999999'))||'<br>';
		
	else
/***************************************************/
/* Notre cible est visible                         */
/***************************************************/	
	code_retour := code_retour||'La cible choisie est en vue.<br>';
	
		-- comportement normal
		select into nb_lock lock_cible
			from lock_combat
			where lock_cible = v_monstre;
		if found then
			-- on est locké, on prend la cible la plus faible
			v_cible := choix_cible_case_lache(v_monstre,pos_actuelle,v_etage,nb_joueur_en_vue);
		else
			select into nv_pos
				ppos_pos_cod
				from perso_position
				where ppos_perso_cod = v_cible;
		end if;
		code_retour := code_retour||'Nouvelle destination : '||trim(to_char(nv_pos,'9999999999999'))||'<br>';
		
	end if;
/***************************************/
/* On récupère la position de la cible */	
/***************************************/
	select into pos_cible ppos_pos_cod from perso_position
		where ppos_perso_cod = v_cible;
	select into pos_actuelle ppos_pos_cod from perso_position
		where ppos_perso_cod = v_monstre;
/*****************************************/
/* Etape 5 : on se déplace vers la cible */
/*****************************************/		
	if (distance(pos_actuelle,pos_cible) >= distance_limite) then
		code_retour := code_retour||'Déplacement vers la cible.<br>';
		
		if statique_combat = 'N' then
			compt_loop := 0;
			while (distance(pos_actuelle,pos_cible) >= distance_limite) and (v_pa >= getparm_n(9)) loop
				compt_loop := compt_loop + 1;
				exit when compt_loop >= 5;
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
	end if;

/*****************************************/
/* Etape 6 : on regarde si on est sur la */	
/*  même case que la cible               */
/*****************************************/
	if (distance(pos_actuelle,pos_cible) <= distance_limite) then
-- on regarde si on peut lancer un sort aggressif
	distance_cible := distance(pos_actuelle,pos_cible);
	select into nb_sort_aggressif count(psort_cod)
		from perso_sorts,sorts
		where psort_perso_cod = v_monstre
		and psort_sort_cod = sort_cod
		and sort_aggressif = 'O'
		and sort_distance >= distance_cible;
	if nb_sort_aggressif != 0 then
-- on tente le lancer de sort		
		chance_sort := v_int * 4;
		if chance_sort > 80 then
			chance_sort := 80;
		end if;
		if (lancer_des(1,100) <= chance_sort) then
		code_retour := code_retour||'Lancement de sort offensif.<br>';
		
-- là on est bien parti pour lancer un sort quand même
-- on commence par choisir le sort qu'on va lancer			
			temp := lancer_des(1,nb_sort_aggressif);
			temp := temp - 1;
			select into num_sort,fonction_sort
				sort_cod,sort_fonction
				from perso_sorts,sorts
				where psort_perso_cod = v_monstre
				and psort_sort_cod = sort_cod
				and sort_aggressif = 'O'
				and sort_distance >= distance_cible
				offset temp
				limit 1;
-- on construit la chaine de lancement du sort
			fonction_sort := 'select nv_'||fonction_sort||'('||trim(to_char(v_monstre,'999999'))||','||trim(to_char(v_cible,'999999'))||',1)';
-- on lance le sort proprement dit
			execute fonction_sort;
		end if;
	end if;
-- on regarde pour les sorts de soutien
	select into nb_lock lock_cible
		from lock_combat
		where lock_cible = v_monstre;
	if not found then
-- on choisit la cible du sort de soutien
		select into v_soutien gmon_soutien
			from monstre_generique,perso
			where perso_cod = v_monstre
			and perso_gmon_cod = gmon_cod;
		if not found then
			cible_soutien := v_monstre;
		else
			if v_soutien = 'N' then
				cible_soutien := v_monstre;
			else
				select into nb_joueur_en_vue count(perso_cod)
					from perso,perso_position,positions
					where pos_x between (v_x - v_vue) and (v_x + v_vue)
					and pos_y between (v_y - v_vue) and (v_y + v_vue)
					and pos_etage = v_etage
					and ppos_perso_cod = perso_cod
					and perso_type_perso = 2
					and perso_actif = 'O'
					and perso_tangible = 'O'
					and ppos_pos_cod = pos_cod;
				if nb_joueur_en_vue = 0 then
					cible_soutien := v_monstre;
				else
					nb_joueur_en_vue := lancer_des(1,nb_joueur_en_vue);
					nb_joueur_en_vue := nb_joueur_en_vue - 1;
					select into cible_soutien perso_cod
						from perso,perso_position,positions
						where pos_x between (v_x - v_vue) and (v_x + v_vue)
						and pos_y between (v_y - v_vue) and (v_y + v_vue)
						and pos_etage = v_etage
						and ppos_perso_cod = perso_cod
						and perso_type_perso = 2
						and perso_actif = 'O'
						and perso_tangible = 'O'
						and ppos_pos_cod = pos_cod
						limit 1
						offset nb_joueur_en_vue;
					if cible_soutien is null then
						cible_soutien := v_monstre;
					end if;
				end if;
			end if;
		end if;
		select into nb_sort_aggressif count(psort_cod)
			from perso_sorts,sorts
			where psort_perso_cod = v_monstre
			and psort_sort_cod = sort_cod
			and sort_soutien = 'O'
			and sort_distance >= distance_cible;
		if nb_sort_aggressif != 0 then
	-- on tente le lancer de sort		
			chance_sort := v_int * 3;
			if chance_sort > 80 then
				chance_sort := 80;
			end if;
			if (lancer_des(1,100) <= chance_sort) then
	-- là on est bien parti pour lancer un sort quand même
	-- on commence par choisir le sort qu'on va lancer			
				temp := lancer_des(1,nb_sort_aggressif);
				temp := temp - 1;
				select into num_sort,fonction_sort
					sort_cod,sort_fonction
					from perso_sorts,sorts
					where psort_perso_cod = v_monstre
					and psort_sort_cod = sort_cod
					and sort_soutien = 'O'
					and sort_distance >= distance_cible
					offset temp
					limit 1;
	-- on construit la chaine de lancement du sort
				fonction_sort := 'select nv_'||fonction_sort||'('||trim(to_char(v_monstre,'999999'))||','||trim(to_char(cible_soutien,'999999'))||',1)';
	-- on lance le sort proprement dit
				execute fonction_sort;
				code_retour := code_retour||'Lancement de sort de soutien sur '||trim(to_char(cible_soutien,'9999999999999'))||'.<br>';
				
			end if;
		end if;
	end if;
-- on attaque avec les compétences spéciales
	perform comp_spe_monstre(v_monstre,v_cible);
-- on attaque en boucle		
		compt_loop := 0;
		while (distance(pos_actuelle,pos_cible) <= distance_limite) and (v_pa >= nb_pa_attaque(v_monstre)) loop
			distance_attaque := distance(pos_actuelle,pos_cible);
			compt_loop := compt_loop + 1;
			exit when compt_loop >= 15;
			temp_txt := attaque(v_monstre,v_cible,0);
			--on récupère les infos pour voir si on boucle encore
			select into v_pa perso_pa from perso where perso_cod = v_monstre;
			select into pos_cible ppos_pos_cod from perso_position
				where ppos_perso_cod = v_cible;
				code_retour := code_retour||'Attaque cible '||trim(to_char(v_cible,'9999999'))||'<br>'||temp_txt||'<hr>';
				
		end loop;
		code_retour := code_retour||'Fin attaque cible '||trim(to_char(v_cible,'9999999'))||'.<br>';
		
	end if;
/*************************************************/
/* Etape 7 : on regarde si on peut se concentrer */
/*************************************************/
	
/*************************************************/
/* Etape 8 : tout semble fini                    */
/*************************************************/
	return code_retour;
end;
$function$

