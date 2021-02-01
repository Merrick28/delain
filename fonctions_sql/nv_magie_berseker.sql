CREATE or replace FUNCTION public.nv_magie_berseker(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_berseker : lance le sort Berseker              */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/*        2 = réceptacle                                         */
/*        4 = parchemin                                          */
/* Le code sortie est une chaîne html utilisable directement     */
/*****************************************************************/
/* Créé le 12/09/2008                                            */
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
    v_perso_niveau           integer;
    v_bonus_toucher          integer;
    v_int_perso              integer;
    v_voie_magique           integer; -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune ou receptacle ou parchemin)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des rune à dropper
    temp_ameliore_competence text; -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_pv_cible               integer;
    nb_sort_tour             integer;
    v_duree                  integer;
    v_duree_txt              text;
begin
    -------------------------------------------------------------
-- Etape 1 : initialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 128;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,v_pv_cible perso_nom,
                                     perso_pv_max
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
-- sélection sur le lanceur
    select into v_perso_niveau, v_int_perso, v_voie_magique perso_niveau,
                                                            perso_int,
                                                            perso_voie_magique
    from perso
    where perso_cod = lanceur;

    if v_int_perso > 20 then
        v_duree := 3;
    else
        v_duree := 2;
    end if;

    -- bonus sur la duree en fonction de la voie magique
    if v_voie_magique = 5 then
        v_duree := v_duree + 1;
        code_retour := code_retour || '<br>En tant qu’enchanteur runique, votre sort est prolongé.<br>';
    end if;
    perform ajoute_bonus(cible, 'PAA', v_duree, -2);
    perform ajoute_bonus(cible, 'DEG', v_duree, 4);
    v_duree_txt := v_duree;
    code_retour := code_retour || '<br>' || nom_cible ||
                   ' gagne un bonus de -2PA par attaque et +4 de dégâts pendant ' || v_duree_txt || ' tours.<br>';

    if v_voie_magique = 5 then
        v_duree := v_duree - 1;
    end if;
    v_duree_txt := v_duree;
    perform ajoute_bonus(cible, 'MAE', v_duree, -150);
    code_retour := code_retour || '<br>' || nom_cible || ' perd 150% en esquive durant ' || v_duree_txt ||
                   ' tours.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';

    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (3, lanceur, cible, 3.5 * ln(v_pv_cible));

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
    code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_berseker(integer, integer, integer) OWNER TO delain;
