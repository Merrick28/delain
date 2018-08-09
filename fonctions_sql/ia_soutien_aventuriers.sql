CREATE OR REPLACE FUNCTION public.ia_soutien_aventuriers(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* fonction ia_soutien_aventuriers                              */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*des monstres ou PNJ qui soutiennent les aventuriers*/
/*****************************************************/
/* 29/10/2012 : Création                             */
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
	v_int integer;					-- intelligence du monstre
	num_sort integer;				-- numéro du sort à lancer
	fonction_sort text;			-- fonction à lancer
	v_pv integer;					-- pv du monstre
	v_pv_max integer;				-- pv_max du monstre
	soigneur boolean;	   -- est-il soigneur exclusivement ?
	statique_combat text;		-- statique en combat ?
	statique_hors_combat text;	-- statique hors combat ?
	doit_jouer integer;			-- 0 pour non, 1 pour oui
	v_dlt timestamp;				-- dlt du monstre
	v_temps_tour integer;		-- temps du tour
	i_temps_tour interval;		-- temps du tour en intervalle
	temp_niveau integer;			-- random pour passage niveau
	v_portee_max integer;		-- Si la vue est trop grande, on majore.
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
	pos_cible integer;			-- position de la cible
	pos_dest integer;				-- destination

begin
	compt_loop := 0;
	doit_jouer := 0;
	code_retour := E'IA soutien\
Monstre '||trim(to_char(v_monstre,'999999999999'))||E'\
';
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
	i_temps_tour := trim(to_char(v_temps_tour,'9999999'))||' minutes';
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
/* passage de niveau si possible   */
/***********************************/
-- on lance la procédure de passage de niveau
	if (v_exp >= v_niveau and v_pa >= getparm_n(8)) then
		temp_niveau := lancer_des(1,6);
		temp_txt := f_passe_niveau(v_monstre,temp_niveau);
		select into v_pa perso_pa from perso where perso_cod = v_monstre;
		code_retour := code_retour||E'Passage niveau.\
';
	end if;
	
/************************************/
/* On regarde si il y a             */
/*  des joueurs en vue              */
/************************************/

	select into soigneur
		count(*) = 0
	from perso_sorts
	inner join sorts on sort_cod = psort_sort_cod
	where psort_perso_cod = v_monstre
		and (sort_cod not in (5, 10, 129));

	select into nb_joueur_en_vue count(perso_cod)
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	inner join positions on pos_cod = ppos_pos_cod
	where pos_x between (v_x - v_vue) and (v_x + v_vue)
		and pos_y between (v_y - v_vue) and (v_y + v_vue)
		and pos_etage = v_etage
		and perso_type_perso = 1
		and perso_actif = 'O'
		and perso_tangible = 'O'
		-- pour un perso soigneur, seuls les perso blessés sont comptés
		and (not soigneur OR perso_pv * 100 / perso_pv_max <= 75)
		and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = ppos_pos_cod
				and lpos_lieu_cod = lieu_cod
				and lieu_refuge = 'O')
		and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1;

	code_retour := code_retour||'Nombre de persos en vue : '||trim(to_char(nb_joueur_en_vue,'9999999999'))||E'\
';

	/* si personne, on sort..... ==> le monstre bouge */
	if nb_joueur_en_vue = 0 then
		update perso set perso_cible = null where perso_cod = v_monstre;
		if statique_hors_combat = 'N' then
			compt_loop := 0;
			while (v_pa >= getparm_n(9)) loop
				compt_loop := compt_loop + 1;
				exit when compt_loop >= 10;
				dep_aleatoire := f_deplace_aleatoire(v_monstre,pos_actuelle);

				select into pos_actuelle, v_x, v_y ppos_pos_cod, pos_x, pos_y
				from perso_position
				inner join positions on pos_cod = ppos_pos_cod
				where ppos_perso_cod = v_monstre;

				select into v_pa perso_pa from perso
				where perso_cod = v_monstre;
			end loop;
		end if;
		code_retour := code_retour||'Aucun joueur en vue, déplacement aléatoire - boucle arrêtée a '||trim(to_char(compt_loop,'9999999'))||E'\
';
		return code_retour;
	end if;

	/* Le monstre en a marre de s’occuper des mêmes personnes, il va voir ailleurs s’il y est*/
	if random() < 0.2 then
		update perso set perso_cible = null where perso_cod = v_monstre;

		code_retour := code_retour||'Changement d’air !\
';

		-- On choisit une cible le plus loin possible
		select into pos_cible
			pos_cod
		from positions 
		where pos_x between (v_x - v_vue) and (v_x + v_vue)
			and pos_y between (v_y - v_vue) and (v_y + v_vue)
			and pos_etage = v_etage
			and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
		order by max(abs(pos_x - v_x), abs(pos_y - v_y)) desc, random()
		limit 1;

		if statique_hors_combat = 'N' then
			compt_loop := 0;
			while (v_pa >= getparm_n(9)) loop
				compt_loop := compt_loop + 1;
				exit when compt_loop >= 10;
				-- on récupère la case vers laquelle on se déplace
				-- on va sur cette nouvelle case
				-- on récupère les nouvelles infos
				pos_dest := dep_vers_cible(pos_actuelle, pos_cible);
				temp_txt := deplace_code(v_monstre, pos_dest);

				select into pos_actuelle, v_x, v_y ppos_pos_cod, pos_x, pos_y
				from perso_position
				inner join positions on pos_cod = ppos_pos_cod
				where ppos_perso_cod = v_monstre;

				select into v_pa perso_pa from perso
				where perso_cod = v_monstre;
			end loop;
		end if;
		code_retour := code_retour||'Aucun joueur en vue, déplacement aléatoire - boucle arrêtée a '||trim(to_char(compt_loop,'9999999'))||E'\
';
		return code_retour;
	end if;

	/*************************************************************/
	/* 1 Boucle sur les PA                                       */
	/* 2 Sort de soutien                                         */
	/*    Détermination de la distance maximale d’action         */
	/*    Choix d’une cible aléatoire dans ce rayon              */
	/*    Si aucune cible : on bouge vers le perso le plus près  */
	/*    Choix d’un sort aléatoire à lancer sur cette cible     */
	/*    Si aucun sort : on se rapproche de la cible            */
	/*    Si aucun sort : on se rapproche de la cible            */
	/* 3 Fin de boucle si plus de PA, sinon boucle fois 12       */
	/*************************************************************/
	/* Début de boucle */
	compt_loop := 0;

	while (v_pa >= 3) loop
		compt_loop := compt_loop + 1;
		exit when compt_loop >= 12;

		/* on tente de lancer un sort de soutien */

		-- Calcul de la distance limite max par rapport aux sorts de soutien
		-- Attention : ne prend pas en compte les bonus / malus aux PA, en déplacement ou magie...
		-- => améliorable.
		-- initialisation de la variable
		distance_limite := 0;
		select into distance_limite
			min(max(sort_distance + ((v_pa - sort_cout) / getparm_n(9))), v_vue)
		from perso_sorts
		inner join sorts on sort_cod = psort_sort_cod
		where psort_perso_cod = v_monstre
			and sort_soutien = 'O';

		-- On choisit une cible
		/* On sélectionne d’abord une cible qui est à distance de sort */
		select into v_cible, distance_cible, v_pv, pos_cible
			perso_cod, max(abs(pos_x - v_x), abs(pos_y - v_y)), perso_pv * 100 / perso_pv_max, ppos_pos_cod
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		where pos_x between (v_x - distance_limite) and (v_x + distance_limite)
			and pos_y between (v_y - distance_limite) and (v_y + distance_limite)
			and pos_etage = v_etage
			and perso_type_perso = 1
			and perso_actif = 'O'
			and perso_tangible = 'O'
			and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
			and (not soigneur OR perso_pv * 100 / perso_pv_max <= 75)
			and not exists
				(select 1 from lieu
				inner join lieu_position on lpos_pos_cod = pos_cod
				where lpos_pos_cod = ppos_pos_cod
					and lieu_refuge = 'O')
		order by random()
		limit 1;

		if not found then	-- Pas de cibles à distance : on se rapproche d’une cible aléatoire
			select into v_cible, distance_cible, v_pv, pos_cible
				perso_cod, max(abs(pos_x - v_x), abs(pos_y - v_y)), perso_pv * 100 / perso_pv_max, ppos_pos_cod
			from perso
			inner join perso_position on ppos_perso_cod = perso_cod
			inner join positions on pos_cod = ppos_pos_cod
			where pos_x between (v_x - v_vue) and (v_x + v_vue)
				and pos_y between (v_y - v_vue) and (v_y + v_vue)
				and pos_etage = v_etage
				and perso_type_perso = 1
				and perso_actif = 'O'
				and perso_tangible = 'O'
				and trajectoire_vue_murs(pos_actuelle, pos_cod) = 1
				and not exists
					(select 1 from lieu
					inner join lieu_position on lpos_pos_cod = pos_cod
					where lpos_pos_cod = ppos_pos_cod
						and lieu_refuge = 'O')
			order by max(abs(pos_x - v_x), abs(pos_y - v_y))
			limit 1;
			code_retour := code_retour||'Pas de cible à distance pour soutien. Déplacement...\
';
		end if;

		code_retour := code_retour || 'Cible localisée : ' || v_cible::text || ' à distance ' || distance_cible::text || '\
';

		-- Choix du sort à lancer
		select into num_sort, fonction_sort psort_cod, sort_fonction
		from perso_sorts
		inner join sorts on sort_cod = psort_sort_cod
		where psort_perso_cod = v_monstre
			and sort_soutien = 'O'
			and (sort_cod not in (5, 10, 129) or v_pv <= 75)	-- Si la cible n’est pas blessée on exclut les soins
			and sort_distance >= distance_cible
			and sort_cout <= v_pa
		order by random()
		limit 1;

		if found then	-- on a un sort, on a une cible
			code_retour := code_retour||E'Lancement de sort de soutien.\
';
			update perso set perso_cible = v_cible where perso_cod = v_monstre;

			-- on construit la chaine de lancement du sort
			-- puis on lance le sort proprement dit
			fonction_sort := 'select nv_' || fonction_sort || '(' || trim(to_char(v_monstre, '9999999999')) || ', ' || trim(to_char(v_cible, '9999999999')) || ', 1)';
			execute fonction_sort;
			code_retour := code_retour||'Lancement de ' || fonction_sort ||E'.\
';
		else	-- déplacement...
			if statique_combat = 'N' then
				-- on récupère la case vers laquelle on se déplace
				-- on va sur cette nouvelle case
				-- on récupère les nouvelles infos
				pos_dest := dep_vers_cible(pos_actuelle, pos_cible);
				temp_txt := deplace_code(v_monstre, pos_dest);
			end if;
		end if;

		-- On finit la boucle, en relançant la procédure, si PA suffisants
		select into v_pa, pos_actuelle, v_x, v_y
			perso_pa, ppos_pos_cod, pos_x, pos_y
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		where perso_cod = v_monstre;

	end loop;
	return code_retour;
end;$function$

