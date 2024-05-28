--
-- Name: ea_message(integer, integer, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_message(integer, integer, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_message                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                             */
/*   $3 = distance (-1..n)                        */
/*   $4 = cibles (Type SAERTPCO)                  */
/*   $5 = cibles nombre, au format rôliste        */
/*   $6 = Probabilité de déclenchement            */
/*   $7 = Message mail sui sera envoyé            */
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
  v_texte_msg alias for $7;
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
	v_perso_fam integer;		     -- Familier de la cible
	v_event_txt text;	           -- pour autres evenements
  -- Output and data holders
  ligne record;                -- Une ligne d’enregistrements
  i integer;                   -- Compteur de boucle
  v_niveau_attaquant integer;  -- Resistance magique
  code_retour text;

  v_compagnon integer;         -- cod perso du familier si aventurier et de l'aventurier si familier
  v_distance_min integer;      -- distance minimum requis pour la cible
  v_exclure_porteur text;      -- le porteur et les compagnons sont inclus dans le ciblage
  v_equipage integer;          -- le partenaire d'équipage: cavalier/monture

  v_msg_envoye integer;      -- flag nombre de message envoye
  v_msg_cod integer;      -- id du message
  v_msg_expediteur integer;      -- type d'expediteur choisi
  v_msg_titre text;           -- titre du message

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « téléportation ».';
    return '';
  end if;
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


  if (v_params->>'fonc_trig_expediteur_msg')::text = 'M' then -- expédié par Malkiar
        v_msg_expediteur := 3 ;
  elsif (v_params->>'fonc_trig_expediteur_msg')::text = 'S' and v_source is not null then -- source de l'EA
        v_msg_expediteur := v_source ;
  else -- expedié par l'inconscicence
        v_msg_expediteur := 1595835 ;
  end if;

  v_msg_titre := left((v_params->>'fonc_trig_titre_msg')::text, 60);           -- titre du message
  v_msg_envoye := 0 ;

  -- on commence par créer le message (pour récupérer son identifiant
  INSERT INTO messages (msg_date2, msg_date, msg_titre, msg_corps, msg_init ) VALUES (now(), now(), v_msg_titre, v_texte_msg, v_msg_expediteur ) RETURNING msg_cod into v_msg_cod;

  -- Et finalement on parcourt les cibles.
  for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, pos_cod, perso_pv, pos_etage
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
                -- cas spécifique de la messagerie:
                      and (perso_type_perso!=3)                                                                         -- on ne mail pas les familiers
                -- Dans les limites autorisées
                order by random()
                limit v_cibles_nombre_max)
  loop
      -- mail au cible avec corps: v_texte_msg
      insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu ,dmsg_archive) values (v_msg_cod, ligne.perso_cod, 'N', 'N');
      v_msg_envoye := v_msg_envoye + 1 ;

  end loop;

  -- definir l'expediteur si des messages ont été envoyé sinon supprimer le message
  if v_msg_envoye > 0 then
      insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive) values (v_msg_cod, v_msg_expediteur, 'N');
  else
      delete from messages where msg_cod = v_msg_cod ;
  end if ;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_message(integer, integer, integer, character, text, numeric, text, json) OWNER TO delain;

