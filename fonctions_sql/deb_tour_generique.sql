--
-- Name: deb_tour_generique(integer, text, text, text, text, numeric, text); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function deb_tour_generique(integer, text, text, text, text, numeric, text) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* deb_tour_generique                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = bonus (de la table bonus_type)          */
/*   $3 = valeur (Entier ou +/-nDy)               */
/*   $4 = distance (-1..n , nC pour une case)     */
/*   $5 = cibles (SAERTCPO, et limite)              */
/*   $6 = Probabilité d atteindre chaque cible    */
/*   $7 = Message d événement associé             */
/**************************************************/
/* Créé le 5 Septembre 2007                       */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_bonus alias for $2;
  v_valeur alias for $3;
  v_distance alias for $4;
  v_cibles alias for $5;
  v_proba alias for $6;
  v_texte_evt alias for $7;

  -- initial data
  v_x_source integer; -- source X position
  v_y_source integer; -- source Y position
  v_et_source integer; -- etage de la source
  v_type_source integer; -- Type perso de la source
  v_distance_int integer; -- Range
  v_cibles_type character; -- Type de cible (SAERT)
  v_cibles_nombre integer; -- Nombre de cibles maxi
  v_race_source integer; -- La race de la source
  v_position_source integer; -- Le pos_cod de la source

  -- Output and data holders
  ligne record;  -- Une ligne d’enregistrements
  i integer; -- Compteur de boucle
  ch character; -- Un caractère tout ce qu’il y a de plus banal
  valeur integer; -- Valeur numérique de l’impact du bonus ou de l’action
  n integer; -- Nombre de dés.
  d integer; -- Nombre de faces du dé
  signe integer; -- Signe de la valeur (1 ou -1)
  cible_case integer; -- Cible une case
  -- Resistance magique
  v_bloque_magie integer;
  v_RM1 integer;
  compt integer;
  niveau_cible integer;
  v_int_cible integer;
  v_con_cible integer;
  niveau_attaquant integer;
  v_seuil integer;
  des integer;
  code_retour text;
  -- temp pour éviter les erreurs dans les logs
  v_cible_donnee integer;
  v_bonus_texte text;

begin

  select tonbus_libelle || case when length(v_bonus)>3 then ' (cumulatif)' else '' end into v_bonus_texte from bonus_type where tbonus_libc = substr(v_bonus, 1, 3) ;

  v_cible_donnee := 0;
  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de «' || v_bonus_texte || '» ce tour-ci.';
  end if;
  -- Initialisation des conteneurs
  -- Distance
  cible_case := 0;
  code_retour := '';
  for i in 1..length(v_distance) loop
    ch := substr(v_distance , i , 1);
    if ch IN ('c' , 'C') then
      cible_case := 1;
    end if;
  end loop;
  if (cible_case = 0) then
    v_distance_int := v_distance;
  else
    -- La cible est une case. Choisir une case puis définir case = source, et
    -- distance = 0
    -- TODO
    v_distance_int := 0;
  end if;

  -- Position et type perso
  select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source
    pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod
  from perso_position, positions, perso
  where ppos_perso_cod = v_source
        and pos_cod = ppos_pos_cod
        and perso_cod = v_source;

  -- Cibles
  v_cibles_type := 'T'; -- Tout le monde, par défaut
  v_cibles_nombre := 0; -- Pas de limite, par défaut
  for i in 1..length(v_cibles) loop
    ch := substr(v_cibles , i , 1);
    if ch IN ('S' , 'A' , 'E' , 'R' , 'T', 'P', 'C', 'O') then
      v_cibles_type := ch;
    else
      v_cibles_nombre := 10 * v_cibles_nombre + cast(ch AS integer);
    end if;
  end loop;
  if (v_cibles_nombre = 0) then
    v_cibles_nombre := 6;
  end if;

  -- Détermination de la liste des cibles
  if (v_distance_int < 0) then
    v_distance_int = 0;
    v_cibles_type = 'S';
  end if;
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod
                from perso
                  inner join perso_position on ppos_perso_cod = perso_cod
                  inner join positions on pos_cod = ppos_pos_cod
                  left outer join lieu_position on lpos_pos_cod = pos_cod
                  left outer join lieu on lieu_cod = lpos_lieu_cod
                where perso_actif = 'O'
                      and perso_tangible = 'O'
                      -- À portée
                      and pos_x between (v_x_source - v_distance_int) and (v_x_source + v_distance_int)
                      and pos_y between (v_y_source - v_distance_int) and (v_y_source + v_distance_int)
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
                /*((v_cibles_type = 'S' and perso_cod = v_source) or
                (v_cibles_type = 'A' and perso_type_perso = v_type_source) or
                (v_cibles_type = 'E' and perso_type_perso != v_type_source) or
                (v_cibles_type = 'R' and perso_race_cod = v_race_source) or
                (v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
                (v_cibles_type = 'T'))*/

                -- Dans les limites autorisées
                order by random()
                limit lancer_des(1, v_cibles_nombre))
  loop
    -- On peut maintenant appliquer le bonus ou l’action sur une cible unique.
    code_retour := code_retour || 'Cible : ' || trim(to_char(ligne.perso_cod, '9999999999')) || ' / ';
    -- Valeur
    n := 0;
    d := -1;
    signe := 1;
    for i in 1..length(v_valeur) loop
      ch := substr(v_valeur , i , 1);
      if ch IN ('D' , 'd') then
        d := 0;
      else
        if ch = '-' then
          signe := -1;
        elseif d = -1 then
          n := n * 10 + cast(ch AS integer);
        else
          d := d * 10 + cast(ch AS integer);
        end if;
      end if;
    end loop;
    if (d = -1) then
      d := 1;
    end if;
    valeur := signe * lancer_des(n , d);

    -- Ajout azaghal on teste un simili resiste magie pour chaque personne cible sauf si la cible est le lanceur
    if v_cibles_type != 'S' then
      v_bloque_magie := 0;
      -- on récupère les données de l’attaquant
      select into niveau_attaquant perso_niveau
      from perso
      where perso_cod = v_source;

      -- on récupère les données de la cible
      select into niveau_cible, v_int_cible, v_con_cible
        perso_niveau, perso_int, perso_con
      from perso
      where perso_cod = ligne.perso_cod;

      -- on calcule le seuil de résistance (en fonction de l’int, la con le niv du sort et la marge de réussite
      v_con_cible := floor(v_con_cible/10);
      niveau_cible := floor(niveau_cible/2);
      v_RM1 := (v_int_cible * 5) + v_con_cible + niveau_cible;
      compt := 30;
      compt := compt + (2*niveau_attaquant);

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
        valeur:= floor(valeur/2);
        if valeur = 0 then
          valeur := signe * 1;
        end if; /*rajout Blade du cas ou valeur devient 0 à cause du floor*/
      end if;

      code_retour := code_retour || 'seuil : ' || trim(to_char(v_seuil, '99999')) || ' / ';
    end if;

    -- Effet: Bonus ou action
    if not exists (select tbonus_libc from bonus_type where tbonus_libc = v_bonus) then
    -- Ceci est une action à effet immédiat. On ignore pour le moment.
    else
      -- Un bonus. On met à jour la table bonus
      perform ajoute_bonus(ligne.perso_cod, v_bonus, 2, valeur);
    end if;

    -- On rajoute la ligne d’événements
    if strpos(v_texte_evt , '[cible]') != 0 then
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
    else
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
    end if;
  end loop;

  return 'OK ' || code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_generique(integer, text, text, text, text, numeric, text) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_generique(integer, text, text, text, text, numeric, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION deb_tour_generique(integer, text, text, text, text, numeric, text) IS '(OBSOLÈTE)';


--
-- Name: deb_tour_generique(integer, text, text, text, text, numeric, integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function deb_tour_generique(integer, text, text, text, text, numeric, integer, text) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* deb_tour_generique                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = bonus (de la table bonus_type)          */
/*   $3 = valeur (Entier ou +/-nDy)               */
/*   $4 = distance (-1..n , nC pour une case)     */
/*   $5 = cibles (SAERTP, et limite)              */
/*   $6 = Probabilité d’atteindre chaque cible    */
/*   $7 = Durée de l’effet                        */
/*   $8 = Message d événement associé             */
/**************************************************/
/* Créé le 5 Septembre 2007                       */
/* Modif le 22 mai 2014 (paramètre Durée)         */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_bonus alias for $2;
  v_valeur alias for $3;
  v_distance alias for $4;
  v_cibles alias for $5;
  v_proba alias for $6;
  v_duree alias for $7;
  v_texte_evt alias for $8;

  -- initial data
  v_x_source integer; -- source X position
  v_y_source integer; -- source Y position
  v_et_source integer; -- etage de la source
  v_type_source integer; -- Type perso de la source
  v_distance_int integer; -- Range
  v_cibles_type character; -- Type de cible (SAERT)
  v_cibles_nombre integer; -- Nombre de cibles maxi
  v_race_source integer; -- La race de la source
  v_position_source integer; -- Le pos_cod de la source
  v_bonus_texte text;

  -- Output and data holders
  ligne record;  -- Une ligne d’enregistrements
  i integer; -- Compteur de boucle
  ch character; -- Un caractère tout ce qu’il y a de plus banal
  valeur integer; -- Valeur numérique de l’impact du bonus ou de l’action
  cible_case integer; -- Cible une case
  -- Resistance magique
  v_bloque_magie integer;
  v_RM1 integer;
  compt integer;
  niveau_cible integer;
  v_int_cible integer;
  v_con_cible integer;
  niveau_attaquant integer;
  v_seuil integer;
  des integer;
  code_retour text;

begin

  select tonbus_libelle || case when length(v_bonus)>3 then ' (cumulatif)' else '' end into v_bonus_texte from bonus_type where tbonus_libc = substr(v_bonus, 1, 3) ;

  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de «' || v_bonus_texte || '» ce tour-ci.';
  end if;
  -- Initialisation des conteneurs
  -- Distance
  cible_case := 0;
  code_retour := '';
  for i in 1..length(v_distance) loop
    ch := substr(v_distance , i , 1);
    if ch IN ('c' , 'C') then
      cible_case := 1;
    end if;
  end loop;
  if (cible_case = 0) then
    v_distance_int := v_distance;
  else
    -- La cible est une case. Choisir une case puis définir case = source, et
    -- distance = 0
    -- TODO
    v_distance_int := 0;
  end if;

  -- Position et type perso
  select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source
    pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod
  from perso_position, positions, perso
  where ppos_perso_cod = v_source
        and pos_cod = ppos_pos_cod
        and perso_cod = v_source;

  -- Cibles
  v_cibles_type := 'T'; -- Tout le monde, par défaut
  v_cibles_nombre := 0; -- Pas de limite, par défaut
  for i in 1..length(v_cibles) loop
    ch := substr(v_cibles , i , 1);
    if ch IN ('S' , 'A' , 'E' , 'R' , 'T', 'P') then
      v_cibles_type := ch;
    else
      v_cibles_nombre := 10 * v_cibles_nombre + cast(ch AS integer);
    end if;
  end loop;
  if (v_cibles_nombre = 0) then
    v_cibles_nombre := 6;
  end if;

  -- Détermination de la liste des cibles
  if (v_distance_int < 0) then
    v_distance_int = 0;
    v_cibles_type = 'S';
  end if;
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod
                from perso
                  inner join perso_position on ppos_perso_cod = perso_cod
                  inner join positions on pos_cod = ppos_pos_cod
                  left outer join lieu_position on lpos_pos_cod = pos_cod
                  left outer join lieu on lieu_cod = lpos_lieu_cod
                where perso_actif = 'O'
                      and perso_tangible = 'O'
                      -- À portée
                      and pos_x between (v_x_source - v_distance_int) and (v_x_source + v_distance_int)
                      and pos_y between (v_y_source - v_distance_int) and (v_y_source + v_distance_int)
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
                limit lancer_des(1, v_cibles_nombre))
  loop
    -- On peut maintenant appliquer le bonus ou l’action sur une cible unique.
    code_retour := code_retour || 'Cible : ' || trim(to_char(ligne.perso_cod, '9999999999')) || ' / ';

    -- Valeur
    valeur := f_lit_des_roliste(v_valeur);

    -- Ajout azaghal on teste un simili resiste magie pour chaque personne cible sauf si la cible est le lanceur
    if v_cibles_type != 'S' then
      v_bloque_magie := 0;
      -- on récupère les données de l’attaquant
      select into niveau_attaquant perso_niveau
      from perso
      where perso_cod = v_source;

      -- on récupère les données de la cible
      select into niveau_cible, v_int_cible, v_con_cible
        perso_niveau, perso_int, perso_con
      from perso
      where perso_cod = ligne.perso_cod;

      -- on calcule le seuil de résistance (en fonction de l’int, la con le niv du sort et la marge de réussite
      v_con_cible := floor(v_con_cible/10);
      niveau_cible := floor(niveau_cible/2);
      v_RM1 := (v_int_cible * 5) + v_con_cible + niveau_cible;
      compt := 30;
      compt := compt + (2*niveau_attaquant);

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
        if floor(valeur / 2) <> 0 then
          valeur := floor(valeur / 2);
        elseif valeur < 0 then
          valeur := -1;
        else
          valeur := 1;
        end if; /*rajout Blade du cas ou valeur devient 0 à cause du floor*/
      end if;

      code_retour := code_retour || 'seuil : ' || trim(to_char(v_seuil, '99999')) || ' / ';
    end if;

    -- Effet: Bonus ou action
    if not exists (select tbonus_libc from bonus_type where tbonus_libc = v_bonus) then
    -- Ceci est une action à effet immédiat. On ignore pour le moment.
    else
      -- Un bonus. On met à jour la table bonus
      perform ajoute_bonus(ligne.perso_cod, v_bonus, v_duree, valeur);
    end if;

    -- On rajoute la ligne d’événements
    if strpos(v_texte_evt , '[cible]') != 0 then
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
    else
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
    end if;
  end loop;

  return 'OK ' || code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_generique(integer, text, text, text, text, numeric, integer, text) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_generique(integer, text, text, text, text, numeric, integer, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION deb_tour_generique(integer, text, text, text, text, numeric, integer, text) IS '(OBSOLÈTE) Ajoute des Bonus / Malus standards en fonction des paramètres donnés.';


--
-- Name: deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* deb_tour_generique                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = bonus (de la table bonus_type)          */
/*   $3 = valeur (Entier ou +/-nDy)               */
/*   $4 = distance (-1..n)                        */
/*   $5 = cibles (Type SAERTP)                    */
/*   $6 = cibles nombre, au format rôliste        */
/*   $7 = Probabilité d’atteindre chaque cible    */
/*   $8 = Durée de l’effet                        */
/*   $9 = Message d’événement associé             */
/**************************************************/
/* Créé le 5 Septembre 2007                       */
/* Modif le 22 mai 2014 (paramètre Durée)         */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_bonus alias for $2;
  v_valeur alias for $3;
  v_distance alias for $4;
  v_cibles_type alias for $5;
  v_cibles_nombre alias for $6;
  v_proba alias for $7;
  v_duree alias for $8;
  v_texte_evt alias for $9;

  -- initial data
  v_x_source integer; -- source X position
  v_y_source integer; -- source Y position
  v_et_source integer; -- etage de la source
  v_type_source integer; -- Type perso de la source
  v_cibles_nombre_max integer; -- Nombre calculé de cibles
  v_race_source integer; -- La race de la source
  v_position_source integer; -- Le pos_cod de la source
  v_bonus_texte text;

  -- Output and data holders
  ligne record;  -- Une ligne d’enregistrements
  i integer; -- Compteur de boucle
  ch character; -- Un caractère tout ce qu’il y a de plus banal
  valeur integer; -- Valeur numérique de l’impact du bonus ou de l’action
  -- Resistance magique
  v_bloque_magie integer;
  v_RM1 integer;
  compt integer;
  niveau_attaquant integer;
  v_seuil integer;
  code_retour text;

begin

  select tonbus_libelle || case when length(v_bonus)>3 then ' (cumulatif)' else '' end into v_bonus_texte from bonus_type where tbonus_libc = substr(v_bonus, 1, 3) ;

  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de «' || v_bonus_texte || '».';
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

  -- Et finalement on parcourt les cibles.
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con
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
    if v_cibles_type != 'S' then
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
        if floor(valeur / 2) <> 0 then
          valeur := floor(valeur / 2);
        elseif valeur < 0 then
          valeur := -1;
        else
          valeur := 1;
        end if; /*rajout Blade du cas ou valeur devient 0 à cause du floor*/
      end if;
    end if;

    code_retour := code_retour || '<br />Vous donnez un bonus/malus «' || v_bonus_texte || '» de force ' || valeur::text || ', pendant ' || v_duree::text || ' tours à ' || ligne.perso_nom;
    if v_bloque_magie = 1 then
      code_retour := code_retour || ' (résisté)';
    end if;
    code_retour := code_retour || '.';

    -- Création du Bonus
    perform ajoute_bonus(ligne.perso_cod, v_bonus, v_duree, valeur);

    -- On rajoute la ligne d’événements
    if strpos(v_texte_evt , '[cible]') != 0 then
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
    else
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
    end if;
  end loop;

  if code_retour = '' then
    code_retour := 'Aucune cible éligible pour le bonus/malus «' || v_bonus_texte || '»';
  end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text) IS '(OBSOLÈTE) Ajoute des Bonus / Malus standards en fonction des paramètres donnés.';


--
-- Name: deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* deb_tour_generique                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = bonus (de la table bonus_type)          */
/*   $3 = valeur (Entier ou +/-nDy)               */
/*   $4 = distance (-1..n)                        */
/*   $5 = cibles (Type SAERTPCO)                  */
/*   $6 = cibles nombre, au format rôliste        */
/*   $7 = Probabilité d’atteindre chaque cible    */
/*   $8 = Durée de l’effet                        */
/*   $9 = Message d’événement associé             */
/*   $10 = Perso ciblé                            */
/**************************************************/
/* Créé le 5 Septembre 2007                       */
/* Modif le 22 mai 2014 (paramètre Durée)         */
/* Modif le 10 juil 2014 (paramètre Perso ciblé)  */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_bonus alias for $2;
  v_valeur alias for $3;
  v_distance alias for $4;
  v_cibles_type alias for $5;
  v_cibles_nombre alias for $6;
  v_proba alias for $7;
  v_duree alias for $8;
  v_texte_evt alias for $9;
  v_cible_donnee alias for $10;

  -- initial data
  v_x_source integer;          -- source X position
  v_y_source integer;          -- source Y position
  v_et_source integer;         -- etage de la source
  v_type_source integer;       -- Type perso de la source
  v_cibles_nombre_max integer; -- Nombre calculé de cibles
  v_race_source integer;       -- La race de la source
  v_position_source integer;   -- Le pos_cod de la source
  v_cible_du_monstre integer;  -- La cible actuelle du monstre
  v_bonus_texte text;
  v_bonus_degressif text;

  -- Output and data holders
  ligne record;                -- Une ligne d’enregistrements
  i integer;                   -- Compteur de boucle
  ch character;                -- Un caractère tout ce qu’il y a de plus banal
  valeur integer;              -- Valeur numérique de l’impact du bonus ou de l’action
  -- Resistance magique
  v_bloque_magie integer;
  v_RM1 integer;
  compt integer;
  niveau_attaquant integer;
  v_seuil integer;
  code_retour text;

begin

  select  tonbus_libelle || case when length(v_bonus)>3 then ' (cumulatif)' else '' end,
          case when length(v_bonus)>3 then ' (dégressif)' else '' end
  into v_bonus_texte, v_bonus_degressif
  from bonus_type where tbonus_libc = substr(v_bonus, 1, 3) ;

  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de «' || v_bonus_texte || '».';
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

  -- Et finalement on parcourt les cibles.
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con
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
    if v_cibles_type != 'S' then
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
        if floor(valeur / 2) <> 0 then
          valeur := floor(valeur / 2);
        elseif valeur < 0 then
          valeur := -1;
        else
          valeur := 1;
        end if; /*rajout Blade du cas ou valeur devient 0 à cause du floor*/
      end if;
    end if;

    code_retour := code_retour || '<br />Vous donnez un bonus/malus «' || v_bonus_texte || '» de force ' || valeur::text || v_bonus_degressif || ', pendant ' || v_duree::text || ' tours à ' || ligne.perso_nom;
    if v_bloque_magie = 1 then
      code_retour := code_retour || ' (résisté)';
    end if;
    code_retour := code_retour || '.';

    -- Création du Bonus
    perform ajoute_bonus(ligne.perso_cod, v_bonus, v_duree, valeur);

    -- On rajoute la ligne d’événements
    if strpos(v_texte_evt , '[cible]') != 0 then
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
    else
      perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
    end if;
  end loop;

  if code_retour = '' then
    code_retour := 'Aucune cible éligible pour le bonus/malus «' || v_bonus_texte || '»';
  end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text, integer) OWNER TO delain;

--
-- Name: FUNCTION deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION deb_tour_generique(integer, text, text, integer, character, text, numeric, integer, text, integer) IS 'Ajoute des Bonus / Malus standards en fonction des paramètres donnés.';

