--
-- Name: ea_lance_sort(integer, integer, text, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_lance_sort(integer, integer, text, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_lance_sort                                 */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                             */
/*   $3 = bonus (de la table bonus_type)          */
/*   $4 = distance (-1..n)                        */
/*   $5 = cibles (Type SAERTPCO)                  */
/*   $6 = cibles nombre, au format rôliste        */
/*   $7 = Probabilité d’atteindre chaque cible    */
/*   $8 = Message d’événement associé             */
/*   $9 = autres paramètres au format json        */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_cible_donnee alias for $2;
  v_sort_cod alias for $3;
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
  v_sort_texte text;           -- le nom du sort
  v_sort_fonction text;        -- la fonction a executer associé au sort
  v_sort_retour text;          -- le texte de retour du sort appelé
	v_fonction text;			       -- fonction à lancer  avec les paramètres
  v_sort_case text;            -- 'O' sir le sort est un sort qui cible une case
  v_source_nom text;           -- nom du perso source

  -- Output and data holders
  ligne record;                -- Une ligne d’enregistrements
  i integer;                   -- Compteur de boucle
  -- Resistance magique
  niveau_attaquant integer;
  code_retour text;

  v_compagnon integer;        -- cod perso du familier si aventurier et de l'aventurier si familier

begin

  -- texte et définition du sort
  select sort_nom, sort_case, case when exists(select * from pg_proc where proname = 'nv_'||sort_fonction) then 'nv_'||sort_fonction else 'dv_'||sort_fonction end
    into v_sort_texte, v_sort_case, v_sort_fonction from sorts where sort_cod = v_sort_cod ;

  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de «' || v_sort_texte || '».';
  end if;
  -- Initialisation des conteneurs
  code_retour := '';

  -- Position et type perso
  select into v_x_source, v_y_source, v_et_source, v_type_source, v_race_source, v_position_source, v_cible_du_monstre, v_source_nom
    pos_x, pos_y, pos_etage, perso_type_perso, perso_race_cod, pos_cod, perso_cible, perso_nom
  from perso_position, positions, perso
  where ppos_perso_cod = v_source
        and pos_cod = ppos_pos_cod
        and perso_cod = v_source;

  -- on récupère les données de l’attaquant (utilisé dans le calcul de résistance)
  select into niveau_attaquant perso_niveau
  from perso
  where perso_cod = v_source;

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

  -- Si le ciblage est limité par la VUE on ajuste la distance max
  if (v_params->>'fonc_trig_vue')::text = 'O' then
      v_distance := CASE WHEN  v_distance=-1 THEN distance_vue(v_source) ELSE LEAST(v_distance, distance_vue(v_source)) END ;
  end if;

  -- Et finalement on parcourt les cibles.
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, pos_cod
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
                      and pos_etage = v_et_source
                      and ( trajectoire_vue(pos_cod, v_position_source) = '1' or (v_params->>'fonc_trig_vue')::text != 'O')
                      -- Hors refuge si on le souhaite
                      and (v_cibles_type = 'P' or coalesce(lieu_refuge, 'N') = 'N')
                      -- Parmi les cibles spécifiées
                      and
                      ((v_cibles_type = 'S' and perso_cod = v_source) or
                       (v_cibles_type = 'A' and perso_type_perso = v_type_source) or
                       (v_cibles_type = 'E' and perso_type_perso != v_type_source) or
                       (v_cibles_type = 'R' and perso_race_cod = v_race_source) or
                       (v_cibles_type = 'V' and f_est_dans_la_liste(perso_race_cod, (v_params->>'fonc_trig_races')::json)) or
                       (v_cibles_type = 'J' and perso_type_perso = 1) or
                       (v_cibles_type = 'L' and perso_cod = v_compagnon) or
                       (v_cibles_type = 'P' and perso_type_perso in (1, 3)) or
                       (v_cibles_type = 'C' and perso_cod = v_cible_donnee) or
                       (v_cibles_type = 'O' and perso_cod = v_cible_donnee) or
                       (v_cibles_type = 'T'))
                -- Dans les limites autorisées
                order by random()
                limit v_cibles_nombre_max)
  loop
      -- On peut maintenant appliquer le bonus ou l’action sur une cible unique.

      code_retour := code_retour || '<br />'|| ligne.perso_nom || ' est la cible du sort «' || v_sort_texte || '».' ;

      if v_sort_case = 'O' then
          -- ciblage de la case du perso
  				v_fonction := 'select ' || v_sort_fonction ||'('||trim(to_char(v_source,'999999999'))||','||trim(to_char(ligne.pos_cod,'9999999999'))||',-1)';

      else
          -- ciblage du perso
  				v_fonction := 'select ' || v_sort_fonction ||'('||trim(to_char(v_source,'999999999'))||','||trim(to_char(ligne.perso_cod,'9999999999'))||',-1)';

      end if;

      -- lancement du sort !
      execute v_fonction into v_sort_retour;
      /*
      if v_sort_retour is not null and v_sort_retour <>'' then
          code_retour := code_retour || '<br />'|| v_sort_retour ;
      end if;
      */

      -- On rajoute la ligne d’événements
      if v_texte_evt != '' then
          if strpos(v_texte_evt , '[cible]') != 0 then
            perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
          else
            perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
          end if;
      end if;

  end loop;

  if code_retour = '' then
    code_retour := 'Aucune cible éligible pour le lancement du sort «' || v_sort_cod_texte || '»';
  end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_lance_sort(integer, integer, text, integer, character, text, numeric, text, json) OWNER TO delain;

--
-- Name: FUNCTION ea_lance_sort(integer, integer, text, integer, character, text, numeric, text, json); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ea_lance_sort(integer, integer, text, integer, character, text, numeric, text, json) IS 'Supprimes des Bonus / Malus standards ou cumulatifs en fonction des paramètres donnés.';

