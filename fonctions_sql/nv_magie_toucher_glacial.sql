CREATE or replace FUNCTION public.nv_magie_toucher_glacial(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_toucher_glacial: lance le sort toucher glacial */
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
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_perso_niveau           integer; -- niveau du lanceur
    v_voie_magique           integer; -- voie magique du lanceur
    v_int_lanceur            integer; -- intelligence du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    pv_cible                 integer; -- pv de la cible
    texte_mort               text;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 numeric; -- PX gagnes
    bonus_voie               integer; -- bonus dégats de la voie
    degats                   integer; -- nombre de PV retirés
    pv_lanceur               integer; -- pv du lanceur
    pv_max_lanceur           integer; -- pv max du lanceur
    diff_pv                  integer; -- différence de pv
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des rune à dropper
    temp_ameliore_competence text;
    -- chaine temporaire pour amélioration
    v_bloque_magie           integer; -- vérif si résistance magique
    nb_sort_tour             integer;
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_pv_cible               integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 147;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,v_pv_cible perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
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
    px_gagne := split_part(magie_commun_txt, ';', 4);
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    select into v_perso_niveau,v_int_lanceur,v_voie_magique perso_niveau, perso_int, perso_voie_magique
    from perso
    where perso_cod = lanceur;
    -- impact de la voie_magique pour les dégats
    if v_voie_magique = 4 then
        bonus_voie := floor(v_perso_niveau / 3.5);
        code_retour :=
                    code_retour || 'Votre puissance de mage de guerre se fait sentir au travers de ce sortilège.<br>';
    else
        bonus_voie := 0;
    end if;
    if v_bloque_magie = 0 then
        ------------------------
-- magie non résistée --
------------------------
        degats := lancer_des(1, 20);
        -- / 0.57143 = * 1.75
        v_int_lanceur := floor(v_int_lanceur / 0.57143);
        degats := degats + v_int_lanceur + bonus_voie;
        code_retour := code_retour || 'Votre adversaire n''arrive pas à résister au sort.<br>';
        if ajoute_bonus(cible, 'PAA', 2, 2) != 0 then
            insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
            values (2, lanceur, cible, 0.5 * ln(v_pv_cible));
        end if;
        code_retour := code_retour || '<br>' || nom_cible ||
                       ' se retrouve totalement engourdie et subit un malus de 2 PA pour ses attaques.<br>';
    else
        --------------------
-- magie résistée --
--------------------		   
        degats := lancer_des(1, 10);
        degats := degats + (v_int_lanceur) + bonus_voie;
        code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
        -- on enlève les bonus existants
        if ajoute_bonus(cible, 'PAA', 2, 1) != 0 then
            insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
            values (2, lanceur, cible, 1 * ln(v_pv_cible));
        end if;
        code_retour := code_retour || '<br>' || nom_cible ||
                       ' se retrouve partiellement engourdie et subit un malus de 1 PA pour ses attaques.<br>';
    end if;
    des := effectue_degats_perso(cible, degats, lanceur);
    if des != degats then
        code_retour := code_retour || '<br>Les dégâts réels liés à l''initiative sont de ' ||
                       trim(to_char(des, '999999999'));
        insert into trace (trc_texte)
        values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
    end if;
    degats := des;
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.25 * ln(v_pv_cible) * degats);
    code_retour := code_retour || '<br>' || nom_cible || ' perd ' || trim(to_char(degats, '999')) ||
                   ' points de vie.<br>';
    select into pv_cible perso_pv
    from perso
    where perso_cod = cible;
    if pv_cible > degats then
        -- pas tuée
        code_retour := code_retour || nom_cible || ' survit à ce sortilège.<br>';
        update perso
        set perso_pv = perso_pv - degats
        where perso_cod = cible;
    else
        -- on appelle la fonction qui gère la mort
        texte_mort := tue_perso_final(lanceur, cible);
        --
        px_gagne := px_gagne + to_number(split_part(texte_mort, ';', 1), '999999999');
        code_retour := code_retour || '<p>Vous avez tué votre adversaire.<br>';
        code_retour := code_retour || split_part(texte_mort, ';', 2);

    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) ||
                   ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], ';
    if v_bloque_magie != 0 then
        texte_evt := texte_evt || 'qui a partiellement résisté au sort, ';
    end if;
    texte_evt := texte_evt || ' occasionnant ' || trim(to_char(degats, '999')) || ' dégâts';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    if (lanceur != cible) then
        insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                              levt_visible, levt_attaquant, levt_cible)
        values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
    end if;

    ---------------------------
    -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
    ---------------------------
    code_retour := code_retour || execute_effet_auto_mag(lanceur, cible, num_sort, 'L') || execute_effet_auto_mag(cible, lanceur, num_sort, 'C');

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_toucher_glacial(integer, integer, integer) OWNER TO delain;
