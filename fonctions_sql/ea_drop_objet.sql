--
-- Name: ea_drop_objet(integer, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_drop_objet(integer, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_drop_objet                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = cibles                                  */
/*   $3 = type de cibles                         */
/*   $4 = cibles nombre de drop                   */
/*   $5 = proba                                   */
/*   $6 = Message d’événement associé             */
/*   $7 = Paramètre additionnels                  */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_cible_donnee alias for $2;
  v_cibles_type alias for $3;
  v_nombre alias for $4;
  v_proba alias for $5;
  v_texte_evt alias for $6;
  v_params alias for $7;

  v_distance integer;

  v_x_source integer;          -- source X position
  v_y_source integer;          -- source Y position
  v_et_source integer;         -- etage de la source
  v_type_source integer;       -- Type perso de la source
  v_nombre_max integer;        -- Nombre calculé de cibles
  v_race_source integer;       -- La race de la source
  v_position_source integer;   -- Le pos_cod de la source
  v_cible_du_monstre integer;  -- La cible actuelle du monstre
  v_source_nom text;           -- nom du perso source
  v_type_drop integer;         -- type de drop sol/pied/inventaire
  v_position_cible integer;    -- Le pos_cod de la cible
  v_cible_cod integer;         -- code perso ciblé
  v_cible_nom text;            -- nom du perso ciblé
  v_cible integer;             -- Perso réllement ciblé (la source si pas de drop special, la cible sinon)
  v_compagnon integer;         -- cod perso du familier si aventurier et de l'aventurier si familier

  v_gobj_cod integer ;         -- objet generique à droper !
  i integer;                   -- Compteur de boucle
  -- Output and data holders
  code_retour text;


begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « drop objet ».';
    return '';
  end if;
  -- Initialisation des conteneurs
  code_retour := '';

  -- Position et type perso Source de l'EA
  select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source, v_cible_du_monstre, v_source_nom
    pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod, perso_cible, perso_nom
  from perso_position, positions, perso
  where ppos_perso_cod = v_source
        and pos_cod = ppos_pos_cod
        and perso_cod = v_source;

  -- on recupère le code de son compagnon de la source (0 si pas de compagnon)
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

  -- s'il n'y a pas de cible donnée, la cible est le perso lui-même
  v_cible_donnee := COALESCE(v_cible_donnee, v_source);

  -- rechercher la vrai cible en fonction du type de cible demandeé!
  select into v_cible_cod, v_position_cible, v_cible_nom
        perso_cod, pos_cod, perso_nom
                from perso
                  inner join perso_position on ppos_perso_cod = perso_cod
                  inner join positions on pos_cod = ppos_pos_cod
                  left outer join lieu_position on lpos_pos_cod = pos_cod
                  left outer join lieu on lieu_cod = lpos_lieu_cod
                where perso_actif = 'O'
                      and perso_tangible = 'O'
                      -- au même etage
                      and pos_etage = v_et_source
                      -- en vue
                      and ( trajectoire_vue(pos_cod, v_position_source) = '1' )
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
                limit 1 ;

  -- type de drop 0= sol, 1 pied de la cible, 2 inventaire de la cible
  v_type_drop := CASE WHEN COALESCE((v_params->>'fonc_trig_pos')::text, '')='' THEN 0 ELSE ((v_params->>'fonc_trig_pos')::text)::integer END ;

  -- Cibles = nombre d'objet ici !
  v_nombre_max := f_lit_des_roliste(v_nombre);
  i:= 0 ;

  -- Et finalement on parcourt les drops
  while i < v_nombre_max
  loop
      i := i + 1;   -- compteur +1

      v_gobj_cod = f_tirage_aleatoire_liste('gobj_cod', 'taux', (v_params->>'fonc_trig_objet')::json) ;


      if v_gobj_cod > 0 then


          if v_type_drop = 1 THEN

              -- drop advance: aux pieds de la cible de l'EA
              v_cible := v_cible_cod ;
              perform cree_objet_pos(v_gobj_cod, v_position_cible);
              code_retour := code_retour|| '<br />' || v_source_nom || ' a fait tombé un objet aux pieds de ' || v_cible_nom || '.';

          elsif v_type_drop = 2 THEN

              -- drop advance: dans l'inventaire de la cible de l'EA
              v_cible := v_cible_cod ;
              perform cree_objet_perso(v_gobj_cod, v_cible_cod);
              code_retour := code_retour|| '<br />' || v_source_nom || ' a mis un objet dans l’inventaire de ' || v_cible_nom || '.';

          else

              -- drop usuel: au pied du monstre avec l'EA (cas par défaut)
              v_cible := v_source ;
              perform cree_objet_pos(v_gobj_cod, v_position_source);
              code_retour := code_retour|| '<br />' || v_source_nom || ' a perdu un objet qui est tombé au sol.';

          end if;

          -- On rajoute la ligne d’événements
          if v_texte_evt != '' then
              if strpos(v_texte_evt , '[cible]') != 0 then
                perform insere_evenement(v_source, v_cible, 54, v_texte_evt, 'O', 'N', null);
              else
                perform insere_evenement(v_source, v_cible, 54, v_texte_evt, 'O', 'O', null);
              end if;
          end if;

      end if;

  end loop;

  -- if code_retour = '' then
  --   code_retour := 'Pas d’effet automatique de « drop objet »!';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_drop_objet(integer, integer, character, text, numeric, text, json) OWNER TO delain;

--
-- Name: FUNCTION ea_drop_objet(integer, integer, character, text, numeric, text, json); Type: COMMENT; Schema: public; Owner: delain
--
