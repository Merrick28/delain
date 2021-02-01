--
-- Name: ea_drop_objet(integer, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_drop_objet(integer, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_drop_objet                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $3 = cibles nombre, au format rôliste        */
/*   $4 = Message d’événement associé             */
/*   $5 = Paramètre additionnels                  */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_nombre alias for $2;
  v_proba alias for $3;
  v_texte_evt alias for $4;
  v_params alias for $5;

  v_x_source integer;          -- source X position
  v_y_source integer;          -- source Y position
  v_et_source integer;         -- etage de la source
  v_type_source integer;       -- Type perso de la source
  v_nombre_max integer; -- Nombre calculé de cibles
  v_race_source integer;       -- La race de la source
  v_position_source integer;   -- Le pos_cod de la source
  v_cible_du_monstre integer;  -- La cible actuelle du monstre
  v_source_nom text;           -- nom du perso source

  v_gobj_cod integer ;         -- objet generique à droper !
  i integer;                   -- Compteur de boucle
  -- Output and data holders
  code_retour text;


begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    return 'Pas d’effet automatique de « drop objet ».';
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

  -- Cibles = nombre d'objet ici !
  v_nombre_max := f_lit_des_roliste(v_nombre);
  i:= 0 ;

  -- Et finalement on parcourt les drops
  while i < v_nombre_max
  loop
      i := i + 1;   -- compteur +1

      v_gobj_cod = f_tirage_aleatoire_liste('gobj_cod', 'taux', (v_params->>'fonc_trig_objet')::json) ;


      if v_gobj_cod > 0 then

          perform cree_objet_pos(v_gobj_cod, v_position_source);
          code_retour := code_retour|| '<br />' || v_source_nom || ' a perdu un objet qui est tombé au sol.';

          -- On rajoute la ligne d’événements
          if v_texte_evt != '' then
              if strpos(v_texte_evt , '[cible]') != 0 then
                perform insere_evenement(v_source, v_source, 54, v_texte_evt, 'O', 'N', null);
              else
                perform insere_evenement(v_source, v_source, 54, v_texte_evt, 'O', 'O', null);
              end if;
          end if;

      end if;

  end loop;

  if code_retour = '' then
    code_retour := 'Pas d’effet automatique de « drop objet »!';
  end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_drop_objet(integer, text, numeric, text, json) OWNER TO delain;

--
-- Name: FUNCTION ea_drop_objet(integer, text, numeric, text, json); Type: COMMENT; Schema: public; Owner: delain
--
