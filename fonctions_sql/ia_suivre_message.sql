--
-- Name: ia_suivre_message(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ia_suivre_message(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************/
/* fonction ia_suivre_message                        */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 28/10/2013 : création                             */
/* 12/11/2013 : Moins de spam                             */
/*****************************************************/

declare
  -------------------------------------------------------
  -- variables E/S
  -------------------------------------------------------
  code_retour text;       -- code sortie
  -------------------------------------------------------
  -- variables de renseignements du monstre
  -------------------------------------------------------
  v_monstre alias for $1; -- perso_cod du monstre
  v_niveau integer;       -- niveau du monstre
  v_exp numeric;          -- xp du monstre
  v_pa integer;           -- pa du monstre
  v_vue integer;          -- distance de vue
  v_cible integer;        -- code de la cible
  actif varchar(2);       -- actif ?
  doit_jouer integer;     -- 0 pour non, 1 pour oui
  v_dlt timestamp;        -- dlt du monstre
  v_temps_tour integer;   -- temps du tour
  i_temps_tour interval;  -- temps du tour en intervalle
  temp_niveau integer;    -- random pour passage niveau
  v_lieu_dest integer;    -- La destination du lieu sur lequel peut être le monstre
  v_lieu_nom text;        -- Le nom du lieu sur lequel peut être le monstre
  v_pos_cod integer;      -- La position actuelle (code)
  v_position text;        -- La position du monstre (en textuel)
  -------------------------------------------------------
  -- variables temporaires ou de calcul
  -------------------------------------------------------
  temp integer;           -- fourre tout
  temp_txt text;          -- texte temporaire
  v_date_dernier timestamp; -- date du dernier message envoyé
  v_statut_dernier integer; -- statut du dernier message envoyé
  v_statut_msg integer;   -- statut du message envoyé
  v_msg integer;          -- le code du message envoyé
  v_msg_corps text;       -- le message à envoyer
  v_msg_titre text;       -- le titre du message
  v_msg_lien text;        -- le(s) lien(s) du message
  -------------------------------------------------------
  -- variables pour cible
  -------------------------------------------------------
  pos_dest integer;       -- destination

begin
  doit_jouer := 0;
  code_retour := 'IA standard<br>Monstre ' || trim(to_char(v_monstre, '999999999999'))||'<br>';
  /***********************************/
  /* Etape 1 : on récupère les infos */
  /* du monstre                      */
  /***********************************/
  temp_txt := calcul_dlt2(v_monstre);
  select into v_niveau,
    v_exp,
    v_pa,
    v_vue,
    v_cible,
    actif,
    v_dlt,
    v_temps_tour,
    v_pos_cod,
    v_position
    limite_niveau(perso_cod),
    perso_px,
    perso_pa,
    distance_vue(perso_cod),
    perso_cible,
    perso_actif,
    perso_dlt,
    perso_temps_tour,
    pos_cod,
    '[' || pos_x::text || ', ' || pos_y::text || ', ' || etage_libelle || ']'
  from perso
    inner join perso_position on ppos_perso_cod = perso_cod
    inner join positions on pos_cod = ppos_pos_cod
    inner join etage on etage_numero = pos_etage
  where perso_cod = v_monstre;

  if actif != 'O' then
    return 'inactif !';
  end if;

  i_temps_tour := trim(to_char(v_temps_tour,'999999999999'))||' minutes';
  if v_dlt + i_temps_tour - '10 minutes'::interval >= now() then
    doit_jouer := 1;
  end if;
  temp := lancer_des(1,100);
  if temp > 50 and doit_jouer = 0 then
    code_retour := code_retour||'Perso non joué.';
    return code_retour;
  end if;

  /***********************************/
  /* Etape 2 : on regarde si passage */
  /*  de niveau                      */
  /***********************************/
  -- on lance la procédure de passage de niveau
  if (v_exp >= v_niveau and v_pa >= getparm_n(8)) then
    temp_niveau := lancer_des(1, 6);
    temp_txt := f_passe_niveau(v_monstre, temp_niveau);
    select into v_pa perso_pa from perso where perso_cod = v_monstre;
    code_retour := code_retour || 'Passage niveau.<br>';
  end if;

  /**********************************************/
  /* Etape 3 : est-ce qu’on voit la cible ?     */
  /**********************************************/
  select into v_cible, v_date_dernier, v_statut_dernier
    pia_parametre, pia_date_dernier_msg, pia_msg_statut
  from perso_ia where pia_perso_cod = v_monstre;
  if not FOUND
  THEN
    RETURN 'Cible not found ?';
  END IF;
  select into pos_dest ppos_pos_cod from perso_position where ppos_perso_cod = v_cible;

  if (distance(v_pos_cod, pos_dest) > v_vue OR trajectoire_vue_murs(v_pos_cod, pos_dest) <> 1)
     AND (v_date_dernier IS NULL OR
          (v_date_dernier < now() - '1 day'::interval AND v_statut_dernier = 0) OR
          (v_date_dernier < now() - '12 hours'::interval AND v_statut_dernier = 1))
  then
    v_statut_msg := 0;
    v_msg_titre := 'Attendez-moi !';
    v_msg_corps := 'Où êtes-vous ? Je ne vous vois plus ! Attendez-moi s’il vous plaît !<br />';
    v_msg_corps := v_msg_corps || 'Je me trouve moi-même en ' || v_position || '.<br />';

    -- Y a-t-il un passage sur la case ?
    select into v_lieu_dest, v_lieu_nom lieu_dest, lieu_nom from lieu
      inner join lieu_position on lpos_lieu_cod = lieu_cod
    where lpos_pos_cod = v_pos_cod and lieu_dest IS NOT NULL;

    if found then
      v_statut_msg := 1;
      v_msg_corps := v_msg_corps || '<br />Ah ! Mais je vois ici un ' || v_lieu_nom || '... Dois-je l’emprunter ?';
      v_msg_lien := 'ia_suivre_gestion.php?perso=' || v_monstre::text || '&methode=oui';
      v_msg_corps := v_msg_corps || '<br /><hr />Souhaitez-vous...<br />';
      v_msg_corps := v_msg_corps || '<br />-&gt; <a href="' || v_msg_lien || '">Lui répondre OUI</a><br />';
      v_msg_lien := 'ia_suivre_gestion.php?perso=' || v_monstre::text || '&methode=non';
      v_msg_corps := v_msg_corps || '<br />-&gt; <a href="' || v_msg_lien || '">Lui répondre NON</a><br />';
    end if;

    v_msg_lien := 'ia_suivre_gestion.php?perso=' || v_monstre::text || '&methode=stop';
    v_msg_corps := v_msg_corps || '<hr /><a href="' || v_msg_lien || '">Arrêtez de me suivre !!</a><br />';

    -- test
    -- Envoi du message
    if v_cible is NULL
      THEN
      return 'Destinataire NULL';
    END IF;
    insert into messages (msg_titre, msg_corps)
    values (v_msg_titre, v_msg_corps)
    RETURNING msg_cod into v_msg;

    insert into messages_exp (emsg_msg_cod, emsg_perso_cod) values (v_msg, v_monstre);
    insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu, dmsg_archive) values (v_msg, v_cible, 'N', 'N');

    update perso_ia set pia_date_dernier_msg = now(), pia_msg_statut = v_statut_msg
    where pia_perso_cod = v_monstre;

    code_retour := code_retour || 'Cible hors de vue, envoi de message.';
  else
    /**********************************************/
    /* Etape 4 : on suit la cible                 */
    /**********************************************/
    code_retour := code_retour || ia_deplacement(v_monstre, pos_dest);
  end if;


  /*************************************************/
  /* Etape 5 : tout semble fini                    */
  /*************************************************/
  return code_retour;
end;$_$;


ALTER FUNCTION public.ia_suivre_message(integer) OWNER TO delain;

--
-- Name: FUNCTION ia_suivre_message(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ia_suivre_message(integer) IS 'Gère une IA qui suit un aventurier / monstre... et lui envoie un message s’il le perd de vue !';

