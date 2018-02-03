--
-- Name: annule_magie(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function annule_magie(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function annule_magie : Dissipe toute magie sur une case      */
/* et sur x cases aux alentours					 */
/* On passe en paramètres                                        */
/*    $1 = pos_cod              	                         */
/*    $2 = distance concernée		                         */
/* Pas de code sortie				                 */
/*****************************************************************/
/* Créé le 22/12/2007                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
  code_retour text;
  position_case alias for $1;
  v_distance alias for $2;
  v_x integer;
  v_y integer;
  v_etage integer;
  -- variable pour les evts
  texte_evt text;
  ligne record;
  ligne_bonus record;
  ligne_arme record;
  ligne_lieu record;
  lieu_arrivee integer;	--lieu cod de l’arrivée du passage pour sa suppression aussi
  compteur integer;
  v_perso integer;	-- Code du perso suite à la première boucle
  v_nom text;		-- Nom du perso suite à la première boucle
  compteur_text text;
begin
  /*on détermine toutes les positions touchées, et donc, tous les persos*/
  code_retour := '';
  texte_evt := '';
  select into v_x,v_y,v_etage
    pos_x,pos_y,pos_etage
  from positions
  where pos_cod = position_case;
  for ligne in select pos_cod,perso_cod,perso_nom
               from perso,perso_position,positions
               where perso_actif = 'O'
                     and perso_tangible = 'O'
                     and ppos_perso_cod = perso_cod
                     and ppos_pos_cod = pos_cod
                     and perso_tangible = 'O'
                     and pos_x between (v_x - v_distance) and (v_x + v_distance)
                     and pos_y between (v_y - v_distance) and (v_y + v_distance)
                     and pos_etage = v_etage
                     and not exists
               (select 1 from lieu_position,lieu
                   where lpos_pos_cod = pos_cod
                         and lieu_refuge = 'O')
                     and trajectoire_vue_murs(position_case,pos_cod) = 1
               order by perso_cod
  loop
    /*On commence à générer les événements, et le code retour*/
    texte_evt := '';
    compteur := 1;
    v_perso := ligne.perso_cod;
    v_nom := ligne.perso_nom;
    for ligne_bonus in select tonbus_libelle from bonus_type,bonus
    where bonus_tbonus_libc = tbonus_libc
          and bonus_perso_cod = v_perso
          and bonus_tbonus_libc not in ('POI')
    loop
      if compteur = 1 then
        code_retour := code_retour||'Pour '||v_nom;
        texte_evt := '[cible] a été impacté par une sphère d’annulation de magie, supprimant le bonus '||ligne_bonus.tonbus_libelle;
      else
        texte_evt := texte_evt||' et le bonus '||ligne_bonus.tonbus_libelle;
      end if;
      compteur := compteur + 1;
      code_retour := code_retour||', Bonus '||ligne_bonus.tonbus_libelle||' supprimé';
    end loop;
    if compteur > 1 then
      code_retour := code_retour||'. ';
    end if;

    /*rajout de l’événement pour le perso concerné*/
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_cible)
    values(nextval('seq_levt_cod'), 14, now(), 1, v_perso, texte_evt, 'N', 'N', v_perso);
    /*Suppression de tous les bonus de type "magiques" pour le perso concerné */
    delete from bonus where bonus_perso_cod = ligne.perso_cod and bonus_tbonus_libc in ('ATT', 'BER', 'PAM', 'ARM', 'PAA', 'TOU', 'DEG', 'VUE', 'ESQ', 'REG', 'DEP', 'ULT', 'DFM', 'BLM', 'MUR', 'MAE', 'DIS', 'DIT');

    /* Suppression des armes élémentaires : on a la liste des persos qui peuvent être concernés, on va regarder leur équipement porté*/
    texte_evt := '';
    compteur := 1;
    for ligne_arme in select perobj_obj_cod,obj_nom from perso_objets,objets,objet_generique
    where perobj_perso_cod = v_perso
          and perobj_equipe = 'O'
          and obj_cod = perobj_obj_cod
          and obj_gobj_cod = gobj_cod
          and perobj_dfin IS NOT NULL
          and perobj_dfin - '30 hours'::interval < now()
    loop
      if compteur = 1 then
        code_retour := code_retour||'Pour '||v_nom;
        texte_evt := 'l’arme de [cible], '||ligne_arme.obj_nom||', a été dissipée par une sphère d’annulation de magie';
      else
        texte_evt := texte_evt||', tout comme '||ligne_arme.obj_nom;
      end if;
      compteur := compteur + 1;
      code_retour := code_retour||', '||ligne_arme.obj_nom||' supprimé';
      /*Suppression de l’objet en question*/
      perform f_del_objet (ligne_arme.perobj_obj_cod);
    end loop;
    if compteur > 1 then
      code_retour := code_retour||'. ';
    end if;

    /*rajout de l’événement pour le perso concerné*/
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_cible)
    values(nextval('seq_levt_cod'), 14, now(), 1, v_perso, texte_evt, 'N', 'N', v_perso);
  end loop;

  /* Suppression des passages magiques */
  compteur := 0;
  for ligne_lieu in select lpos_lieu_cod,lpos_pos_cod,lieu_dest from lieu, lieu_position, positions
  where lpos_pos_cod = pos_cod
        and pos_x = v_x
        and pos_y = v_y
        and pos_etage = v_etage
        and lieu_cod = lpos_lieu_cod
        and lieu_tlieu_cod = 10
        and lieu_port_dfin IS NOT NULL
        and (lieu_url = 'passage.php'
             or lieu_url = 'passage_b.php')
  loop
    -- on regarde si on est sur un lieu de depart ou non
    if ligne_lieu.lieu_dest <> 0 then
      select into lieu_arrivee lpos_lieu_cod from lieu_position where lpos_pos_cod = ligne_lieu.lieu_dest;
    else
      select into lieu_arrivee lieu_cod from lieu, lieu_position where lieu_dest = lpos_pos_cod and lpos_lieu_cod = ligne_lieu.lpos_lieu_cod;
    end if;
    update lieu set lieu_port_dfin = now() where lieu_cod = ligne_lieu.lpos_lieu_cod;
    update lieu set lieu_port_dfin = now() where lieu_cod = lieu_arrivee;
    compteur := compteur + 1;
  end loop;
  compteur_text := compteur;
  code_retour := code_retour||'<br>'||compteur_text||' lieu(x) supprimé(s) (passages magiques)';
  return code_retour;
end;$_$;


ALTER FUNCTION public.annule_magie(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION annule_magie(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION annule_magie(integer, integer) IS 'Fonction annulant toute sorte de magie autour d’une case. Cela comprend les effets des sorts, les armes élémentaires, et les passages magiques.
On peut imaginer d’autres impacts d’annulation.';

--
-- Name: annule_magie(integer, integer, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function annule_magie(integer, integer, numeric) RETURNS text
LANGUAGE plpgsql
AS $_$/*****************************************************************/
/* function annule_magie : Dissipe toute magie sur une case      */
/* et sur x cases aux alentours, mais avec un %age de chance de  */
/* succès pour chaque élément.					 */
/* On passe en paramètres                                        */
/*    $1 = pos_cod              	                         */
/*    $2 = distance concernée		                         */
/*    $3 = la probabilité de suppression                         */
/* Pas de code sortie				                 */
/*****************************************************************/
/* Créé le 22/12/2007                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
  code_retour text;
  position_case alias for $1;
  v_distance alias for $2;
  v_proba alias for $3;
  v_x integer;
  v_y integer;
  v_etage integer;
  -- variable pour les evts
  texte_evt text;
  ligne record;
  ligne_bonus record;
  ligne_arme record;
  ligne_lieu record;
  lieu_arrivee integer;	--lieu cod de l’arrivée du passage pour sa suppression aussi
  compteur integer;
  v_perso integer;	-- Code du perso suite à la première boucle
  v_nom text;		-- Nom du perso suite à la première boucle
  compteur_text text;
begin
  /*on détermine toutes les positions touchées, et donc, tous les persos*/
  code_retour := '';
  texte_evt := '';
  select into v_x,v_y,v_etage
    pos_x,pos_y,pos_etage
  from positions
  where pos_cod = position_case;
  for ligne in select pos_cod,perso_cod,perso_nom
               from perso,perso_position,positions
               where perso_actif = 'O'
                     and perso_tangible = 'O'
                     and ppos_perso_cod = perso_cod
                     and ppos_pos_cod = pos_cod
                     and perso_tangible = 'O'
                     and pos_x between (v_x - v_distance) and (v_x + v_distance)
                     and pos_y between (v_y - v_distance) and (v_y + v_distance)
                     and pos_etage = v_etage
                     and not exists
               (select 1 from lieu_position,lieu
                   where lpos_pos_cod = pos_cod
                         and lieu_refuge = 'O')
                     and trajectoire_vue_murs(position_case,pos_cod) = 1
               order by perso_cod
  loop
    /*On commence à générer les événements, et le code retour*/
    texte_evt := '';
    compteur := 1;
    v_perso := ligne.perso_cod;
    v_nom := ligne.perso_nom;
    for ligne_bonus in select tonbus_libelle, bonus_tbonus_libc from bonus_type, bonus
    where bonus_tbonus_libc = tbonus_libc
          and bonus_perso_cod = v_perso
          and bonus_tbonus_libc in ('ATT', 'BER', 'PAM', 'ARM', 'PAA', 'TOU', 'DEG', 'VUE', 'ESQ', 'REG', 'DEP', 'ULT', 'DFM', 'BLM', 'MUR', 'MAE', 'DIS', 'DIT')
          and random() < v_proba
    loop
      if compteur = 1 then
        code_retour := code_retour||'Pour '||v_nom;
        texte_evt := '[cible] a été impacté par une sphère d’annulation de magie, supprimant le bonus '||ligne_bonus.tonbus_libelle;
      else
        texte_evt := texte_evt||' et le bonus '||ligne_bonus.tonbus_libelle;
      end if;
      /*Suppression de tous les bonus de type "magiques" pour le perso concerné */
      delete from bonus where bonus_perso_cod = ligne.perso_cod and bonus_tbonus_libc = ligne_bonus.bonus_tbonus_libc;

      compteur := compteur + 1;
      code_retour := code_retour||', Bonus '||ligne_bonus.tonbus_libelle||' supprimé';
    end loop;
    if compteur > 1 then
      code_retour := code_retour||'. ';

      /*rajout de l’événement pour le perso concerné*/
      insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_cible)
      values(nextval('seq_levt_cod'), 14, now(), 1, v_perso, texte_evt, 'N', 'N', v_perso);
    end if;

    /* Suppression des armes élémentaires  : on a la liste des persos qui peuvent être concernés, on va regarder leur équipement porté*/
    texte_evt := '';
    compteur := 1;
    for ligne_arme in select perobj_obj_cod,obj_nom from perso_objets,objets,objet_generique
    where perobj_perso_cod = v_perso
          and perobj_equipe = 'O'
          and obj_cod = perobj_obj_cod
          and obj_gobj_cod = gobj_cod
          and perobj_dfin IS NOT NULL
          and perobj_dfin - '30 hours'::interval < now()
          and random() < v_proba
    loop
      if compteur = 1 then
        code_retour := code_retour||'Pour '||v_nom;
        texte_evt := 'l’arme de [cible], '||ligne_arme.obj_nom||', a été dissipée par une sphère d’annulation de magie';
      else
        texte_evt := texte_evt||', tout comme '||ligne_arme.obj_nom;
      end if;
      compteur := compteur + 1;
      code_retour := code_retour||', '||ligne_arme.obj_nom||' supprimé';
      /*Suppression de l’objet en question*/
      perform f_del_objet (ligne_arme.perobj_obj_cod);
    end loop;
    if compteur > 1 then
      code_retour := code_retour||'. ';

      /*rajout de l’événement pour le perso concerné*/
      insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_cible)
      values(nextval('seq_levt_cod'), 14, now(), 1, v_perso, texte_evt, 'N', 'N', v_perso);
    end if;
  end loop;

  /* Suppression des passages magiques */
  compteur := 0;
  for ligne_lieu in select lpos_lieu_cod,lpos_pos_cod,lieu_dest from lieu, lieu_position, positions
  where lpos_pos_cod = pos_cod
        and pos_x = v_x
        and pos_y = v_y
        and pos_etage = v_etage
        and lieu_cod = lpos_lieu_cod
        and lieu_tlieu_cod = 10
        and lieu_port_dfin IS NOT NULL
        and (lieu_url = 'passage.php'
             or lieu_url = 'passage_b.php')
        and random() < v_proba
  loop
    -- on regarde si on est sur un lieu de depart ou non
    if ligne_lieu.lieu_dest <> 0 then
      select into lieu_arrivee lpos_lieu_cod from lieu_position where lpos_pos_cod = ligne_lieu.lieu_dest;
    else
      select into lieu_arrivee lieu_cod from lieu, lieu_position where lieu_dest = lpos_pos_cod and lpos_lieu_cod = ligne_lieu.lpos_lieu_cod;
    end if;
    update lieu set lieu_port_dfin = now() where lieu_cod = ligne_lieu.lpos_lieu_cod;
    update lieu set lieu_port_dfin = now() where lieu_cod = lieu_arrivee;
    compteur := compteur + 1;
  end loop;
  compteur_text := compteur;
  code_retour := code_retour||'<br>'||compteur_text||' lieu(x) supprimé(s) (passages magiques)';
  return code_retour;
end;$_$;


ALTER FUNCTION public.annule_magie(integer, integer, numeric) OWNER TO delain;

--
-- Name: FUNCTION annule_magie(integer, integer, numeric); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION annule_magie(integer, integer, numeric) IS 'Comme annule_magie (integer, integer), mais avec un paramètre qui donne la probabilité (de 0 à 1) que chaque élément soit supprimé.';