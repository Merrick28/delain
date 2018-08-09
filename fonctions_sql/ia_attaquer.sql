CREATE OR REPLACE FUNCTION public.ia_attaquer(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* fonction ia_attaquer                                */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 12/08/2005 : création                             */
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

begin
	doit_jouer := 0;
	code_retour := 'IA Attaquer cible <br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';
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
					perso_int,
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
	i_temps_tour := trim(to_char(v_temps_tour,'99999999999999'))||' minutes';
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
/**********************************************/
/* Etape 3 : on se met à portée de la cible  */
/**********************************************/
   select into v_cible pia_parametre from perso_ia where pia_perso_cod = v_monstre;
   select into pos_cible ppos_pos_cod from perso_position where ppos_perso_cod = v_cible;
	-- Si on n'est pas à portée on se déplace à portée   	
	if (distance(pos_actuelle,pos_cible) > distance_limite) then
		code_retour := 'Deplacement vers cible<br>';
		code_retour := code_retour||ia_deplacement(v_monstre,pos_cible);
	end if;
/*****************************************/
/* Etape 6 : on regarde si on est sur la */	
/*  même case que la cible               */
/*****************************************/
	if (distance(pos_actuelle,pos_cible) <= distance_limite) then
		-- Si des locks de combat autres que la cible on se désengage
		select into temp_cible count(lock_cod) from lock_combat
			where lock_cible = v_monstre;
		if temp_cible != 0 then
			if v_cible not in 
				(select lock_attaquant from lock_combat where lock_cible = v_monstre
					union
					select lock_cible from lock_combat where lock_attaquant = v_monstre) then
				code_retour := code_retour||'Monstre locké par d''autres que la cible désignée.<br>';
				select into temp_cible count(distinct(perso_cod))
					from perso,lock_combat
					where (lock_cible = v_monstre and lock_attaquant = perso_cod)
					or (lock_cible = perso_cod and lock_attaquant = v_monstre);
				-- On essaie de se désengager des autres attaquants
				compt_loop := 0;
				while ((temp_cible > 0) and (v_pa >= 2)) loop
					compt_loop := compt_loop + 1;
					exit when compt_loop >= 15;
					select into temp_cible
						distinct perso_cod
						from perso,lock_combat
						where (lock_cible = v_monstre and lock_attaquant = perso_cod)
						or (lock_cible = perso_cod and lock_attaquant = v_monstre)
						limit 1;		
					temp_txt := desengagement(v_monstre, temp_cible);	
					code_retour := code_retour||'Tentative de désengagement.<br>';	
					select into v_pa perso_pa from perso where perso_cod = v_monstre;
					select into temp_cible count(distinct(perso_cod))
						from perso,lock_combat
						where (lock_cible = v_monstre and lock_attaquant = perso_cod)
						or (lock_cible = perso_cod and lock_attaquant = v_monstre);
				end loop;
			end if;
		end if;	
	
	
	
	
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
			fonction_sort := 'select nv_'||fonction_sort||'('||trim(to_char(v_monstre,'99999999999'))||','||trim(to_char(v_cible,'99999999999'))||',1)';
-- on lance le sort proprement dit
			execute fonction_sort;
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
                        --code_retour := code_retour||attaque(v_monstre,v_cible,0);
			--on récupère les infos pour voir si on boucle encore
			select into v_pa perso_pa from perso where perso_cod = v_monstre;
			select into pos_cible ppos_pos_cod from perso_position
				where ppos_perso_cod = v_cible;
		end loop;
		code_retour := code_retour||'Attaque cible.<br>';
	end if;   
   
   
/*************************************************/
/* Etape 8 : tout semble fini                    */
/*************************************************/
        code_retour := code_retour||'FIN OK.<br>';
	return code_retour;
end;
$function$

