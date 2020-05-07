--
-- Name: deb_tour_degats(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.deb_tour_degats(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/************************************************/
/* deb_tour_degats augmentant leds dégâts de 4  */
/************************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_pos integer;
	ligne record;
	has_bloque integer;
	v_bloque_magie integer;
	v_pa_attaque integer;
	v_malus_degats integer;
	v_chance_toucher integer;
	texte_evt text;
	v_pa_dep integer;
begin
	select into v_pos
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = personnage;
	if not found then
		return 'Anomalie sur position !';
	end if;
	if lancer_des(1,100) < 30 then
		perform ajoute_bonus(personnage, 'DEG', 2, 4);
		texte_evt := '[perso_cod1] est devenu totalement incontrolable et enragé ! Ses coups d''une force inouie pleuvent pour s''abattre sur sa cible !';
		 	insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
     			values(54,now(),1,personnage,texte_evt,'O','O');
	end if;

	return 'OK';
end;$_$;


ALTER FUNCTION public.deb_tour_degats(integer) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_degats(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.deb_tour_degats(integer) IS '(OBSOLÈTE)';


--
-- Name: deb_tour_degats(integer, text, integer, character, text, numeric, text); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text) RETURNS text
    LANGUAGE plpgsql
    AS $_$/**************************************************/
/* deb_tour_degats                                */
/* Applique des dégâts (ou soins)                 */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = valeur (Entier ou +/-nDy)               */
/*   $3 = distance (-1..n)                        */
/*   $4 = cibles (type : SAERTP    )              */
/*   $5 = cibles nombre, au format roliste        */
/*   $6 = Probabilité d’activer l’effet           */
/*   $7 = Message d’événement associé             */
/**************************************************/
/* Créé le 9 juin 2014                            */
/**************************************************/
declare
	-- Parameters
	v_source alias for $1;
	v_valeur alias for $2;
	v_distance alias for $3;
	v_cibles_type alias for $4;
	v_cibles_nombre alias for $5;
	v_proba alias for $6;
	v_texte_evt alias for $7;

	-- initial data
	v_x_source integer; -- source X position
	v_y_source integer; -- source Y position
	v_et_source integer; -- etage de la source
	v_type_source integer; -- Type perso de la source
	v_cibles_nombre_max integer; -- Nombre calculé de cibles
	v_cibles_nombre_reel integer; -- Nombre effectif de cibles
	v_race_source integer; -- La race de la source
	v_position_source integer; -- Le pos_cod de la source

	-- Output and data holders
	ligne record;  -- Une ligne d’enregistrements
	i integer; -- Compteur de boucle
	ch character; -- Un caractère tout ce qu’il y a de plus banal
	valeur integer;     -- Valeur numérique des PV infligés
	valeur_pvp integer; -- Valeur numérique des PV ingligés, corrigés de l’atténuation PVP
	-- Resistance magique
	v_bloque_magie integer;
	v_RM1 integer;
	compt integer;
	niveau_attaquant integer;
	v_seuil integer;
	code_retour text;

begin
	-- Chances de déclencher l’effet
	if random() > v_proba then
		return 'Pas d’effet automatique de dégâts/soins.';
	end if;
	-- Initialisation des conteneurs
	code_retour := '';

	-- Position et type perso
	select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source
		pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod
	from perso_position, positions, perso
	where ppos_perso_cod = v_source
		and pos_cod = ppos_pos_cod
		and perso_cod = v_source;

	-- on récupère les données de l’attaquant (utilisé dans le calcul de résistance)
	select into niveau_attaquant perso_niveau
	from perso
	where perso_cod = v_source;

	-- Cibles
	v_cibles_nombre_max := f_lit_des_roliste(v_cibles_nombre);

	-- On compte le nombre de cibles réelles (utilisé pour le calcul futur du gain de PX)
	select into v_cibles_nombre_reel min(v_cibles_nombre_max, count(*)::integer)
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	inner join positions on pos_cod = ppos_pos_cod
	left outer join lieu_position on lpos_pos_cod = pos_cod
	left outer join lieu on lieu_cod = lpos_lieu_cod
	where perso_actif = 'O'
		and perso_tangible = 'O'
		-- À portée
		and pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)
		and pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)
		and pos_etage = v_et_source
		and trajectoire_vue(pos_cod, v_position_source) = '1'
		-- Hors refuge si on le souhaite
		and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
		-- Parmi les cibles spécifiées
		and
			((v_cibles_type = 'S' and perso_cod = v_source) or
			(v_cibles_type = 'A' and perso_type_perso = v_type_source) or
			(v_cibles_type = 'E' and perso_type_perso != v_type_source) or
			(v_cibles_type = 'R' and perso_race_cod = v_race_source) or
			(v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
			(v_cibles_type = 'T'));

	-- Et finalement on parcourt les cibles.
	for ligne in (select perso_cod, perso_type_perso, perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, perso_pv, perso_pv_max
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		left outer join lieu_position on lpos_pos_cod = pos_cod
		left outer join lieu on lieu_cod = lpos_lieu_cod
		where perso_actif = 'O'
			and perso_tangible = 'O'
			-- À portée
			and pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)
			and pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)
			and pos_etage = v_et_source
			and trajectoire_vue(pos_cod, v_position_source) = '1'
			-- Hors refuge si on le souhaite
			and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
			-- Parmi les cibles spécifiées
			and
				((v_cibles_type = 'S' and perso_cod = v_source) or
				(v_cibles_type = 'A' and perso_type_perso = v_type_source) or
				(v_cibles_type = 'E' and perso_type_perso != v_type_source) or
				(v_cibles_type = 'R' and perso_race_cod = v_race_source) or
				(v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
				(v_cibles_type = 'T'))
		-- Dans les limites autorisées
		order by random()
		limit v_cibles_nombre_max)
	loop
		-- On peut maintenant appliquer le bonus ou l’action sur une cible unique.

		-- Valeur
		valeur := f_lit_des_roliste(v_valeur);

		-- Ajout azaghal on teste un simili resiste magie pour chaque personne cible sauf si la cible est le lanceur
		if v_cibles_type != 'S' AND valeur > 0 then
			v_bloque_magie := 0;

			-- on calcule le seuil de résistance (en fonction de l’int, la con le niv du sort et la marge de réussite
			v_RM1 := (ligne.perso_int * 5) + floor(ligne.perso_con / 10) + floor(ligne.perso_niveau / 2);
			compt := 30;
			compt := compt + (2 * niveau_attaquant);

			-- calcul du seuil effectif
			v_seuil = v_RM1 - compt;
			-- on limite une premiere fois le seuil à 15
			if v_seuil < 15 then
				v_seuil := 15;
			end if;

			-- le seuil (v_seuil) est maintenant calculé on peut tester
			if lancer_des(1, 100) > v_seuil then
				-- resistance ratée
				v_bloque_magie := 0;
			else
				v_bloque_magie := 1;
				valeur := floor(valeur / 2);
			end if;
		end if;

		-- Effet: Perte ou gain de PVs
		if valeur < 0 then	-- Gain de PV (soins)
			valeur := min (valeur, ligne.perso_pv_max - ligne.perso_pv);
			code_retour := code_retour || '<br />Vous soignez ' || ligne.perso_nom || ' de ' || (valeur * -1)::text || ' PVs.';

			update perso set perso_pv = ligne.perso_pv + valeur where perso_cod = ligne.perso_cod;

			-- On rajoute la ligne d’événements
			if strpos(v_texte_evt , '[cible]') != 0 then
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', redonnant ' || (valeur * -1)::text || ' PVs.', 'O', 'N', null);
			else
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', redonnant ' || (valeur * -1)::text || ' PVs.', 'O', 'O', null);
			end if;
		elsif ligne.perso_cod != v_source then	-- Perte de PVs (dégâts)
			valeur_pvp := effectue_degats_perso(ligne.perso_cod, valeur, v_source);
			code_retour := code_retour || '<br />Vous infligez ' || valeur_pvp::text || ' dégâts à ' || ligne.perso_nom;
			if v_bloque_magie = 1 then
				code_retour := code_retour || ' (résisté)';
			end if;
			if valeur_pvp != valeur then
				code_retour := code_retour || ' (atténué pour PVP)';
				insert into trace (trc_texte) values ('att '||trim(to_char(v_source,'99999999'))||' cib '||trim(to_char(ligne.perso_cod,'99999999'))||' init '||trim(to_char(valeur,'99999999'))||' fin '||trim(to_char(valeur_pvp,'99999999')));
			end if;
			code_retour := code_retour || '.';

			insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
			values (2, v_source, ligne.perso_cod, (0.5 * ln(ligne.perso_pv_max) * valeur_pvp) / v_cibles_nombre_reel);

			-- On rajoute la ligne d’événements
			if strpos(v_texte_evt , '[cible]') != 0 then
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', causant ' || valeur_pvp::text || ' dégâts.', 'O', 'N', null);
			else
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', causant ' || valeur_pvp::text || ' dégâts.', 'O', 'O', null);
			end if;

			-- On gère les dégâts
			if ligne.perso_pv <= valeur_pvp then
				-- on a tué l’adversaire !!
				code_retour := code_retour || ' Vous l’avez <b>tué</b>, gagnant ' || split_part(tue_perso_final(v_source, ligne.perso_cod), ';', 1) || ' PXs !';
			else
				update perso set perso_pv = perso_pv - valeur_pvp where perso_cod = ligne.perso_cod;
			end if;
		end if;
	end loop;

	if code_retour = '' then
		code_retour := 'Aucune cible éligible pour l’effet de dégâts/soins.';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_degats(integer, text, integer, character, text, numeric, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text) IS '(OBSOLÈTE) Gère un effet automatique de type dégâts (de zone ou pas) au début de chaque tour (ou de type soin si la valeur est négative...)';


--
-- Name: deb_tour_degats(integer, text, integer, character, text, numeric, text, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/**************************************************/
/* deb_tour_degats                                */
/* Applique des dégâts (ou soins)                 */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = valeur (Entier ou +/-nDy)               */
/*   $3 = distance (-1..n)                        */
/*   $4 = cibles (type : SAERTPCO  )              */
/*   $5 = cibles nombre, au format roliste        */
/*   $6 = Probabilité d’activer l’effet           */
/*   $7 = Message d’événement associé             */
/*   $8 = Perso ciblé                             */
/**************************************************/
/* Créé le 9 juin 2014                            */
/**************************************************/
declare
	-- Parameters
	v_source alias for $1;
	v_valeur alias for $2;
	v_distance alias for $3;
	v_cibles_type alias for $4;
	v_cibles_nombre alias for $5;
	v_proba alias for $6;
	v_texte_evt alias for $7;
	v_cible_donnee alias for $8;

	-- initial data
	v_x_source integer;           -- source X position
	v_y_source integer;           -- source Y position
	v_et_source integer;          -- etage de la source
	v_type_source integer;        -- Type perso de la source
	v_cibles_nombre_max integer;  -- Nombre calculé de cibles
	v_cibles_nombre_reel integer; -- Nombre effectif de cibles
	v_race_source integer;        -- La race de la source
	v_position_source integer;    -- Le pos_cod de la source
	v_cible_du_monstre integer;   -- La cible actuelle du monstre

	-- Output and data holders
	ligne record;                 -- Une ligne d’enregistrements
	i integer;                    -- Compteur de boucle
	ch character;                 -- Un caractère tout ce qu’il y a de plus banal
	valeur integer;               -- Valeur numérique des PV infligés
	valeur_pvp integer;           -- Valeur numérique des PV ingligés, corrigés de l’atténuation PVP
	-- Resistance magique
	v_bloque_magie integer;
	v_RM1 integer;
	compt integer;
	niveau_attaquant integer;
	v_seuil integer;
	code_retour text;
        v_nom_efaseur text;

begin
        -- récupération du nom de la source de l'effet
        select into v_nom_efaseur perso_nom from perso where perso_cod = v_source;
	-- Chances de déclencher l’effet
	if random() > v_proba then
		return 'Pas d’effet automatique de dégâts/soins.';
	end if;
	-- Initialisation des conteneurs
	code_retour := '';

	-- Position et type perso
	select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source, v_cible_du_monstre
		pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod, perso_cible
	from perso_position, positions, perso
	where ppos_perso_cod = v_source
		and pos_cod = ppos_pos_cod
		and perso_cod = v_source;

	-- on récupère les données de l’attaquant (utilisé dans le calcul de résistance)
	select into niveau_attaquant perso_niveau
	from perso
	where perso_cod = v_source;

	-- Cibles
	v_cibles_nombre_max := f_lit_des_roliste(v_cibles_nombre);

	-- On compte le nombre de cibles réelles (utilisé pour le calcul futur du gain de PX)
	select into v_cibles_nombre_reel min(v_cibles_nombre_max, count(*)::integer)
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	inner join positions on pos_cod = ppos_pos_cod
	left outer join lieu_position on lpos_pos_cod = pos_cod
	left outer join lieu on lieu_cod = lpos_lieu_cod
	where perso_actif = 'O'
		and perso_tangible = 'O'
		-- À portée
		and pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)
		and pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)
		and pos_etage = v_et_source
		and trajectoire_vue(pos_cod, v_position_source) = '1'
		-- Hors refuge si on le souhaite
		and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
		-- Parmi les cibles spécifiées
		and
			((v_cibles_type = 'S' and perso_cod = v_source) or
			(v_cibles_type = 'A' and perso_type_perso = v_type_source) or
			(v_cibles_type = 'E' and perso_type_perso != v_type_source) or
			(v_cibles_type = 'R' and perso_race_cod = v_race_source) or
			(v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
			(v_cibles_type = 'C' and perso_cod = v_cible_donnee) or
			(v_cibles_type = 'O' and perso_cod = v_cible_donnee) or
			(v_cibles_type = 'T'));

	-- Et finalement on parcourt les cibles.
	for ligne in (select perso_cod, perso_type_perso, perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, perso_pv, perso_pv_max
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		left outer join lieu_position on lpos_pos_cod = pos_cod
		left outer join lieu on lieu_cod = lpos_lieu_cod
		where perso_actif = 'O'
			and perso_tangible = 'O'
			-- À portée
			and pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)
			and pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)
			and pos_etage = v_et_source
			and trajectoire_vue(pos_cod, v_position_source) = '1'
			-- Hors refuge si on le souhaite
			and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
			-- Parmi les cibles spécifiées
			and
				((v_cibles_type = 'S' and perso_cod = v_source) or
				(v_cibles_type = 'A' and perso_type_perso = v_type_source) or
				(v_cibles_type = 'E' and perso_type_perso != v_type_source) or
				(v_cibles_type = 'R' and perso_race_cod = v_race_source) or
				(v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
				(v_cibles_type = 'C' and perso_cod = v_cible_donnee) or
				(v_cibles_type = 'O' and perso_cod = v_cible_donnee) or
				(v_cibles_type = 'T'))
		-- Dans les limites autorisées
		order by random()
		limit v_cibles_nombre_max)
	loop
		-- On peut maintenant appliquer le bonus ou l’action sur une cible unique.

		-- Valeur
		valeur := f_lit_des_roliste(v_valeur);

		-- Ajout azaghal on teste un simili resiste magie pour chaque personne cible sauf si la cible est le lanceur
		if v_cibles_type != 'S' AND valeur > 0 then
			v_bloque_magie := 0;

			-- on calcule le seuil de résistance (en fonction de l’int, la con le niv du sort et la marge de réussite
			v_RM1 := (ligne.perso_int * 5) + floor(ligne.perso_con / 10) + floor(ligne.perso_niveau / 2);
			compt := 30;
			compt := compt + (2 * niveau_attaquant);

			-- calcul du seuil effectif
			v_seuil = v_RM1 - compt;
			-- on limite une premiere fois le seuil à 15
			if v_seuil < 15 then
				v_seuil := 15;
			end if;

			-- le seuil (v_seuil) est maintenant calculé on peut tester
			if lancer_des(1, 100) > v_seuil then
				-- resistance ratée
				v_bloque_magie := 0;
			else
				v_bloque_magie := 1;
				valeur := floor(valeur / 2);
			end if;
		end if;

		-- Effet: Perte ou gain de PVs
		if valeur < 0 then	-- Gain de PV (soins)
			valeur := min ((valeur * -1), ligne.perso_pv_max - ligne.perso_pv);
			code_retour := code_retour || '<br />' || v_nom_efaseur || ' soigne ' || ligne.perso_nom || ' de ' || valeur::text || ' PVs.';

			update perso set perso_pv = ligne.perso_pv + valeur where perso_cod = ligne.perso_cod;
                        -- On rajoute la ligne d’événements
			if strpos(v_texte_evt , '[cible]') != 0 then
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', redonnant ' || valeur::text || ' PVs.', 'O', 'N', null);
			else
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', redonnant ' || valeur::text || ' PVs.', 'O', 'O', null);
			end if;
		elsif ligne.perso_cod != v_source then	-- Perte de PVs (dégâts)
			valeur_pvp := effectue_degats_perso(ligne.perso_cod, valeur, v_source);
			code_retour := code_retour || '<br />' || v_nom_efaseur || ' inflige ' || valeur_pvp::text || ' dégâts à ' || ligne.perso_nom;
			if v_bloque_magie = 1 then
				code_retour := code_retour || ' (résisté)';
			end if;
			if valeur_pvp != valeur then
				code_retour := code_retour || ' (atténué pour PVP)';
				insert into trace (trc_texte) values ('att '||trim(to_char(v_source,'99999999'))||' cib '||trim(to_char(ligne.perso_cod,'99999999'))||' init '||trim(to_char(valeur,'99999999'))||' fin '||trim(to_char(valeur_pvp,'99999999')));
			end if;
			code_retour := code_retour || '.';

			insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
			values (2, v_source, ligne.perso_cod, (0.5 * ln(ligne.perso_pv_max) * valeur_pvp) / v_cibles_nombre_reel);

			-- On rajoute la ligne d’événements
			if strpos(v_texte_evt , '[cible]') != 0 then
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', causant ' || valeur_pvp::text || ' dégâts.', 'O', 'N', null);
			else
				perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt || ', causant ' || valeur_pvp::text || ' dégâts.', 'O', 'O', null);
			end if;

			-- On gère les dégâts
			if ligne.perso_pv <= valeur_pvp then
				-- on a tué l’adversaire !!
				code_retour := code_retour || ' Vous l’avez <b>tué</b>, gagnant ' || split_part(tue_perso_final(v_source, ligne.perso_cod), ';', 1) || ' PXs !';
			else
				update perso set perso_pv = perso_pv - valeur_pvp where perso_cod = ligne.perso_cod;
			end if;
		end if;
	end loop;

	if code_retour = '' then
		code_retour := 'Aucune cible éligible pour l’effet de dégâts/soins.';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text, integer) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_degats(integer, text, integer, character, text, numeric, text, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.deb_tour_degats(integer, text, integer, character, text, numeric, text, integer) IS 'Gère un effet automatique de type dégâts (de zone ou pas) au début de chaque tour (ou de type soin si la valeur est négative...)';

