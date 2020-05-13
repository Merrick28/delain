CREATE FUNCTION public.dv_mg_lame_destin(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function dv_mg_lame_destin : lance le sort lame du destin     */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        doit être fixé à 3                                     */
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
    texte_evt                text; -- texte pour &#233;v&#232;nements
    nom_sort                 text; -- nom du sort
    texte_mort               text;
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_niveau_lanceur         integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    v_pv_cible               integer;
    v_cible                  integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- num&#233;ro du sort &#224; lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 integer; -- PX gagnes
    v_bonus                  integer;
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne                    record; -- record des rune à dropper
    temp_ameliore_competence text;
    -- chaine temporaire pour am&#233;lioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de d&#233;s
    compt                    integer; -- fourre tout
    cpt_brouzoufs            integer;
    tmp_brouzoufs            integer;
    des_objet                integer;
    v_gobj_cod               integer;
    nom_obj                  text;
    nb_des_deg               integer;
    val_des_deg              integer;
    v_degats                 integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le num&#233;ro du sort 
    num_sort := 79;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contr&#244;les
-------------------------------------------------------------	
    select into nom_cible,v_pv_cible perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
    select into nom_sort sort_nom
    from sorts
    where sort_cod = num_sort;
    magie_commun_txt := magie_commun_dieu(lanceur, cible, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    v_cible := cible;
    code_retour := split_part(magie_commun_txt, ';', 3);
    -- on va d'abord choisir l'objet
    des_objet := lancer_des(1, 100);
    if des_objet < 5 then
        code_retour := code_retour || 'Une lame surgie du n&#233;ant apparait, et se retourne contre vous, ';
        v_cible = lanceur;
    else
        code_retour := code_retour || 'Une lame surgie du n&#233;ant vient embrocher la cible';
    end if;
    v_degats := lancer_des(3, 5);
    v_degats := v_degats + 5;
    code_retour := code_retour || ' en faisant ' || trim(to_char(v_degats, '999999')) || ' d&#233;gats.<br>';
    des := effectue_degats_perso(cible, v_degats, lanceur);
    if des != v_degats then
        code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                       trim(to_char(des, '999999999'));
        insert into trace (trc_texte)
        values (' att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(v_degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
    end if;
    v_degats := des;
    v_degats := v_degats - f_armure_perso(v_cible);
    if v_degats < 0 then
        v_degats := 0;
    end if;
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.5 * ln(v_pv_cible));


    code_retour := code_retour || 'Son armure est de ' || trim(to_char(f_armure_perso(v_cible), '999999')) ||
                   ', vous causez donc ' || trim(to_char(v_degats, '999999')) || ' d&#233;gats.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], causant ' || trim(to_char(v_degats, '999999')) ||
                 ' dégats.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, v_cible);
    if (lanceur != cible) then
        insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                              levt_visible, levt_attaquant, levt_cible)
        values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
    end if;

    select into v_pv_cible perso_pv
    from perso
    where perso_cod = v_cible;
    if v_pv_cible < v_degats then
        -- on appelle la fonction qui gère la mort
        texte_mort := tue_perso_final(lanceur, cible);
        --
        px_gagne := px_gagne + to_number(split_part(texte_mort, ';', 1), '999999999');
        code_retour := code_retour || '<p>Vous avez tué votre adversaire.<br>';
        code_retour := code_retour || split_part(texte_mort, ';', 2);
    else
        -- cible encore en vie
        code_retour := code_retour || 'Votre cible a survécu.<br>';
        update perso set perso_pv = perso_pv - v_degats where perso_cod = v_cible;
    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) ||
                   ' PX pour cette action.<br>';

    return code_retour;
end;

$_$;


ALTER FUNCTION public.dv_mg_lame_destin(integer, integer, integer) OWNER TO delain;
