CREATE or replace FUNCTION public.nv_magie_boule_magma(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_boule_feu : lance le sort boule de magma       */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
    code_retour              text; -- chaine html de sortie
    texte_evt                text; -- texte pour évènements
    nom_sort                 text; -- nom du sort
    v_bloque_magie           integer;
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    pv_cible                 integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 numeric; -- PX gagnes
    ligne                    record; -- enregistrements
    pos_lanceur              integer; -- pos_cod du lanceur
    x_lanceur                integer; -- x du lanceur
    y_lanceur                integer; -- y du lanceur
    e_lanceur                integer; -- etage du lanceur
    v_degats                 integer; -- dégats effectués
    text_brulure             text;
    reussite_esquive         integer; -- réussite on non de l'esquive
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des rune à dropper
    temp_ameliore_competence text;
    -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_act_numero             integer;
    nb_cible                 integer;
    nb_cible2                integer;
    v_pv_cible               integer;
    v_res_feu                integer;
    v_niv_lanceur            integer; -- niveau du lanceur du lanceur
    v_int_lanceur            integer; -- intelligence du lanceur
    v_voie_magique           integer; -- voie magique du lanceur
    v_bonus_voie             integer; -- bonus dégats lié à la voie magique
    v_bonus_degats           integer;
    v_bonus_cible            integer; -- bonus du nombre de cible liée à la voie magique
    v_int_lanceur_txt        text; -- int en text pour le code retour
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
    num_sort := 168;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
    select into nom_cible,v_pv_cible perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
    select into v_voie_magique,v_int_lanceur,v_niv_lanceur perso_voie_magique, perso_int, perso_niveau
    from perso
    where perso_cod = lanceur;
    select into nom_sort sort_nom
    from sorts
    where sort_cod = num_sort;
    magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');

    -- bonus de la voie magique
    if v_voie_magique = 6 then
        v_bonus_voie := 5;
    else
        v_bonus_voie := 0;
    end if;

    -- a partir d'ici on s'amuse
    -- on prend la position
    select into pos_lanceur,x_lanceur,y_lanceur,e_lanceur pos_cod,
                                                          pos_x,
                                                          pos_y,
                                                          pos_etage
    from positions,
         perso_position
    where ppos_perso_cod = cible
      and ppos_pos_cod = pos_cod;
    select into nb_cible count(perso_cod)
    from perso,
         perso_position
    where perso_actif = 'O'
      and perso_tangible = 'O'
      and ppos_perso_cod = perso_cod
      and perso_cod != lanceur
      and ppos_pos_cod = pos_lanceur;
    -- on commence par la cible
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    if v_bloque_magie = 0 then
        ------------------------
-- magie non résistée --
------------------------
        v_degats := lancer_des(12, 6);
        v_bonus_degats := floor(v_int_lanceur / 2);
        v_degats := v_degats + v_bonus_degats + v_bonus_voie;
        code_retour := code_retour || 'Votre adversaire n''arrive pas à résister au sort.<br>';
    else
        --------------------
-- magie résistée --
--------------------
        v_degats := lancer_des(6, 6);
        v_bonus_degats := floor(v_int_lanceur / 3);
        v_degats := v_degats + v_bonus_degats;
        code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
    end if;

    perform ajoute_bonus(cible, 'BRU', 2, v_bonus_degats);
    text_brulure := v_bonus_degats;
    des := effectue_degats_perso(cible, v_degats, lanceur);

    if des != v_degats then
        code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                       trim(to_char(des, '999999999')) || '.<br />';

        insert into trace (trc_texte)
        values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(v_degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));

    end if;
    v_degats := des;

    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, (0.5 * ln(v_pv_cible) * v_degats) / nb_cible);
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
    -- on regarde si esquive
    --v_degats := max(0,v_degats - f_armure_perso(cible));
    code_retour := code_retour || 'Vous provoquez ' || trim(to_char(v_degats, '99999')) || ' dégats sur <b>' ||
                   nom_cible || '</b><br>';
    code_retour := code_retour || 'La boule de magma inflige ' || text_brulure ||
                   ' de brulûres profondes durant deux tours.<br>';
    texte_evt := texte_evt || ' causant ' || trim(to_char(v_degats, '999999')) || ' dégats';

    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
    select into pv_cible perso_pv
    from perso
    where perso_cod = cible;
    if pv_cible <= v_degats then
        -- on a tué l'adversaire !!
        px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, cible), ';', 1), '99999999999999');
        code_retour := code_retour || 'Vous avez <b>tué</b> ' || nom_cible || '<br><br>';
    else
        code_retour := code_retour || nom_cible || ' a survécu à votre attaque<br><br>';
        update perso set perso_pv = perso_pv - v_degats where perso_cod = cible;
    end if;


    -- ensuite on fait les gens autour
-- NOMBRE DE CIBLES MAX = INT/2
    v_int_lanceur := floor(v_int_lanceur / 2);
    v_int_lanceur_txt := v_int_lanceur;
    code_retour := code_retour || '<b> Intelligence/2 = ' || v_int_lanceur_txt || ' Cibles au maximum</b><br>';
    v_int_lanceur := v_int_lanceur - 1;
    for ligne in select perso_cod, perso_nom, perso_pv, perso_pv_max, lancer_des(1, 1000) as num
                 from perso,
                      perso_position,
                      positions
                 where perso_actif = 'O'
                   and perso_tangible = 'O'
                   and ppos_perso_cod = perso_cod
                   and ppos_pos_cod = pos_cod
                   and pos_cod = pos_lanceur
                   and perso_cod != cible
                 order by num
                 limit v_int_lanceur
        loop
            texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
            v_bonus_degats := floor(v_int_lanceur / 3);
            v_degats := lancer_des(6, 6);
            v_degats := v_degats + v_bonus_degats + v_bonus_voie;

            perform ajoute_bonus(cible, 'BRU', 2, v_bonus_degats);
            text_brulure := v_bonus_degats;
            des := effectue_degats_perso(cible, v_degats, lanceur);
            if des != v_degats then
                code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                               trim(to_char(des, '999999999')) || '.<br />';
                insert into trace (trc_texte)
                values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                        ' init ' || trim(to_char(v_degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
            end if;
            v_degats := des;
            --v_degats := max(0,v_degats - (lancer_des(1,100)*f_armure_perso(ligne.perso_cod))/100);
            insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
            values (2, lanceur, ligne.perso_cod, (0.3 * ln(ligne.perso_pv_max) * v_degats) / nb_cible);
            -- on regarde si esquive
            code_retour := code_retour || 'Sur <b>' || ligne.perso_nom || '</b>, vous provoquez ' ||
                           trim(to_char(v_degats, '99999')) || ' dégats.<br>';
            code_retour := code_retour || 'La boule de magma inflige ' || text_brulure ||
                           ' de brulûres profondes durant deux tours.<br>';
            texte_evt := texte_evt || ' causant ' || trim(to_char(v_degats, '999999')) || ' dégats';
            insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                  levt_lu, levt_visible, levt_attaquant, levt_cible)
            values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, ligne.perso_cod);
            insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                  levt_lu, levt_visible, levt_attaquant, levt_cible)
            values (nextval('seq_levt_cod'), 14, now(), 1, ligne.perso_cod, texte_evt, 'N', 'O', lanceur,
                    ligne.perso_cod);
            if ligne.perso_pv <= v_degats then
                -- on a tué l'adversaire !!
                px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, ligne.perso_cod), ';', 1),
                                                 '9999999999999999');
                code_retour := code_retour || 'Vous avez <b>tué</b> ' || ligne.perso_nom || '<br><br>';
            else
                code_retour := code_retour || ligne.perso_nom || ' a survécu à votre attaque<br><br>';
                update perso set perso_pv = perso_pv - v_degats where perso_cod = ligne.perso_cod;
            end if;
        end loop;
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) ||
                   ' PX pour cette action.<br>';
    return code_retour;
end
$_$;


ALTER FUNCTION public.nv_magie_boule_magma(integer, integer, integer) OWNER TO delain;
