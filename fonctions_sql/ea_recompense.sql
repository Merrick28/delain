--
-- Name: ea_recompense(integer, integer, integer, character, text, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_recompense(integer, integer, integer, character, text, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_recompense                                 */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Perso ciblé                             */
/*   $3 = distance (-1..n)                        */
/*   $4 = cibles (Type SAERTPCO)                  */
/*   $5 = cibles nombre, au format rôliste        */
/*   $6 = Probabilité de declenchement            */
/*   $7 = Message d’événement associé             */
/*   $8 = autres paramètres au format json        */
/*                                                */
/* Paramètres json relatifs aux objets:           */
/*   fonc_trig_nb_objet_min = nombre mini d’objets*/
/*        par cible (rôliste, 0 si vide)          */
/*   fonc_trig_nb_objet_max = nombre maxi d’objets*/
/*        par cible (rôliste, 0 ou vide = un tirage*/
/*        sur chaque objet de la liste)           */
/*   fonc_trig_objet = liste [{gobj_cod, taux}]   */
/*        taux = % de réussite propre à l’objet   */
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
    v_mod_ciblage integer;       -- modificateur de ciblage
    v_race_source integer;       -- La race de la source
    v_position_source integer;   -- Le pos_cod de la source
    v_cible_du_monstre integer;  -- La cible actuelle du monstre
    v_source_nom text;           -- nom du perso source
    v_nom_monstre text;           -- nom du monstre source sans son cod

    -- Output and data holders
    ligne record;                -- Une ligne d’enregistrements
    i integer;                   -- Compteur de boucle

    v_compagnon integer;         -- cod perso du familier si aventurier et de l'aventurier si familier
    v_distance_min integer;      -- distance minimum requis pour la cible
    v_exclure_porteur text;      -- le porteur et les compagnons sont inclus dans le ciblage
    v_equipage integer;          -- le partenaire d'équipage: cavalier/monture

    v_gain_po integer ;           -- gain en PO
    v_gain_px  integer ;          -- gain en PX
    v_gain_titre text;            -- gain de titre
    v_gain_titre_tueur text;      -- gain de titre pour le tueur
    v_partage_px integer ;        -- flag de partage de px

    -- Distribution d’objets
    v_objets_json json;           -- liste des objets et de leur taux de réussite
    ligne_obj record;             -- Une ligne de la liste d’objets
    v_liste_gobj integer[];       -- gobj_cod des objets valides de la liste
    v_liste_taux numeric[];       -- taux (en %) de chaque objet valide (même indice que v_liste_gobj)
    v_nb_liste integer;           -- nombre d’objets valides dans la liste
    v_nb_min integer;             -- nombre mini d’objets pour la cible
    v_nb_max integer;             -- nombre maxi d’objets pour la cible
    v_tmp integer;                -- variable temporaire (inversion min/max)
    v_ordre integer[];            -- indices de la liste dans un ordre aléatoire
    v_pris integer[];             -- indices des objets retenus pour la cible
    v_idx integer;                -- indice dans la liste d’objets
    v_total numeric;              -- somme des taux des objets restants
    v_tirage numeric;             -- tirage pondéré
    v_cumul numeric;              -- cumul des taux lors du tirage pondéré
    v_trouve boolean;             -- un objet a été trouvé lors du tirage pondéré
    v_gobj_cod integer;           -- objet générique donné
    v_obj_cod integer;            -- obj_cod de l’objet créé (-1 en cas d’erreur)
    v_obj_nom text;               -- nom de l’objet donné
    v_obj_poids numeric;          -- poids de l’objet générique donné

    -- Output and data holders
    code_retour text;

    begin

    -- Chances de déclencher l’effet
    if random() > v_proba then
        -- return 'Pas d’effet automatique de « recompense ».';
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


    -- nombre de Cibles
    v_cibles_nombre_max := f_lit_des_roliste(v_cibles_nombre);
    if v_cibles_nombre_max = -1 then
        v_cibles_nombre_max := 1000;   -- valeur arbitraire pour dire "toutes les cibles"
    end if;

    v_mod_ciblage := f_to_numeric(COALESCE(v_params->>'fonc_trig_ciblage'::text, '0'))::integer ;

    v_nom_monstre := TRIM(SPLIT_PART(v_source_nom,'(n°', 1) )  ;
    v_gain_titre := replace (COALESCE((v_params->>'fonc_trig_titre')::text, ''), '[monstre]', v_nom_monstre);
    v_gain_titre_tueur := replace (COALESCE((v_params->>'fonc_trig_titre_tueur')::text, ''), '[monstre]', v_nom_monstre);
    if v_gain_titre_tueur = '' then
        v_gain_titre_tueur := v_gain_titre ;    -- s'il n'y a pas de titre spécifique pour le tueur, on prend le titre stadard
    end if;

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

    -- Liste des objets à distribuer: on ne garde que les objets génériques qui existent (une seule fois chacun dans la liste)
    v_objets_json := COALESCE(NULLIF((v_params->>'fonc_trig_objet')::text, ''), '[]')::json ;
    v_liste_gobj := '{}'::integer[];
    v_liste_taux := '{}'::numeric[];
    for ligne_obj in (select f_to_numeric(value->>'gobj_cod')::integer as gobj_cod,
                                 GREATEST(COALESCE(f_to_numeric(value->>'taux'), 0), 0) as taux
                          from json_array_elements(v_objets_json))
        loop
            if ligne_obj.gobj_cod > 0
               and not (ligne_obj.gobj_cod = ANY (v_liste_gobj))
               and exists (select 1 from objet_generique where gobj_cod = ligne_obj.gobj_cod) then
                    v_liste_gobj := v_liste_gobj || ligne_obj.gobj_cod;
                    v_liste_taux := v_liste_taux || ligne_obj.taux;
            end if;
        end loop;
    v_nb_liste := cardinality(v_liste_gobj);


    -- Et finalement on parcourt les cibles.
    for ligne in (select perso_cod , perso_type_perso , perso_race_cod, perso_nom, perso_niveau, perso_int, perso_con, pos_cod, perso_enc_max
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
           (v_cibles_type = 'V' and f_est_dans_la_liste(COALESCE(f_perso_monture_race(perso_cod), 0), (v_params->>'fonc_trig_races')::json)) or
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

        -- px/po peut ettre déterminé par un dé rolliste, on recalcul a chaque fois
        v_gain_po := CASE WHEN COALESCE((v_params->>'fonc_trig_gain_po')::text, '')='' THEN 0 ELSE f_lit_des_roliste((v_params->>'fonc_trig_gain_po')::text) END ;
        v_gain_px := CASE WHEN COALESCE((v_params->>'fonc_trig_gain_px')::text, '')='' THEN 0 ELSE f_lit_des_roliste((v_params->>'fonc_trig_gain_px')::text) END ;

        -- On verifie si le gain est limité au partage de px sur le monstre
        v_partage_px := 0 ;  -- par défaut le joueur ne fait pas parti du partage
        if v_mod_ciblage = 1 then
                -- on calcul à partir des actions qui servent au partage de px
                -- le partage n'a pas encore été fait, on ne peut pas utiliser les evenements

                SELECT sum(A.count) into v_partage_px FROM (
                    select count(*) as count from action, action_type
                        where act_perso2 = v_source and act_tact_cod = tact_cod
                        and act_tact_cod in (1, 2)
                        and act_perso1 = ligne.perso_cod

                    union all

                    select count(*) as count from action t2, action_type ta2
                        where t2.act_perso2 in
                            (select t1.act_perso1 from action t1
                            where t1.act_perso2 = v_source and act_tact_cod in (1, 2))
                        and t2.act_tact_cod = 3
                        and t2.act_tact_cod = ta2.tact_cod
                        and t2.act_perso1 = ligne.perso_cod

                    union all

                    select count(*) as count  from action t3, action_type ta3
                        where t3.act_perso2 in
                            (select t1.act_perso1 from action t1
                        where t1.act_perso2 = v_source and act_tact_cod in (1, 2))
                        and t3.act_tact_cod = 5
                        and t3.act_tact_cod = ta3.tact_cod
                        and t3.act_perso1 = ligne.perso_cod

                    union all

                    select count(*) as count from action, action_type
                        where act_perso1 = v_source and act_tact_cod = tact_cod
                        and act_tact_cod in (1, 2)
                        and act_donnee >= 0
                        and act_perso2 = ligne.perso_cod

                    ) as A;
        end if;

        -- si pas de restriction de ciblage ou si le perso fait partie du partage de px
        if (v_mod_ciblage = 0) or (v_partage_px > 0) then

            code_retour := code_retour || '<br />'|| ligne.perso_nom || ' est récompensé.' ;

           if v_gain_po > 0 then    -- distribution des po
                update perso set perso_po = perso_po + v_gain_po where perso_cod = ligne.perso_cod;
                perform insere_evenement(v_source, ligne.perso_cod, 40, '[attaquant] a donné '|| v_gain_po::text || ' brouzoufs à [cible]', 'O', 'N', null);
           end if;

            if v_gain_px > 0 then  -- distribution des px limité à 200px
                update perso set perso_px = perso_px + LEAST(200, v_gain_px) where perso_cod = ligne.perso_cod;
                perform insere_evenement(v_source, ligne.perso_cod, 18, '[attaquant] a donné '|| LEAST(200, v_gain_px)::text || ' PX à [cible]', 'O', 'N', null);
            end if;

            if ligne.perso_cod = v_cible_donnee then
                -- c'est le tueur
                if v_gain_titre_tueur != '' then  -- distribution du titre au tueur
                    -- on recherche si le perso ne l'a pas déjà
                    select count(*) into i from perso_titre where ptitre_perso_cod = ligne.perso_cod and ptitre_titre = v_gain_titre_tueur ;
                    if i = 0 then
                            insert into perso_titre (ptitre_perso_cod, ptitre_titre, ptitre_date) values ( ligne.perso_cod, v_gain_titre_tueur, now());
                    end if;
                end if;
            else
                -- cas des autre aventuriers (non tueurs)
                if v_gain_titre != '' then  -- distribution du titre
                    -- on recherche si le perso ne l'a pas déjà
                    select count(*) into i from perso_titre where ptitre_perso_cod = ligne.perso_cod and ptitre_titre = v_gain_titre ;
                    if i = 0 then
                            insert into perso_titre (ptitre_perso_cod, ptitre_titre, ptitre_date) values ( ligne.perso_cod, v_gain_titre, now());
                    end if;
                end if;
            end if;


            -- distribution des objets (chaque objet de la liste est donné au plus une fois par cible)
            if v_nb_liste > 0 then

                -- nombres mini / maxi d’objets: dés rôlistes recalculés pour chaque cible
                v_nb_min := CASE WHEN COALESCE((v_params->>'fonc_trig_nb_objet_min')::text, '')='' THEN 0 ELSE f_lit_des_roliste((v_params->>'fonc_trig_nb_objet_min')::text) END ;
                v_nb_max := CASE WHEN COALESCE((v_params->>'fonc_trig_nb_objet_max')::text, '')='' THEN 0 ELSE f_lit_des_roliste((v_params->>'fonc_trig_nb_objet_max')::text) END ;
                v_nb_min := GREATEST(COALESCE(v_nb_min, 0), 0);
                v_nb_max := GREATEST(COALESCE(v_nb_max, 0), 0);
                if v_nb_max = 0 then
                    v_nb_max := v_nb_liste ;    -- pas de maximum: un tirage sur chaque objet possible
                end if;
                if v_nb_min > v_nb_max then     -- min > max: on inverse les deux
                    v_tmp := v_nb_min ;
                    v_nb_min := v_nb_max ;
                    v_nb_max := v_tmp ;
                end if;
                v_nb_min := LEAST(v_nb_min, v_nb_liste);    -- on ne peut pas donner plus d’objets qu’il n’y en a dans la liste
                v_nb_max := LEAST(v_nb_max, v_nb_liste);

                -- Etape 1: on passe les objets un par un, dans un ordre aléatoire, chacun avec son propre % de réussite,
                -- jusqu’à atteindre le nombre maximum
                v_pris := '{}'::integer[];
                v_ordre := (select array_agg(g order by random()) from generate_series(1, v_nb_liste) as g);
                foreach v_idx in array v_ordre
                loop
                    exit when cardinality(v_pris) >= v_nb_max;
                    if random() * 100 < v_liste_taux[v_idx] then
                        v_pris := v_pris || v_idx;
                    end if;
                end loop;

                -- Etape 2: si le minimum n’est pas atteint, on complète par des tirages forcés pondérés par les taux,
                -- parmi les objets pas encore retenus (jamais deux fois le même objet)
                while cardinality(v_pris) < v_nb_min
                loop
                    v_total := 0;
                    for v_idx in 1..v_nb_liste
                    loop
                        if not (v_idx = ANY (v_pris)) then
                            v_total := v_total + v_liste_taux[v_idx];
                        end if;
                    end loop;
                    exit when v_total <= 0;     -- plus aucun objet avec un taux > 0: on ne donne rien de plus

                    v_tirage := random() * v_total;
                    v_cumul := 0;
                    v_trouve := false;
                    for v_idx in 1..v_nb_liste
                    loop
                        if not (v_idx = ANY (v_pris)) then
                            v_cumul := v_cumul + v_liste_taux[v_idx];
                            if v_tirage < v_cumul then
                                v_pris := v_pris || v_idx;
                                v_trouve := true;
                                exit;
                            end if;
                        end if;
                    end loop;
                    exit when not v_trouve;     -- sécurité
                end loop;

                -- Etape 3: on donne les objets retenus (inventaire, ou aux pieds si le perso est trop chargé)
                if cardinality(v_pris) > 0 then
                    foreach v_idx in array v_pris
                    loop
                        v_gobj_cod := v_liste_gobj[v_idx];
                        if v_gobj_cod > 0 then
                            select gobj_nom, gobj_poids into v_obj_nom, v_obj_poids from objet_generique where gobj_cod = v_gobj_cod;
                            v_obj_nom := COALESCE(v_obj_nom, 'un objet');
                            v_obj_poids := GREATEST(COALESCE(v_obj_poids, 0), 0);   -- ne pas mettre de poids négatif (cas des sacs)

                            if get_poids(ligne.perso_cod)+v_obj_poids > 3 * COALESCE(ligne.perso_enc_max, 0) then
                                -- trop chargé: l’objet est déposé aux pieds du perso
                                v_obj_cod := cree_objet_pos(v_gobj_cod, ligne.pos_cod);
                                if v_obj_cod >0 then
                                    perform insere_evenement(v_source, ligne.perso_cod, 3, '[cible] n’a pas réussi à ramasser ' || v_obj_nom || ' car il est trop chargé.', 'O', 'N', v_obj_cod);
                                end if;
                            else
                                v_obj_cod := cree_objet_perso(v_gobj_cod, ligne.perso_cod);
                                if v_obj_cod >0 then
                                    perform insere_evenement(v_source, ligne.perso_cod, 3, '[cible] a ramassé ' || v_obj_nom || '.', 'O', 'N', v_obj_cod);
                                end if;
                            end if;
                        end if;
                    end loop;
                end if;
            end if; -- fin distribution d’objets


            -- On rajoute la ligne d’événements
            if v_texte_evt != '' then
                if strpos(v_texte_evt , '[cible]') != 0 then
                    perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'N', null);
                else
                    perform insere_evenement(v_source, ligne.perso_cod, 54, v_texte_evt, 'O', 'O', null);
                end if;
            end if;

        end if;

    end loop;

    -- if code_retour = '' then
    --   code_retour := 'Aucune cible éligible pour le lancement du sort «' || v_sort_cod_texte || '»';
    -- end if;

    return code_retour;

end;$_$;


ALTER FUNCTION public.ea_recompense(integer, integer, integer, character, text, numeric, text, json) OWNER TO delain;

--
-- Name: FUNCTION ea_recompense(integer, integer, integer, character, text, numeric, text, json); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION ea_recompense(integer, integer, integer, character, text, numeric, text, json) IS 'Récompense les aventuriers pour la mise à mort d''un monstre.';

