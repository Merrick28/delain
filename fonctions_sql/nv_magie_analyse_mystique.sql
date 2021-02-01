CREATE or replace FUNCTION public.nv_magie_analyse_mystique(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function nv_magie_analyse_mystique : analyse mystique         */
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
    code_retour            text; -- chaine html de sortie
    texte_evt              text; -- texte pour évènements
    nom_sort               text; -- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_int_perso            integer;
    v_voie_magique_lanceur integer; -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible              text; -- nom de la cible
    v_pv_cible             text; -- pvie
    v_pv_max_cible         text; -- pvie max
    v_int_cible            text; -- intelligence
    v_niv_cible            text; -- niveau
    v_for_cible            text; -- force
    v_arm_cible            text; -- amélioration armure
    v_vue_cible            integer; -- vue
    v_amel_vue_cible       integer; -- amélioration vue
    v_amel_deg_cible       text; -- amélioration dégâts
    v_amel_dist_cible      text; -- amélioration dégâts à distance
    v_reg_cible            text; -- nombre D regénération
    v_amel_reg_cible       text; -- amélioration regénération
    v_des_reg_cible        text; -- valeur D regénération
    v_receptacle_cible     text; -- nombre receptacle
    v_temps_tour           text; -- temps tour en minute
    v_perso_pa             text; -- PA restant
    v_perso_des_degat      text; -- nombre dés de dégâts
    v_perso_val_degat      text; -- valeur des dés de dégâts
    v_perso_deg_dext       text; -- amélioration dégâts à distance
    v_taille_cible         text; -- taille de la cible
    v_voie_magique         text; -- voie magique de la cible
    v_text_vue_cible       text;
    v_text_regeneration    text;
    v_hyp                  integer; -- malus d’hypnotisme
    v_glyphe_pos           integer; -- Glyphe de résurrection
    v_etage_nom            text; -- Glyphe de résurrection : Position
    v_x                    text; -- Glyphe de résurrection : Position
    v_y                    text; -- Glyphe de résurrection : Position

-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort               integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                integer; -- Cout en PA du sort
    px_gagne               text; -- PX gagnes

-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt       text; -- texte pour magie commun
    res_commun             integer; -- partie 1 du commun
    -- chaine temporaire pour amélioration
    v_bloque_magie         integer; -- vérif si résistance magique
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                    integer; -- lancer de dés
    compt                  integer; -- fourre tout
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 154;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	

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

---- minimum syndical
    select into v_int_perso,v_voie_magique_lanceur perso_int, perso_voie_magique
    from perso
    where perso_cod = lanceur;
    select into nom_cible,v_pv_cible,v_pv_max_cible,v_int_cible,v_niv_cible,v_for_cible perso_nom,
                                                                                        perso_pv,
                                                                                        perso_pv_max,
                                                                                        perso_int,
                                                                                        perso_niveau,
                                                                                        perso_for
    from perso
    where perso_cod = cible;

    code_retour := code_retour || 'l’analyse mystique vous dévoile que ' || nom_cible || ' est niveau ' ||
                   v_niv_cible || ' ,';
    code_retour := code_retour || ' il possède une intelligence de ' || v_int_cible || ' une force de  ' ||
                   v_for_cible || ' ,';
    code_retour := code_retour || ' et il lui reste ' || v_pv_cible || ' pvies sur un maximum de ' || v_pv_max_cible ||
                   ' pvies.<br>';
    if v_int_perso > 10 then
        select into v_arm_cible,v_vue_cible,v_amel_vue_cible,v_amel_deg_cible,v_amel_dist_cible,v_reg_cible,v_amel_reg_cible,v_des_reg_cible perso_amelioration_armure,
                                                                                                                                             perso_vue,
                                                                                                                                             perso_amelioration_vue,
                                                                                                                                             perso_amelioration_degats,
                                                                                                                                             perso_amel_deg_dex,
                                                                                                                                             perso_valeur_regen,
                                                                                                                                             perso_amelioration_regen,
                                                                                                                                             perso_des_regen
        from perso
        where perso_cod = cible;
        v_vue_cible := v_vue_cible + v_amel_vue_cible;
        v_text_vue_cible := v_vue_cible;
        code_retour := code_retour || 'l’analyse mystique vous dévoile également que ' || nom_cible ||
                       ' a une vue de  ' || v_text_vue_cible || ' ,';
        code_retour := code_retour || ' il a investi en protection ' || v_arm_cible || ' fois,';
        code_retour := code_retour || ' et augmenté ses dégâts au corps à corps ' || v_amel_deg_cible ||
                       ' fois, et ceux à distance ' || v_amel_dist_cible || ' fois';
        code_retour := code_retour || ' enfin sa regénération est de  ' || v_des_reg_cible || 'D' || v_reg_cible ||
                       ' hors bonus lié à la constitution. <br>';

    end if;
    if v_int_perso > 15 then
        select into v_receptacle_cible,v_temps_tour,v_perso_pa,v_taille_cible,v_voie_magique,v_perso_deg_dext, v_glyphe_pos perso_nb_receptacle,
                                                                                                                            perso_temps_tour,
                                                                                                                            perso_pa,
                                                                                                                            perso_taille,
                                                                                                                            perso_voie_magique,
                                                                                                                            perso_amel_deg_dex,
                                                                                                                            rejoint_glyphe_resurrection(perso_cod)
        from perso
        where perso_cod = cible;
        code_retour := code_retour || 'Pour finir vous devinez que ' || nom_cible || ' possède ' ||
                       v_receptacle_cible || ' receptacle(s) ,';
        code_retour := code_retour || ' son temps de tour est de  ' || v_temps_tour || ' minutes ,';
        code_retour :=
                    code_retour || ' et il lui reste ' || v_perso_pa || ' PA disponible(s) jusqu’a son prochain tour, ';
        code_retour := code_retour || ' sa taille est de  ' || v_taille_cible || '.<br>';
        if v_glyphe_pos != -1 then
            select into v_etage_nom, v_x, v_y etage_libelle, pos_x, pos_y
            from etage,
                 positions
            where etage_numero = pos_etage
              and pos_cod = v_glyphe_pos;
            code_retour := code_retour || nom_cible || ' est lié à un glyphe de résurrection situé en X=' || v_x ||
                           '/Y=' || v_y || ' à l’étage ' || v_etage_nom || '.<br>';
        end if;
    end if;

    -- on ajoute un deuxième effet kiss cool en fonction de la voie magique
    if v_voie_magique_lanceur = 2 then
        -- on a bien a faire à un maitre des arcanes
-- on aliment le malus d’hypnoptisme pour les deux prochains tours de 2
        if ajoute_bonus(cible, 'HYP', 2, 2) != 0 then
            code_retour := code_retour || 'Comme vous êtes un maître des arcanes votre sort est amplifié ' ||
                           nom_cible || ' subira un malus de 2PA après l’activation de sa prochaine dlt<br>';
        else
            code_retour := code_retour ||
                           'Comme vous êtes un maître des arcanes votre sort est amplifié ; malheureusement ' ||
                           nom_cible ||
                           ' est déjà sous l’effet d’une hypnose et votre connaissance n’apporte rien de plus.<br>';
        end if;
    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
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
    code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_analyse_mystique(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_analyse_mystique(integer, integer, integer) IS 'Lance le sort d’Analyse Mystique';
