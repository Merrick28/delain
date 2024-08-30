--
-- Name: ea_ajoute_bm(integer, integer, text, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_ajoute_bm(integer, integer, text, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_ajoute_bm                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                            */
/*   $3 = soins/degats (Entier ou +/-nDy)               */
/*   $4 = distance (-1..n)                        */
/*   $5 = cibles (Type SAERTPCO)                  */
/*   $6 = cibles nombre, au format rôliste        */
/*   $7 = Probabilité d’atteindre chaque cible    */
/*   $8 = Message d’événement associé             */
/*   $9 = autres paramètres au format json       */
/**************************************************/
/* Créé le 18 Juin 2020                           */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_cible_donnee alias for $2;
  v_force alias for $3;
  v_distance alias for $4;
  v_cibles_type alias for $5;
  v_cibles_nombre alias for $6;
  v_proba alias for $7;
  v_texte_evt alias for $8;
  v_params alias for $9;

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
  v_source_nom text;           -- nom du perso source
  v_bonus text;
  v_bonus_degressif text;

  -- Output and data holders
  bm record;                   -- Une ligne d’enregistrements (pour explode json
  ligne record;                -- Une ligne d’enregistrements
  i integer;                   -- Compteur de boucle
  ch character;                -- Un caractère tout ce qu’il y a de plus banal
  v_pv integer;                -- Perte/Gain de PV (Soins ou Dégats)
  v_duree integer;             -- Valeur numérique de durée du BM
  v_cumulatif varchar(1);      -- O/N
  v_valeur integer;            -- Valeur numérique de l’impact du bonus ou de l’action
  valeur integer;              -- Valeur après test de resistance
  -- Resistance magique
  v_bloque_magie integer;
  v_RM1 integer;
  compt integer;
  v_niveau_attaquant integer;
  v_seuil integer;
  code_retour text;

  v_compagnon integer;         -- cod perso du familier si aventurier et de l'aventurier si familier
  v_distance_min integer;      -- distance minimum requis pour la cible
  v_exclure_porteur text;      -- le porteur et les compagnons sont inclus dans le ciblage
  v_equipage integer;          -- le partenaire d'équipage: cavalier/monture
  v_resistance text;           -- faire une test de resitance O/N

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de « Soins/Dégats et ajout de Bonus/Malus ».';
  end if;
  -- Initialisation des conteneurs
  code_retour := '';

  -- Position et type perso
  select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source, v_cible_du_monstre, v_source_nom, v_niveau_attaquant
    pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod, perso_cible, perso_nom, perso_niveau
  from perso_position, positions, perso
  where ppos_perso_cod = v_source
        and pos_cod = ppos_pos_cod
        and perso_cod = v_source;

  -- on recupère le code de son compagnon (0 si pas de compagnon)
  if v_type_source=1 then
  	select into v_compagnon pfam_familier_cod from perso_familier inner join perso on perso_cod = pfam_familier_cod where pfam_perso_cod = v_source and perso_actif = 'O';
  	if not found then
  	    v_compagnon:=0;
    end if;
  else
  	select into v_compagnon pfam_perso_cod from perso_familier inner join perso on perso_cod = pfam_perso_cod where pfam_familier_cod = v_source and perso_actif = 'O';
  	if not found then
  	    v_compagnon:=0;
    end if;
  end if;

  -- Cibles
  v_cibles_nombre_max := f_lit_des_roliste(v_cibles_nombre);

  -- test de reistance (Oui par defaut)
  v_resistance := COALESCE((v_params->>'fonc_trig_resistance')::text, 'O');

  -- Si le ciblage est limité par la VUE on ajuste la distance max
  if (v_params->>'fonc_trig_vue')::text = 'O' then
      v_distance := CASE WHEN  v_distance=-1 THEN distance_vue(v_source) ELSE LEAST(v_distance, distance_vue(v_source)) END ;
  end if;
  v_distance_min := CASE WHEN COALESCE((v_params->>'fonc_trig_min_portee')::text, '')='' THEN 0 ELSE ((v_params->>'fonc_trig_min_portee')::text)::integer END ;
  -- ciblage du porteur et de ses compagnons (familier/cavalier/monture)
  v_exclure_porteur := COALESCE((v_params->>'fonc_trig_exclure_porteur')::text, 'N')  ;
  v_equipage = COALESCE(f_perso_cavalier(v_source), COALESCE(f_perso_monture(v_source),0));
  if v_compagnon=0 and v_equipage != 0  then
      v_compagnon:=v_equipage ;
  end if;

  -- Et finalement on parcourt les cibles.
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, perso_pv, perso_pv_max
                from perso
                  inner join perso_position on ppos_perso_cod = perso_cod
                  inner join positions on pos_cod = ppos_pos_cod
                  left outer join lieu_position on lpos_pos_cod = pos_cod
                  left outer join lieu on lieu_cod = lpos_lieu_cod
                where perso_actif = 'O'
                      and perso_tangible = 'O'
                      -- À portée
                      and ((pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)) or v_distance=-1)
                      and ((pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)) or v_distance=-1)
                      and ((v_distance_min = 0) or (abs(pos_x-v_x_source) >= v_distance_min) or (abs(pos_y-v_y_source) >= v_distance_min))
                      and pos_etage = v_et_source
                      and ( trajectoire_vue(pos_cod, v_position_source) = '1' or (v_params->>'fonc_trig_vue')::text != 'O')
                      -- Hors refuge si on le souhaite
                      and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
                      -- cas d'exclusion du porteur d'ea et de ses compagnos
                      and (perso_cod not in (v_source, v_equipage, v_compagnon) or (v_exclure_porteur='N') or (v_cibles_type = 'S') )
                      -- Parmi les cibles spécifiées
                      and
                      ((v_cibles_type = 'S' and perso_cod = v_source) or
                       (v_cibles_type = 'A' and perso_type_perso!=2 and v_type_source!=2) or
                       (v_cibles_type = 'A' and perso_type_perso=2  and v_type_source=2) or
                       (v_cibles_type = 'E' and perso_type_perso!=2 and v_type_source=2) or
                       (v_cibles_type = 'E' and perso_type_perso=2  and v_type_source!=2) or
                       (v_cibles_type = 'R' and perso_race_cod = v_race_source) or
                       (v_cibles_type = 'V' and f_est_dans_la_liste(perso_race_cod, (v_params->>'fonc_trig_races')::json)) or
                       (v_cibles_type = 'J' and perso_type_perso = 1) or
                       (v_cibles_type = 'L' and perso_cod = v_compagnon) or
                       (v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
                       (v_cibles_type = 'C' and perso_cod = v_cible_donnee) or
                       (v_cibles_type = 'O' and perso_cod = v_cible_donnee) or
                       (v_cibles_type = 'M' and perso_cod = COALESCE(f_perso_cavalier(v_cible_donnee), COALESCE(f_perso_monture(v_cible_donnee),0))) or
                       (v_cibles_type = 'T'))
                -- Dans les limites autorisées
                order by random()
                limit v_cibles_nombre_max)
  loop
    -- On peut maintenant appliquer les bonus/malus sur une cible unique.

    -- Ajout azaghal on teste un simili resiste magie pour chaque personne cible sauf si la cible est le lanceur
    v_bloque_magie := 0;
    if v_cibles_type != 'S' and v_resistance != 'N' then

      -- on calcule le seuil de résistance (en fonction de l’int, la con le niv du sort et la marge de réussite
      v_RM1 := (ligne.perso_int * 5) + floor(ligne.perso_con / 10) + floor(ligne.perso_niveau / 2);
      compt := 30;
      compt := compt + (2 * v_niveau_attaquant);

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
      end if;

    end if;

    -- on applique des degats/soins    (en fonction de la force de l'effet et de la pseudo resistance)
    v_pv := f_lit_des_roliste(v_force);
    if v_bloque_magie = 1 and v_pv < 0 then
        v_pv := floor(v_pv / 2 ) ;
        if v_pv = 0 then
            v_pv = -1  ;
        end if;
    end if;

    if v_pv > 0 then
    		v_pv := LEAST(v_pv, ligne.perso_pv_max - ligne.perso_pv);
    		if v_pv > 0 then
            update perso set perso_pv = LEAST(perso_pv + v_pv, perso_pv_max) where perso_cod = ligne.perso_cod;
            code_retour := code_retour || '<br />'|| ligne.perso_nom || ' regagne  «' || v_pv::text || '» points de vie.' ;
        end if;
    elsif v_pv < 0 then
        -- attenuer les dégats eventullement en cas de PvP (le porteur de l'EA est un joueur et la cible aussi)
        v_pv := effectue_degats_perso(ligne.perso_cod, v_pv, v_source) ;

        code_retour := code_retour || '<br />'|| ligne.perso_nom || ' perd  «' || (-v_pv)::text || '» points de vie' ;
        if v_bloque_magie = 1 then
          code_retour := code_retour || ' (résisté)';
        end if;
        code_retour := code_retour || '.';

        -- On gère les dégâts
        if ligne.perso_pv + v_pv <= 0 then
          -- on a tué l’adversaire !!
          perform tue_perso_final(v_source, ligne.perso_cod);
          code_retour := code_retour || '<br />'|| ligne.perso_nom || ' a été tué par cette perte de point de vie!' ;
        else
          update perso set perso_pv = perso_pv + v_pv where perso_cod = ligne.perso_cod;
        end if;
    end if;


    -- boucle sur la liste des bonus à appliquer
    for bm in (select value from json_array_elements( (v_params->>'fonc_trig_effet_bm')::json )  )
    loop
        v_bonus := COALESCE(bm.value->>'tbonus_libc'::text, '') ;
        v_valeur := f_lit_des_roliste(bm.value->>'force'::text) ;
        v_duree := f_to_numeric(bm.value->>'duree'::text) ;
        v_cumulatif := COALESCE(bm.value->>'cumulatif'::text, 'N');

        select  tonbus_libelle || case when v_cumulatif='O' then ' (cumulatif)' else '' end, case when v_cumulatif='O' then ' (dégressif)' else '' end
            into v_bonus_texte, v_bonus_degressif
        from bonus_type where tbonus_libc = v_bonus and v_valeur!=0 and v_duree!=0  ;

        if found then

            if v_bloque_magie = 1 then
                valeur := floor(v_valeur / 2 ) ;
                if valeur = 0 then
                    v_valeur = sign(v_valeur) ;
                end if;
            else
                valeur = v_valeur ;
            end if;

            code_retour := code_retour || '<br />'|| ligne.perso_nom || ' prend un bonus/malus «' || v_bonus_texte || '» de force ' || valeur::text || v_bonus_degressif || ', pendant ' || v_duree::text || ' tours' ;
            if v_bloque_magie = 1 then
              code_retour := code_retour || ' (résisté)';
            end if;
            code_retour := code_retour || '.';

            -- Création du Bonus
            perform ajoute_bonus(ligne.perso_cod, CASE WHEN v_cumulatif='O' THEN v_bonus||'+' ELSE v_bonus END, v_duree, valeur);

        end if;
    end loop;

    -- On rajoute la ligne d’événements
    if v_texte_evt != '' then
        if strpos(v_texte_evt , '[cible]') != 0 then
          perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
        else
          perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
        end if;
    end if;

  end loop;

  -- if code_retour = '' then
  --   code_retour := 'Aucune cible éligible pour « Soins/Dégats et ajout de Bonus/Malus »';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_ajoute_bm(integer, integer, text, integer, character, text, numeric, text, json) OWNER TO delain;

--
-- Name: FUNCTION ea_ajoute_bm(integer, integer, text, integer, character, text, numeric, text, json); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ea_ajoute_bm(integer, integer, text, integer, character, text, numeric, text, json) IS 'Applique des Soins/Dégats et ajoute multiple Bonus / Malus en fonction des paramètres donnés.';

