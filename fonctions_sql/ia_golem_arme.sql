--
-- Name: ia_golem_arme(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION ia_golem_arme(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction ia_golem_arme				                     */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 06/04/2010 : 																		 */
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
	nb_tas_obj integer;	-- nombre de joueurs en vue
	nb_cible_en_vue integer;	-- nombre de cibles en vue
	pos_cible integer;			-- position de la cible
	pos_dest integer;				-- destination
	quantite integer;				--nombre de brouzoufs
	v_des integer;
	ligne record;
	position_arrivee integer;
	poids numeric;
	poids_max numeric;
	v_obj_enchantable int;

begin
	doit_jouer := 0;
	code_retour := 'IA golem armes et armures<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';
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
		code_retour := code_retour||'Passage niveau.<br>';
	end if;
/************************************/
/* Etape 3 : on regarde si il y a   */
/*  des armes et armures dans la vue*/
/************************************/
/* tobj_cod :
  1	"Arme"
  2	"Armure"
  4	"Casque"
  6	"Artefact"
  15	"Instrument de musique"
  21	"Potion"
  39	"Substance"
  40	"Gants"
  41	"Bottes"
 */
	select into nb_tas_obj count(obj_cod)
		from objet_position,positions,objets,objet_generique
		where pos_x between (v_x - v_vue) and (v_x + v_vue)
		and pos_y between (v_y - v_vue) and (v_y + v_vue)
		and pos_etage = v_etage
		and pobj_pos_cod = pos_cod
		and pobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod in (1,2,4,6,15,21,39,40,41)
		and trajectoire_vue_murs(pos_actuelle,pos_cod, false) = 1;      -- and trajectoire_vue(pos_actuelle,pos_cod) = 1; -- Marlysa, c'est mieux sans murs
	code_retour := code_retour||'Nombre objets en vue : '||trim(to_char(nb_tas_obj,'9999999999'))||'<br>';


-- si pas d'objets, on sort.....
	if nb_tas_obj = 0 then
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
		code_retour := code_retour||'Aucun objet en vue, déplacement aléatoire.<br>';
		return code_retour;
	end if;
-- sinon, on reste....
	if v_cible is null then
		code_retour := code_retour||'Pas de cible départ.<br>';
	else
		code_retour := code_retour||'Cible départ : '||trim(to_char(v_cible,'9999999999999'))||'. On va faire un déplacement aléatoire avant<br>';
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
        -- On va chercher quel est l'objet le plus proche ainsi que sa position
        select into nb_tas_obj,pos_cible,quantite,v_obj_enchantable pobj_obj_cod,pobj_pos_cod,gobj_poids,obj_enchantable
            from objet_position,positions,objets,objet_generique
            where pos_x between (v_x - v_vue) and (v_x + v_vue)
            and pos_y between (v_y - v_vue) and (v_y + v_vue)
            and pos_etage = v_etage
            and pobj_pos_cod = pos_cod
            and pobj_obj_cod = obj_cod
            and obj_gobj_cod = gobj_cod
            and gobj_tobj_cod in (1,2,4,6,15,21,39,40,41)
            and trajectoire_vue_murs(pos_actuelle,pos_cod, false) = 1 -- and trajectoire_vue(pos_actuelle,pos_cod) = 1
            order by distance(pos_actuelle,pos_cod) asc
            limit 1;
        --si sur la case, on le ramasse, on remet les pa dépensés, et on augmente les pxs
        if (distance(pos_actuelle,pos_cible) = 0) then
            temp_txt := ramasse_objet(v_monstre,nb_tas_obj);

            -- digestion de l'objet seulement si pas enchanté!!
            if v_obj_enchantable<>2 then
                quantite := min(quantite,50);
                update perso set perso_px = perso_px + quantite where perso_cod = v_monstre;
                --On va transformer l'objet et donc le détruire, et si le poids est trop important pour le monstre, il va le vomir et créer d'autres golems d'arme et d'armure, provoquer un jet de métal, et des monstres potentiellement
                quantite := f_del_objet(nb_tas_obj);

                /* Digestion :
                1-20      164    "Statue du dieu Caillou"
                21-26       341    "Pépite d’or"
                27-32        354    "Cryptonite hémicaustique"
                33-38        361    "Ambre"
                39-44        357    "Obsidienne"
                45-50        338    "Émeraude"
                51-56        339    "Rubis"
                57-62       358    "Améthyste"
                63-68        340    "Saphir"
                69-74        353    "Brazilianite épimystique"
                75-80        352    "Apophyllite diatropique"
                81-83        342    "Mithril"
                84-86        337    "Diamant"
                87-88        360    "Jade"
                89-90        359    "Topaze"
                91-92     335    "Fer"
                93-94     336    "Charbon"
                95-96       438    "Morceau d'Adamantium"
                97-98        355    "Dolomite hyporhombique"
                99-100        356    "Erythrite hystésismique"
                */
                v_des := lancer_des(1,100);
                if v_des <= 20 then
                    quantite := cree_objet_perso(164,v_monstre);
                elsif v_des <= 26 then
                    quantite := cree_objet_perso(341,v_monstre);
                elsif v_des <= 32 then
                    quantite := cree_objet_perso(354,v_monstre);
                elsif v_des <= 38 then
                    quantite := cree_objet_perso(361,v_monstre);
                elsif v_des <= 44 then
                    quantite := cree_objet_perso(357,v_monstre);
                elsif v_des <= 50 then
                    quantite := cree_objet_perso(338,v_monstre);
                elsif v_des <= 56 then
                    quantite := cree_objet_perso(339,v_monstre);
                elsif v_des <= 62 then
                    quantite := cree_objet_perso(358,v_monstre);
                elsif v_des <= 68 then
                    quantite := cree_objet_perso(340,v_monstre);
                elsif v_des <= 74 then
                    quantite := cree_objet_perso(353,v_monstre);
                elsif v_des <= 80 then
                    quantite := cree_objet_perso(352,v_monstre);
                elsif v_des <= 83 then
                    quantite := cree_objet_perso(342,v_monstre);
                elsif v_des <= 86 then
                    quantite := cree_objet_perso(337,v_monstre);
                elsif v_des <= 88 then
                    quantite := cree_objet_perso(360,v_monstre);
                elsif v_des <= 90 then
                    quantite := cree_objet_perso(359,v_monstre);
                elsif v_des <= 92 then
                    quantite := cree_objet_perso(335,v_monstre);
                elsif v_des <= 94 then
                    quantite := cree_objet_perso(336,v_monstre);
                elsif v_des <= 96 then
                    quantite := cree_objet_perso(438,v_monstre);
                elsif v_des <= 98 then
                    quantite := cree_objet_perso(355,v_monstre);
                else
                    quantite := cree_objet_perso(356,v_monstre);
                end if;
            end if;

            --on regarde si le goelm est trop chargé, si c'est le cas il vomi son equipement!!!
            select into poids,poids_max get_poids(v_monstre),perso_enc_max	from perso where perso_cod = v_monstre;
            if poids > poids_max then
                --On va vider l'inventaire du monstre en envoyant tous les objets dans l'étage à 10 cases autour => 10/09/2019: Changement pour 3 cases autour
                code_retour := code_retour||'Objets balancés : ';
                for ligne in select perobj_obj_cod from perso_objets where perobj_perso_cod = v_monstre loop
                    select into position_arrivee lancer_position from lancer_position(pos_actuelle,3) where lancer_position not in (select mur_pos_cod from murs) order by random() limit 1;
                    delete from perso_objets where perobj_obj_cod = ligne.perobj_obj_cod;
                    insert into objet_position (pobj_cod,pobj_obj_cod,pobj_pos_cod) values
                    (nextval('seq_pobj_cod'),ligne.perobj_obj_cod,position_arrivee);
                    code_retour := code_retour||trim(to_char(ligne.perobj_obj_cod,'999999999999'))||'<br>';
                end loop;
                -- On va créer un élémentaire de terre
                --quantite := cree_monstre_pos(187,pos_actuelle);
                -- Un jet d'acide sur les persos autour
                -- A faire

                -- ajout d'un evenement
                perform insere_evenement(v_monstre, v_monstre, 112, '[perso_cod1] a vomi.', 'O', 'N', null);

            end if;
            --On se déplace si le tas n'est pas sur la case
        else
            code_retour := code_retour||'Déplacement vers la cible.<br>';
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
	end loop;

/*************************************************/
/* Etape 5 : tout semble fini                    */
/*************************************************/
	return code_retour;
end;
$_$;


ALTER FUNCTION public.ia_golem_arme(integer) OWNER TO delain;
