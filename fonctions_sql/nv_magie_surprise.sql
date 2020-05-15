CREATE or replace FUNCTION public.nv_magie_surprise(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_surprise : lance capitaine_surprise            */
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
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
    des_objet                integer; -- dés pour trouver objet
    code_objet               integer; -- code de l'objet créé
    nom_objet                text; -- nom de l'objet créé
    num_objet                integer; -- code de l'objet créé
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
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 54;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible perso_nom
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
    ------------------------
-- magie non résistée --
------------------------
    des_objet := lancer_des(1, 100);
    if des_objet <= 25 then
        code_objet := 161;
    end if;
    if des_objet > 25 and des_objet <= 48 then
        code_objet := 162;
    end if;
    if des_objet > 48 and des_objet <= 70 then
        code_objet := 163;
    end if;
    if des_objet > 70 and des_objet <= 90 then
        code_objet := 164;
    end if;
    if des_objet > 90 and des_objet <= 93 then
        code_objet := 27;
    end if;
    if des_objet > 93 and des_objet <= 96 then
        code_objet := 28;
    end if;
    if des_objet > 96 and des_objet <= 98 then
        code_objet := 29;
    end if;
    if des_objet = 99 then
        code_objet := 30;
    end if;
    if des_objet = 100 then
        code_objet := 31;
    end if;
    code_retour := code_retour || 'Votre adversaire n''arrive pas à résister au sort.<br>';
    num_objet := nextval('seq_obj_cod');
    insert into objets (obj_cod, obj_gobj_cod) values (num_objet, code_objet);
    insert into perso_objets
        (perobj_perso_cod, perobj_obj_cod)
    values (cible, num_objet);
    select into nom_objet gobj_nom from objet_generique where gobj_cod = code_objet;
    code_retour := code_retour || '<br>Vous avez créé l''objet <b>' || nom_objet || '</b> dans l''inventaire de ' ||
                   nom_cible || '.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (2, lanceur, cible, 1);
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';
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


ALTER FUNCTION public.nv_magie_surprise(integer, integer, integer) OWNER TO delain;
