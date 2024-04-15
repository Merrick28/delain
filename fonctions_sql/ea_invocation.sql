--
-- Name: ea_invocation(integer, integer, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_invocation(integer, integer, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_invocation                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                             */
/*   $3 = distance (-1..n)                        */
/*   $4 = cibles (Type SAERTPCO)                  */
/*   $5 = cibles nombre, au format rôliste        */
/*   $6 = Probabilité d’atteindre chaque cible    */
/*   $7 = Message d’événement associé             */
/*   $8 = Paramètre additionnels                  */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_cible_donnee alias for $2;
  v_distance alias for $3;
  v_cibles_type alias for $4;
  v_cibles_nombre alias for $5;
  v_proba alias for $6;
  v_texte_evt alias for $7;
  v_params alias for $8;

  -- initial data
  v_x_source integer;          -- source X position
  v_y_source integer;          -- source Y position
  v_et_source integer;         -- etage de la source
  v_type_source integer;       -- Type perso de la source
  v_cibles_nombre_max integer; -- Nombre calculé de cibles
  v_race_source integer;       -- La race de la source
  v_position_source integer;   -- Le pos_cod de la source
  v_cible_du_monstre integer;  -- La cible actuelle du monstre
  v_source_nom text;           -- nom du perso source
 	v_event_txt text;	           -- pour autres evenements
 	v_gmon_cod integer;          -- monstre genrique a invoquer
 	v_pos_cod integer;           -- la case ciblé
	v_cod_monstre integer;       -- cod du monstre invoqué
	v_nom_monstre text;          -- nom du monstre invoqué
  -- Output and data holders
  ligne record;                -- Une ligne d’enregistrements
  i integer;                   -- Compteur de boucle
  v_niveau_attaquant integer;  -- Resistance magique
  code_retour text;

  v_compagnon integer;         -- cod perso du familier si aventurier et de l'aventurier si familier
  v_distance_min integer;      -- distance minimum requis pour la cible
  v_exclure_porteur text;      -- le porteur et les compagnons sont inclus dans le ciblage
  v_equipage integer;          -- le partenaire d'équipage: cavalier/monture

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « invocation ».';
    return '';
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

  -- Cibles = nombre de monstre à poper
  v_cibles_nombre_max := f_lit_des_roliste(v_cibles_nombre);

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

  -- Compteur de boucle!
  i:= 0 ;

  -- Et finalement on parcourt la liste des monstres generiques
  while i < v_cibles_nombre_max
  loop
      i := i + 1;   -- compteur +1

      v_gmon_cod = f_tirage_aleatoire_liste('gmon_cod', 'taux', (v_params->>'fonc_trig_monstre')::json) ;

      -- pour poper le monstre on cherche une case où le faire apparaitre (en fonction du ciblage)
      if v_gmon_cod > 0 then

          v_pos_cod := null ; -- par defaut on ne sait pas où cibler!
          if v_cibles_type != 'X' then

              select into v_pos_cod pos_cod
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
              order by random()
              limit 1;
          end if;

          -- si on a pas trouvé de case cible ou si cible "X" alors on cherche une case à portée
          -- Marlyza - 2024-04-15 : empecher l'invocation dans un mur
          if v_pos_cod is null then
              select into v_pos_cod pos_cod
              from positions
                left outer join murs on  mur_pos_cod=pos_cod
              where     ((pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)) or v_distance=-1)
                    and ((pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)) or v_distance=-1)
                    and ((v_distance_min = 0) or (abs(pos_x-v_x_source) >= v_distance_min) or (abs(pos_y-v_y_source) >= v_distance_min))
                    and pos_etage = v_et_source
                    and ( trajectoire_vue(pos_cod, v_position_source) = '1' or (v_params->>'fonc_trig_vue')::text != 'O')
                    and mur_pos_cod is null
              order by random()
              limit 1;
          end if;

          -- si on trouvé une case cible on fait l'invocation
          if v_pos_cod is not null then

          	  select into v_nom_monstre gmon_nom from monstre_generique where gmon_cod = v_gmon_cod ;
              code_retour := code_retour|| '<br />Invocation de ' || v_nom_monstre || '.';
              v_cod_monstre := cree_monstre_pos(v_gmon_cod, v_pos_cod);

              -- On rajoute la ligne d’événements
              if v_texte_evt != '' then
                  if strpos(v_texte_evt , '[cible]') != 0 then
                    perform insere_evenement(v_source, v_cod_monstre, 54, v_texte_evt, 'O', 'N', null);
                  else
                    perform insere_evenement(v_source, v_cod_monstre, 54, v_texte_evt, 'O', 'O', null);
                  end if;
              end if;

          end if;

      end if;

  end loop;

  -- if code_retour = '' then
  --   code_retour := 'Aucune cible éligible pour « invocation »';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_invocation(integer, integer, integer, character, text, numeric, text, json) OWNER TO delain;

