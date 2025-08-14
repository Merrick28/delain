--
-- Name: ea_saut_sur_cible(integer, integer, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_saut_sur_case(integer, integer, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_saut_sur_cible                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                             */
/*   $3 = distance (-1..n)                        */
/*   $4 = cibles (Type SAERTPCO)                  */
/*   $5 = nombre de cible pour les degats         */
/*   $6 = Probabilité de déclenchement            */
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
  v_x_dest integer;          -- dest X position
  v_y_dest integer;          -- dest Y position
  v_et_source integer;         -- etage de la source
  v_type_source integer;       -- Type perso de la source
  v_cibles_nombre_max integer; -- Nombre calculé de cibles
  v_race_source integer;       -- La race de la source
  v_position_source integer;   -- Le pos_cod de la source
  v_cible_du_monstre integer;  -- La cible actuelle du monstre
  v_source_nom text;           -- nom du perso source
  v_dist_proj integer;         -- distance de projection
  v_pos_projection integer;    -- la case sur laquelle la cible atterrie
  v_perso_fam integer;		     -- Familier de la cible
  v_degats_portes integer;		 -- dégat de la projection
  v_event_txt text;	           -- pour autres evenements
  -- Output and data holders
  ligne record;                -- Une ligne d’enregistrements
  i integer;                   -- Compteur de boucle
  v_niveau_attaquant integer;  -- Resistance magique
  code_retour text;

  v_compagnon integer;         -- cod perso du familier si aventurier et de l'aventurier si familier
  v_distance_min integer;      -- distance minimum requis pour la cible

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « saut sur case ».';
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

  -- Cibles
  v_cibles_nombre_max = f_lit_des_roliste(v_cibles_nombre); -- nombre de cibles pour les degats sur la case

  -- Si le ciblage est limité par la VUE on ajuste la distance max
  if (v_params->>'fonc_trig_vue')::text = 'O' then
      v_distance := CASE WHEN  v_distance=-1 THEN distance_vue(v_source) ELSE LEAST(v_distance, distance_vue(v_source)) END ;
  end if;
  v_distance_min := CASE WHEN COALESCE((v_params->>'fonc_trig_min_portee')::text, '')='' THEN 0 ELSE ((v_params->>'fonc_trig_min_portee')::text)::integer END ;

  -- on cherche d'abord la case à portée (et eventuellement en vue) sur laquelle se deplacer.
  select pos_cod, pos_x, pos_y into v_pos_projection, v_x_dest, v_y_dest
    from positions left join murs on mur_pos_cod=pos_cod
    where mur_pos_cod is null
      and ((pos_x between (v_x_source - v_distance) and (v_x_source + v_distance)) or v_distance=-1)
      and ((pos_y between (v_y_source - v_distance) and (v_y_source + v_distance)) or v_distance=-1)
      and ((v_distance_min = 0) or (abs(pos_x-v_x_source) >= v_distance_min) or (abs(pos_y-v_y_source) >= v_distance_min))
      and pos_etage = v_et_source
      and ( trajectoire_vue(pos_cod, v_position_source) = '1' or (v_params->>'fonc_trig_vue')::text != 'O')
      and pos_cod <> v_position_source
    order by random() limit 1 ;
  if not found then
    return '';      -- echec pas de case trouvée!!
  end if;

  -- On peut maintenant deplacer le porteur de l'EA sur la case trouvée.
  code_retour := code_retour || '<br />'|| ' « saut »  sur case X=' || v_x_dest::text || ' Y=' || v_y_dest::text || '.' ;

      -- on réalise le bond sur la case (pour la source de l'EA et eventuellement son familier)!
  update perso_position set ppos_pos_cod = v_pos_projection where ppos_perso_cod = v_source ;
  delete from lock_combat where lock_cible = v_source;
  delete from lock_combat where lock_attaquant = v_source;
  delete from riposte where riposte_attaquant = v_source;
  delete from transaction	where tran_vendeur = v_source;
  delete from transaction	where tran_acheteur = v_source;
  select into v_perso_fam max(pfam_familier_cod) from perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE perso_actif='O' and pfam_perso_cod=v_source;
  if found then
    update perso_position set ppos_pos_cod = v_pos_projection where ppos_perso_cod = v_perso_fam;
    delete from lock_combat where lock_cible = v_perso_fam;
    delete from lock_combat where lock_attaquant = v_perso_fam;
    delete from riposte where riposte_attaquant = v_perso_fam;
    delete from transaction	where tran_vendeur = v_perso_fam;
    delete from transaction	where tran_acheteur = v_perso_fam;
  end if;

  -- ajouter un evenement "generique" pour le porteur
  perform insere_evenement(v_source, v_source, 54, '[attaquant] a sauté sur case X=' || v_x_dest::text || ' Y=' || v_y_dest::text || '.', 'O', 'O', null);

  -- Et finalement on parcours les cibles sur la case du saut pour effectuer des degats
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, pos_cod, perso_pv
                from perso
                  inner join perso_position on ppos_perso_cod = perso_cod
                  inner join positions on pos_cod = ppos_pos_cod
                  left outer join lieu_position on lpos_pos_cod = pos_cod
                  left outer join lieu on lieu_cod = lpos_lieu_cod
                where perso_actif = 'O'
                      and perso_tangible = 'O'
                      -- sur la case du saut
                      and pos_cod =  v_pos_projection
                      -- Hors refuge si on le souhaite
                      and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
                      -- Parmi les cibles spécifiées (on retire le "S" pour ne pas s'auto infliger des degats
                      and
                      (-- (v_cibles_type = 'S' and perso_cod = v_source) or
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

      -- appliquer les dégats
      IF COALESCE(v_params->>'fonc_trig_degats'::text,'') != '' then

          v_degats_portes := f_lit_des_roliste(v_params->>'fonc_trig_degats'::text);

          if v_degats_portes > 0 then
              if v_degats_portes >= ligne.perso_pv then -- la cible a été tuée......
                    code_retour := code_retour || '<br />' || v_source_nom || ' a bondi sur ' ||ligne.perso_nom || ' lui faisant subir '||trim(to_char(v_degats_portes,'9999'))||' points de dégats, et le tuant sur le coup !';

                    /* evts pour coup porté */
                    v_event_txt := '[attaquant] a bondi sur [cible] lui infligeant '||trim(to_char(v_degats_portes,'9999'))||' points de dégats, le tuant sur le coup !';
                    perform insere_evenement(v_source, ligne.perso_cod, 66, v_event_txt, 'O', 'N', null);

                    code_retour := code_retour || tue_perso_final(v_source,ligne.perso_cod);

              else
                    code_retour := code_retour|| '<br />' || v_source_nom || ' a bondi sur ' || ligne.perso_nom || ' lui faisant subir '||trim(to_char(v_degats_portes,'9999'))||' points de dégats.';

                    /* evts pour coup porté */
                    v_event_txt := '[attaquant] a bondi sur [cible] lui infligeant '||trim(to_char(v_degats_portes,'9999'))||' points de dégats.';
                    perform insere_evenement(v_source, ligne.perso_cod, 66, v_event_txt, 'O', 'N', null);

                    update perso set perso_pv = perso_pv - v_degats_portes where perso_cod = ligne.perso_cod;

              end if;
          end if;
      END IF;

      -- On rajoute la ligne d’événements
      if v_texte_evt != '' then
          if strpos(v_texte_evt , '[cible]') != 0 then
            perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
          else
            perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
          end if;
      end if;

  end loop;

  ---------------------------
  -- les EA liés au déplacement (le saut sur case est considéré comme un déplacement)
  ---------------------------
  code_retour := code_retour || execute_fonctions(v_source, v_source, 'DEP', json_build_object('ancien_pos_cod',v_position_source,'ancien_etage',v_et_source, 'nouveau_pos_cod',v_pos_projection,'nouveau_etage',v_et_source)) ;


  -- if code_retour = '' then
  --   code_retour := 'Aucune cible éligible pour « saut sur cible »';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_saut_sur_case(integer, integer, integer, character, text, numeric, text, json) OWNER TO delain;

