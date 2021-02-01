CREATE or replace FUNCTION public.nv_magie_defense_groupe(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_defense_groupe : lance le sort                 */
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
    pos_lanceur              integer; -- position du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    v_pos alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    pv_cible                 integer; -- pv de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
    v_bonus_toucher          integer; -- bonus toucher
    drain_pv                 integer; -- nombre de PV retirés
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
    ligne                    record;
    temp_ameliore_competence text;
    -- chaine temporaire pour amélioration
    v_bloque_magie           integer; -- vérif si résistance magique
    v_monstre                integer; --numéro du monstre créé
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_act_numero             integer;
    nb_cible                 integer;
    v_pv_cible               integer;
begin
    v_act_numero := nextval('seq_act_numero');
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 61;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_sort sort_nom
    from sorts
    where sort_cod = num_sort;
    magie_commun_txt := magie_commun_case(lanceur, v_pos, type_lancer, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    px_gagne := split_part(magie_commun_txt, ';', 4);
    select into v_perso_niveau perso_niveau
    from perso
    where perso_cod = lanceur;
    if v_perso_niveau > 5 then
        v_perso_niveau := 5;
    end if;
    select into pos_lanceur ppos_pos_cod
    from perso_position
    where ppos_perso_cod = lanceur;
    select into nb_cible count(perso_cod)
    from perso,
         perso_position
    where ppos_pos_cod = v_pos
      and ppos_perso_cod = perso_cod
      and perso_actif = 'O';
    for ligne in
        select perso_cod, perso_nom, perso_pv_max
        from perso,
             perso_position
        where ppos_pos_cod = v_pos
          and ppos_perso_cod = perso_cod
          and perso_actif = 'O'
        loop

            perform ajoute_bonus(ligne.perso_cod, 'ARM', 2, 2);


            code_retour := code_retour || '<br>' || ligne.perso_nom || ' a un bonus de 2 en armure pendant 2 tours.';
            texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
            insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
            values (5, lanceur, ligne.perso_cod, 1.5 * ln(ligne.perso_pv_max) / nb_cible);
            insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                  levt_lu, levt_visible, levt_attaquant, levt_cible)
            values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, ligne.perso_cod);
            if (lanceur != ligne.perso_cod) then
                insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                      levt_lu, levt_visible, levt_attaquant, levt_cible)
                values (nextval('seq_levt_cod'), 14, now(), 1, ligne.perso_cod, texte_evt, 'N', 'O', lanceur,
                        ligne.perso_cod);
            end if;

            ---------------------------
            -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#ZONE#
            ---------------------------
            code_retour := code_retour || execute_fonctions(lanceur, ligne.perso_cod, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(ligne.perso_cod, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

        end loop;
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_defense_groupe(integer, integer, integer) OWNER TO delain;
