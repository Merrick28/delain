CREATE FUNCTION public.dv_mg_glang(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function dv_mg_glang : lance le sort glang magie divine       */
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
/*   18/04/2006 : modif des valeurs de dégâts (erreur par rapport*/
/*                                             à la description  */
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
    v_niveau_lanceur         integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    v_pv_cible               integer;
    pos_cible                integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
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
    -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_x                      integer;
    v_y                      integer;
    v_e                      integer;
    cpt_brouzoufs            integer;
    tmp_brouzoufs            integer;
    des_objet                integer;
    v_gobj_cod               integer;
    nom_obj                  text;
    nb_des_deg               integer;
    val_des_deg              integer;
    v_degats                 integer;
    nv_pv_cible              integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 78;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,nv_pv_cible perso_nom, perso_pv_max
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
    code_retour := split_part(magie_commun_txt, ';', 3);
    -- on va d'abord choisir l'objet
    des_objet := lancer_des(1, 100);
    if des_objet < 30 then
        v_gobj_cod := 68; -- dictionnaire
        nb_des_deg := 1;
        val_des_deg := 5;
    end if;
    if des_objet >= 30 and des_objet <= 66 then
        v_gobj_cod := 61; -- enclume
        nb_des_deg := 3;
        val_des_deg := 5;
    end if;
    if des_objet > 66 then
        v_gobj_cod := 60; -- armoire normande
        nb_des_deg := 5;
        val_des_deg := 5;
    end if;
    select into nom_obj gobj_nom
    from objet_generique
    where gobj_cod = v_gobj_cod;
    v_degats := lancer_des(nb_des_deg, val_des_deg);
    des := effectue_degats_perso(cible, v_degats, lanceur);
    if des != v_degats then
        code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                       trim(to_char(des, '999999999'));
        insert into trace (trc_texte)
        values (' att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(v_degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
    end if;
    v_degats := des;
    code_retour := code_retour || 'Vous avez créé l''objet ' || nom_obj ||
                   ' au dessus de la cible. Celui-ci tombe en faisant ' || trim(to_char(v_degats, '999999')) ||
                   ' dégats.<br>';
    select into pos_cible ppos_pos_cod from perso_position where ppos_perso_cod = cible;
    v_x := cree_objet_pos(v_gobj_cod, pos_cible);
    select into v_pv_cible perso_pv
    from perso
    where perso_cod = cible;
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.5 * ln(nv_pv_cible));
    if v_pv_cible < v_degats then
        -- cible tuée
        code_retour := code_retour || 'Vous avez <b>tué</b> votre adversaire.<br>';
        px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, cible), ';', 1), '99999999999999999');
    else
        -- cible encore en vie
        code_retour := code_retour || 'Votre cible a survécu.<br>';
        update perso set perso_pv = perso_pv - v_degats where perso_cod = cible;
    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '999')) || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], causant ' || trim(to_char(v_degats, '999999')) ||
                 ' dégats.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    if (lanceur != cible) then
        insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                              levt_visible, levt_attaquant, levt_cible)
        values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
    end if;

    return code_retour;
end;

$_$;


ALTER FUNCTION public.dv_mg_glang(integer, integer, integer) OWNER TO delain;
