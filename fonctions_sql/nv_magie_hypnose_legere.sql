CREATE or replace FUNCTION public.nv_magie_hypnose_legere(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_hypnose_legere : lance le sort hypnose_legere  */
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
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
    duree                    integer; -- Durée du sort
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
    v_hyp_pa                 integer; -- PA perdu au prochain tour
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_pv_cible               integer;
    v_hyp                    integer; -- malus d'hypno après le sort
    v_pa                     integer; -- Pa de la cible avant le sort
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
    num_sort := 14;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
    select into nom_cible,v_pv_cible,v_pa perso_nom, perso_pv_max, perso_pa
    from perso
    where perso_cod = cible;
    select into v_voie_magique perso_voie_magique
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
    px_gagne := split_part(magie_commun_txt, ';', 4);
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    if v_bloque_magie = 0 then
        code_retour := code_retour || 'Votre adversaire n''arrive pas à résister au sort.<br>';
    else
        code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
    end if;

    -- on regarde si la cible 8 PA ou moins
-- en ce cas on mets les PA à 0 et on reporte un malus de deux  PA sur les deux prochains tours
    if v_pa < 9 then
        if v_bloque_magie = 0 then
            update perso set perso_pa = 0 where perso_cod = cible;
            code_retour := code_retour || '<br>' || nom_cible || ' perd tous ses PA restants.<br>';
            v_hyp_pa := 2 ;
        else
            update perso set perso_pa = (perso_pa / 2)::integer where perso_cod = cible;
            code_retour := code_retour || '<br>' || nom_cible || ' perd la moitié de ses PA restants.<br>';
            v_hyp_pa := 2 ;
        end if;

        -- pour le maitre des arcanes on reporte 3 PA
        if v_voie_magique = 2 then
            v_hyp_pa := v_hyp_pa + 1 ;
            -- on aliment le malus d'hypnoptisme pour les deux prochains tours de 2
            if ajoute_bonus(cible, 'HYP', 2, v_hyp_pa) != 0 then
                -- on ajoute les bonus qui vont bien
                code_retour := code_retour || 'Un effet d''hypnotisme supplémentaire vient affecter ' || nom_cible ||
                               ' , qui subira un malus de ' || v_hyp_pa::text || ' PA en moins lors de ses deux prochaines réactivations.<br>';
            end if;
        else
            -- on aliment le malus d'hypnoptisme pour les deux prochains tours de 2
            if ajoute_bonus(cible, 'HYP', 2, v_hyp_pa) != 0 then
                -- on ajoute les bonus qui vont bien
                code_retour := code_retour || 'Un effet d''hypnotisme supplémentaire vient affecter ' || nom_cible ||
                               ' , qui subira un malus de ' || v_hyp_pa::text || ' PA en moins lors de ses deux prochaines réactivations.<br>';
            end if;
        end if;
    else
        if v_bloque_magie = 0 then
            v_pa := v_pa - 8;
            update perso set perso_pa = v_pa where perso_cod = cible;
            code_retour := code_retour || '<br>' || nom_cible || ' perd 8 PA.<br>';
        else
            v_pa := v_pa - 4;
            update perso set perso_pa = v_pa where perso_cod = cible;
            code_retour := code_retour || '<br>' || nom_cible || ' perd 4 PA.<br>';
        end if;

    end if;

    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 3 * ln(v_pv_cible));
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';
    if v_bloque_magie != 0 then
        texte_evt := texte_evt || ' qui a partiellement résisté au sort.';
    end if;
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


ALTER FUNCTION public.nv_magie_hypnose_legere(integer, integer, integer) OWNER TO delain;
