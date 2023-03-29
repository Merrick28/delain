--
-- Name: ea_glissade(integer, integer, text, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_glissade(integer, integer, text, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_glissade                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                             */
/*   $3 = force de projection(format dé roliste)  */
/*   $4 = distance (-1..n)                        */
/*   $5 = cibles (Type SAERTPCO)                  */
/*   $6 = cibles nombre, au format rôliste        */
/*   $7 = Probabilité de déclenchement            */
/*   $8 = Message d’événement associé             */
/*   $9 = Paramètre additionnels                  */
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
  v_x_ancien integer;          -- ancien source X position
  v_y_ancien integer;          -- source Y position
  v_et_source integer;         -- etage de la source
  v_type_source integer;       -- Type perso de la source
  v_race_source integer;       -- La race de la source
  v_position_source integer;   -- Le pos_cod de la source
  v_cible_du_monstre integer;  -- La cible actuelle du monstre
  v_source_nom text;           -- nom du perso source
  v_dist_glisse integer;         -- distance de projection
  v_pos_glissade integer;    -- la case sur laquelle la cible atterrie
	v_perso_fam integer;		     -- Familier de la cible
	v_degats_portes integer;		 -- dégat de la projection
	v_event_txt text;	           -- pour autres evenements
  v_niveau_attaquant integer;  -- Resistance magique
  v_type_perso integer;
  v_perso_monture integer;
  code_retour text;

  v_pattern_glisse text;
  v_pattern_direction text;
  v_direction_pente varchar(2);   -- direction pente au format texte (N, NE, E, etc..)
  v_pente integer ;
  v_dir_pente integer ;         -- direction pente au format integer (0=N, 1=NE, 3=E, etc..)
  v_dir_perso integer ;         -- direction perso au format integer (0=N, 1=NE, 3=E, etc..)
  v_tirage integer ;            -- tirage au sort du sens de glissage
  v_dir_glisse integer ;        -- direction -1 si pas de glisse sinon direction au format integer (0=N, 1=NE, 3=E, etc..)
  v_temp integer ;              -- variable poubelle

  ligne record;                 -- Une ligne d’enregistrements
  v_taux numeric;                 -- taux des case de glisses

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « glissade ».';
    return '';
  end if;

  if coalesce(v_params->>'ancien_pos_cod'::text, '') = '' then
    return ''; -- la glissade ne fonctionne qu'avec un sens de glisse (donc sur deplacement du perso, y compris projection, etc...)
  end if;

  select pos_x, pos_y into v_x_ancien, v_y_ancien from positions where pos_cod = coalesce( f_to_numeric(v_params->>'ancien_pos_cod'), 0) ;
  if not found then
      return ''; -- probleme l'ancienne position du perso est inconnue
  end if;

  v_dist_glisse :=  coalesce( f_to_numeric(v_params->>'glissade_distance'), 0) ;
  if v_dist_glisse > 0 then
    return ''; -- la glissade ne fonctionne que si on c'est déplacé autrement qu'en glissant (on n'enchaine pas les glissades)
  end if;

  -- Initialisation des conteneurs
  code_retour := '';

  -- Position et type perso
  select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source, v_cible_du_monstre, v_source_nom, v_niveau_attaquant, v_type_perso, v_perso_monture
    pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod, perso_cible, perso_nom, perso_niveau, perso_type_perso, coalesce(perso_monture,0)
  from perso_position, positions, perso
  where ppos_perso_cod = v_source
        and pos_cod = ppos_pos_cod
        and perso_cod = v_source;

  if v_type_perso=1 and v_perso_monture > 0 then
      return ''; -- si le perso est sur une monture c'est la monture qui glisse, le perso n'est pas impacté par la glissade
  end if;


  -- calcul e la direction de déplacement
  if v_y_source > v_y_ancien then    -- cadran nord
      if v_x_source > v_x_ancien then
          v_dir_perso = 1 ;   -- nord/est
      elseif v_x_source < v_x_ancien then
          v_dir_perso = 7 ;   -- nord/ouest
      else
          v_dir_perso = 0 ;     -- nord
      end if;
  elseif v_y_source < v_y_ancien then -- cadran sud
      if v_x_source > v_x_ancien then
          v_dir_perso = 3 ;   -- sud/est
      elseif v_x_source < v_x_ancien then
          v_dir_perso = 5 ;   -- sud/ouest
      else
          v_dir_perso = 4 ;     --sud
      end if;
  else  -- cadran horizontal
      if v_x_source > v_x_ancien then
          v_dir_perso = 2 ;   -- est
      elseif v_x_source < v_x_ancien then
          v_dir_perso = 6 ;   -- ouest
      else
          v_dir_perso = -1 ;     -- le perso n'a pas bougé ?
      end if;
  end if;

  if v_dir_perso = -1 then
    return ''; -- calcul de la direction de déplacement impossible, donc on touche à rien !
  end if;

  -- on a tout ce qui faut, on fait calcul la "glissade"
  v_pattern_glisse :=  coalesce(v_params->>'fonc_trig_glisse'::text, '') ;
  v_pattern_direction  :=  coalesce(v_params->>'fonc_trig_direction'::text, '') ;
  v_direction_pente :=  coalesce(v_params->>'fonc_trig_cardinal'::text, 'N') ;
  v_pente := coalesce( f_to_numeric(v_params->>'fonc_trig_pente'), 0) ;
  v_dir_pente := CASE WHEN v_direction_pente='NE' then 1	WHEN v_direction_pente='E' then 2	WHEN v_direction_pente='SE' then 3	WHEN v_direction_pente='S' then 4	WHEN v_direction_pente='SO' then 5	WHEN v_direction_pente='O' then 6	WHEN v_direction_pente='NO' then 7  ELSE 0 END ;

  -- tirage aléatoire
  v_tirage := floor(random()*100)::integer ;
  v_dir_glisse := -1 ;
  v_taux:= 0 ;

  -- calcul des chances suivant le pattern de glisse et tirage
  FOR ligne IN (
      SELECT d, floor(g1*pdir/100 + g2*pente/100)::integer as glisse
      FROM (
        SELECT d::integer, TO_NUMBER(g1, '999') as g1, TO_NUMBER(g2, '999') as g2, pdir, pente
        FROM unnest ( string_to_array('0,1,2,3,4,5,6,7', ',')) as a(d)
        JOIN unnest ( string_to_array('0,1,2,3,4,5,6,7', ','), string_to_array(v_pattern_glisse, ',')) as b(o01,g1) on (((o01::integer)+v_dir_perso) %8) = d
        JOIN unnest ( string_to_array('0,1,2,3,4,5,6,7', ','), string_to_array(v_pattern_glisse, ',')) as c(o02,g2) on  (((o02::integer)+v_dir_pente) %8) =  d
        JOIN   (
            SELECT TO_NUMBER(g,999) as pdir, (100-TO_NUMBER(g,999))*v_pente/100 as pente  from unnest ( string_to_array('0,1,2,3,4,5,6,7', ','), string_to_array(v_pattern_direction, ',') )  as pp(o,g) where o=v_dir_perso
          ) as p on true
      ) as patterns
      ORDER BY d )
  loop
      -- boucle pour trouver la case désignée par v_tirage en fonction des % de chances
      v_taux := v_taux + ligne.glisse ;
      if v_dir_glisse = -1 and v_tirage <= v_taux then
          v_dir_glisse := ligne.d ;
      end if;
      -- code_retour := code_retour || '<br>v_taux=' || v_taux::text || ' v_dir_glisse=' || v_dir_glisse::text ;
  end loop;

  -- code_retour := code_retour || '<br>glisse=' || v_pattern_glisse || ' direction=' || v_pattern_direction || ' v_direction_pente=' || v_direction_pente || ' v_pente=' || v_pente::text ;
  -- code_retour := code_retour || '<br>v_tirage=' || v_tirage::text || ' v_dir_glisse=' || v_dir_glisse::text ;


  -- calcul des coordonée de la position glissée
  if v_dir_glisse = 0 then -- nord
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source and pos_y = v_y_source + 1 ;
  elseif v_dir_glisse = 1 then -- nord/est
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source + 1 and pos_y = v_y_source + 1 ;
  elseif v_dir_glisse = 2 then -- est
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source + 1 and pos_y = v_y_source ;
  elseif v_dir_glisse = 3 then -- sud/est
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source + 1 and pos_y = v_y_source - 1;
  elseif v_dir_glisse = 4 then -- sud
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source and pos_y = v_y_source - 1 ;
  elseif v_dir_glisse = 5 then -- sud/ouest
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source -1 and pos_y = v_y_source - 1 ;
  elseif v_dir_glisse = 6 then -- ouest
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source -1 and pos_y = v_y_source ;
  elseif v_dir_glisse = 7 then -- nord/ouest
      select pos_cod into v_pos_glissade from positions where pos_etage = v_et_source and pos_x = v_x_source -1 and pos_y = v_y_source + 1 ;
  else
      v_pos_glissade := v_position_source ;
  end if;
  v_pos_glissade := coalesce (v_pos_glissade, v_position_source);    -- au cas ou position non-trouvé


  -- s'assurer que la position est valide (on a pas glissé dans pas un mur)
  if v_pos_glissade != v_position_source then
      select mur_pos_cod into v_temp from murs where mur_pos_cod = v_pos_glissade and mur_illusion = 'N' ;
      if found then
          v_pos_glissade := v_position_source ;
          code_retour := code_retour || '<br />La glissade de ' || v_source_nom || ' a été arrêtée par un mur.' ;
      end if;
  end if;

  if v_pos_glissade != v_position_source then

          code_retour := code_retour || '<br />'|| ' « glissade »  de ' || v_source_nom || '.' ;

          v_dist_glisse := v_dist_glisse + 1;   -- pour gérer la distance de glisse

          -- déplacer la cible (et eventuellement son familier avec lui)
          update perso_position set ppos_pos_cod = v_pos_glissade where ppos_perso_cod = v_source;
          delete from lock_combat where lock_cible = v_source;
          delete from lock_combat where lock_attaquant = v_source;
          delete from riposte where riposte_attaquant =v_source;
          delete from transaction	where tran_vendeur = v_source;
          delete from transaction	where tran_acheteur = v_source;
          select into v_perso_fam max(pfam_familier_cod) from perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE perso_actif='O' and pfam_perso_cod=v_source;
          if found then
              update perso_position	set ppos_pos_cod = v_pos_glissade where ppos_perso_cod = v_perso_fam;
              delete from lock_combat where lock_cible = v_perso_fam;
              delete from lock_combat where lock_attaquant = v_perso_fam;
              delete from riposte where riposte_attaquant = v_perso_fam;
              delete from transaction	where tran_vendeur = v_perso_fam;
              delete from transaction	where tran_acheteur = v_perso_fam;
          end if;

        -- On rajoute la ligne d’événements
        if v_texte_evt != '' then
            if strpos(v_texte_evt , '[cible]') != 0 then
              perform insere_evenement(v_source, v_source, 54, v_texte_evt, 'O', 'N', null);
            else
              perform insere_evenement(v_source, v_source, 54, v_texte_evt, 'O', 'O', null);
            end if;
        end if;

        ---------------------------
        -- les EA liés au déplacement (la glissade est considéré comme un déplacement si la cible a glissé ailleurs que sur sa case d'origine)
        ---------------------------
        code_retour := code_retour || execute_fonctions(v_source, v_source, 'DEP', json_build_object('glissade_distance',v_dist_glisse,'ancien_pos_cod',v_position_source,'ancien_etage',v_et_source, 'nouveau_pos_cod',v_pos_glissade,'nouveau_etage',v_et_source)) ;

  end if;



  -- if code_retour = '' then
  --   code_retour := 'Aucune cible éligible pour « projection/attraction »';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_glissade(integer, integer, text, integer, character, text, numeric, text, json) OWNER TO delain;

