--
-- Name: ia_golem_pierre(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION ia_golem_pierre(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction ia_golem_pierre				                     */
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
	code_retour := 'IA golem de pierres précieuses<br>Monstre '||trim(to_char(v_monstre,'999999999999'))||'<br>';
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
  17    "Minerai"
  19	"Pierre précieuse"
  28	"Espèce Minérale"
  42	"Grisbi"

 */
	select into nb_tas_obj count(obj_cod)
		from objet_position,positions,objets,objet_generique
		where pos_x between (v_x - v_vue) and (v_x + v_vue)
		and pos_y between (v_y - v_vue) and (v_y + v_vue)
		and pos_etage = v_etage
		and pobj_pos_cod = pos_cod
		and pobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod in (17, 19, 28, 42)
		and trajectoire_vue_murs(pos_actuelle,pos_cod,false) = 1;
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
			and gobj_tobj_cod in (17, 19, 28, 42)
			and trajectoire_vue_murs(pos_actuelle,pos_cod,false) = 1
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
                    27	"Mâle"
                    28	"Femelle"
                    29	"Minéral"
                    30	"Végétal"
                    31	"Animal"
                    32	"Terre"
                    33	"Eau"
                    34	"Feu"
                    35	"Air"
                    36	"Pouce"
                    37	"Index"
                    38	"Majeur"
                    39	"Annulaire"
                    40	"Auriculaire"
                    41	"Merle de Kilgwri"
                    42	"Cerf de Rhedynvre"
                    43	"Hibou de Cwm Cawlwyd"
                    44	"Aigle de Gwern Abwy"
                    45	"Saumon de Llyn Llyn"
                    46	"Sanglier de Yskithrwynn"
                    47	"Muladhara"
                    48	"Swadhistana"
                    49	"Manipura"
                    50	"Anahata"
                    51	"Vishudda"
                    52	"Ajna"
                    53	"Sahasrara"
                 */
                v_des := lancer_des(1,5040);
                if v_des <= 20   then -- Mâle = 1/12
                    quantite := cree_objet_perso(27,v_monstre);
                elsif v_des <= 40   then -- Femelle = 1/12
                    quantite := cree_objet_perso(28,v_monstre);
                elsif v_des <= 120  then -- Minéral = 1/18
                    quantite := cree_objet_perso(29,v_monstre);
                elsif v_des <= 1400 then -- Végétal = 1/18
                    quantite := cree_objet_perso(30,v_monstre);
                elsif v_des <= 1680 then -- Animal = 1/18
                    quantite := cree_objet_perso(31,v_monstre);
                elsif v_des <= 1890 then -- Terre = 1/24
                    quantite := cree_objet_perso(32,v_monstre);
                elsif v_des <= 2100 then -- Eau = 1/24
                    quantite := cree_objet_perso(33,v_monstre);
                elsif v_des <= 2310 then -- Feu = 1/24
                    quantite := cree_objet_perso(34,v_monstre);
                elsif v_des <= 2520 then -- Air = 1/24
                    quantite := cree_objet_perso(35,v_monstre);
                elsif v_des <= 2688 then -- Pouce = 1/30
                    quantite := cree_objet_perso(36,v_monstre);
                elsif v_des <= 2856 then -- Index = 1/30
                    quantite := cree_objet_perso(37,v_monstre);
                elsif v_des <= 3024 then -- Majeur = 1/30
                    quantite := cree_objet_perso(38,v_monstre);
                elsif v_des <= 3192 then -- Annulaire = 1/30
                    quantite := cree_objet_perso(39,v_monstre);
                elsif v_des <= 3360 then -- Auriculaire = 1/30
                    quantite := cree_objet_perso(40,v_monstre);
                elsif v_des <= 3500 then -- Merle de Kilgwri = 1/36
                    quantite := cree_objet_perso(41,v_monstre);
                elsif v_des <= 3640 then -- Cerf de Rhedynvre = 1/36
                    quantite := cree_objet_perso(42,v_monstre);
                elsif v_des <= 3780 then -- Hibou de Cwm Cawlwyd = 1/36
                    quantite := cree_objet_perso(43,v_monstre);
                elsif v_des <= 3920 then -- Aigle de Gwern Abwy = 1/36
                    quantite := cree_objet_perso(44,v_monstre);
                elsif v_des <= 4060 then -- Saumon de Llyn Llyn = 1/36
                    quantite := cree_objet_perso(45,v_monstre);
                elsif v_des <= 4200 then -- Sanglier de Yskithrwynn = 1/36
                    quantite := cree_objet_perso(46,v_monstre);
                elsif v_des <= 4320 then -- Muladhara = 1/42
                    quantite := cree_objet_perso(47,v_monstre);
                elsif v_des <= 4440 then -- Swadhistana = 1/42
                    quantite := cree_objet_perso(48,v_monstre);
                elsif v_des <= 4560 then -- Manipura = 1/42
                    quantite := cree_objet_perso(49,v_monstre);
                elsif v_des <= 4680 then -- Anahata = 1/42
                    quantite := cree_objet_perso(51,v_monstre);
                elsif v_des <= 4800 then -- Vishudda = 1/42
                    quantite := cree_objet_perso(51,v_monstre);
                elsif v_des <= 4920 then -- Ajna = 1/42
                    quantite := cree_objet_perso(52,v_monstre);
                else
                    quantite := cree_objet_perso(53,v_monstre);
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


ALTER FUNCTION public.ia_golem_pierre(integer) OWNER TO delain;
