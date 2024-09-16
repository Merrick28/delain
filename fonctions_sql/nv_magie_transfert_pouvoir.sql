CREATE or replace FUNCTION public.nv_magie_transfert_pouvoir(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function transfert_pouvoir : transfert pouvoir                */
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
    v_voie_magique           integer; --  voie magique du lanceur
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_pv_cible               integer;
    v_bonus_tour             integer;
    v_bonus_valeur           integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
    num_sort := 140;
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
    if v_bloque_magie = 0 then
        ------------------------
-- magie non résistée --
------------------------
        code_retour := code_retour ||
                       'Votre adversaire n''arrive pas à résister au sort, vous lui volez tous ses bonus.<br>';
-- effet de la voie magique
        select into v_voie_magique perso_voie_magique
        from perso
        where perso_cod = lanceur;
        if v_voie_magique = 3 then
            perform ajoute_bonus(cible, 'MDS', 3, 10);
            code_retour := code_retour ||
                           ' En tant que sorcier vous tuez lentement votre cible, infectant le sang de cette dernière pour 3 tours.<br>';
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours, bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur > 0
          and bonus_tbonus_libc = 'DEG' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur > 0
              and bonus_tbonus_libc = 'DEG' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'DEG', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur > 0
          and bonus_tbonus_libc = 'VUE' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur > 0
              and bonus_tbonus_libc = 'VUE' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'VUE', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur > 0
          and bonus_tbonus_libc = 'TOU' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur > 0
              and bonus_tbonus_libc = 'TOU' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'TOU', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur > 0
          and bonus_tbonus_libc = 'REG' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur > 0
              and bonus_tbonus_libc = 'REG' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'REG', 5, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur > 0
          and bonus_tbonus_libc = 'ARM' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur > 0
              and bonus_tbonus_libc = 'ARM' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'ARM', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'PAA' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'PAA' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'PAA', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'PAM' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'PAM' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'PAM', 2, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur > 0
          and bonus_tbonus_libc = 'ESQ' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur > 0
              and bonus_tbonus_libc = 'ESQ' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'ESQ', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'DEP' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'DEP' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'DEP', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'JUS' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'JUS' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'JUS', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'ERU' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'ERU' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'ERU', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'DSG' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'DSG' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'DSG', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'MUR' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'MUR' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'MUR', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'DIT' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'DIT' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'DIT', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'CFS' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'CFS' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'CFS', 3, v_bonus_valeur);
        end if;
        select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,
                                                bonus_valeur
        from bonus
        where bonus_perso_cod = cible
          and bonus_valeur < 0
          and bonus_tbonus_libc = 'BLM' and bonus_mode = 'S' ;
        if found then
            delete
            from bonus
            where bonus_perso_cod = cible
              and bonus_valeur < 0
              and bonus_tbonus_libc = 'BLM' and bonus_mode = 'S' ;
            perform ajoute_bonus(lanceur, 'BLM', 3, v_bonus_valeur);
        end if;
    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';
    if v_bloque_magie != 0 then
        texte_evt := texte_evt || ' qui a résisté au sort.';
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


ALTER FUNCTION public.nv_magie_transfert_pouvoir(integer, integer, integer) OWNER TO delain;
