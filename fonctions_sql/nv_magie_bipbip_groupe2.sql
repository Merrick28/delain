CREATE or replace FUNCTION public.nv_magie_bipbip_groupe2(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_bipbip_groupe : lance le sort                  */
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
/*   25/11/2009 : ajout du maitre des arcanes / revue du sort		 */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
    code_retour              text; -- chaine html de sortie
    texte_evt                text; -- texte pour évènements
    nom_sort                 text; -- nom du sort
    texte_add                text; --texte complémentaire pour les évènements
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_perso_niveau           integer; -- niveau du lanceur
    pos_lanceur              integer; -- position du lanceur
    v_voie_magique           integer; -- voie magique du lanceur
    v_perso_int              integer; -- intelligence du lanceur
    lanceur_nom              text; --nom du lanceur du sort
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    v_pos alias for $2; -- pos_cod de la cible
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
    duree_lanceur            integer;
    duree_cible              integer;
    nombre_cible             integer;
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
    v_niv_lanceur            integer;
    compteur                 integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 60;
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
    select into v_perso_niveau,v_voie_magique,v_perso_int,lanceur_nom,pos_lanceur perso_niveau,
                                                                                  perso_voie_magique,
                                                                                  perso_int,
                                                                                  perso_nom,
                                                                                  ppos_pos_cod
    from perso,
         perso_position
    where perso_cod = lanceur
      and ppos_perso_cod = lanceur;
    /*        v_perso_niveau := floor(v_perso_niveau/10);
            v_perso_int := floor(v_perso_int/5);
        if v_perso_niveau > v_perso_int then
                 v_niv_lanceur := v_perso_niveau + 2;
            else
                 v_niv_lanceur := v_perso_int + 2;
            end if;	*/
    -- modif az le 30/09/2009, si on est pas maitre des arcanes,le nombre de cibles diminue
    compt := 0; /*compteur qu'on initialise*/
    If v_voie_magique = 2 then
        duree_lanceur := 2; /* au cas où on veuille faire un cas particulier avec le lanceur de cette voie magique*/
        nombre_cible := 10000;
        texte_add := 'Sans votre pouvoir de maître des arcanes votre influence aurait été moindre.';
    else
        duree_lanceur := 2;
        nombre_cible := floor(v_perso_int / 4); /*nombre de cible bénéficiant d'un bonus sur deux tours*/
        texte_add := '';
    end if;
    for ligne in
        select perso_cod, perso_nom
        from perso,
             perso_position /*On détermine les cibles potentielles, en excluant le lanceur et les familiers*/
        where ppos_pos_cod = v_pos
          and perso_type_perso != '3'
          and ppos_perso_cod = perso_cod
          and perso_actif = 'O'
          and perso_cod != lanceur
        order by random()
        loop
            if compt < nombre_cible then
                duree_cible := 2; /*On traite le cas des cibles bénéficiant d'un full effect. Avec le maître des arcanes, le seuil de cible est 10000, donc toutes sont touchées de la même façon*/
            else
                duree_cible := 1;
            end if;
            compt := compt + 1;
            perform ajoute_bonus(ligne.perso_cod, 'DEP', duree_cible, -2);
            code_retour := code_retour || '<br>' || ligne.perso_nom || ' a un bonus de -2 en déplacement pendant ' ||
                           cast(duree_cible as text) || ' tours. ' || texte_add;
            texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
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
            code_retour := code_retour || execute_fonctions(lanceur, ligne.perso_cod, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions( ligne.perso_cod, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

        end loop;
    -- on traite ensuite le maitres des arcanes en particulier
    if v_pos = pos_lanceur then
        perform ajoute_bonus(lanceur, 'DEP', duree_lanceur, -2);
        code_retour := code_retour || '<br>' || lanceur_nom || ' a un bonus de -2 en déplacement pendant ' ||
                       cast(duree_lanceur as text) || ' tours. ' || texte_add;
        texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
        insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                              levt_visible, levt_attaquant, levt_cible)
        values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, lanceur);

        ---------------------------
        -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#ZONE#
        ---------------------------
        code_retour := code_retour || execute_fonctions(lanceur, lanceur, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(lanceur, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;
    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_bipbip_groupe2(integer, integer, integer) OWNER TO delain;
