CREATE or replace FUNCTION public.nv_magie_bipbip_groupe(integer, integer, integer) RETURNS text
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
    v_voie_magique           integer; -- voie magique du lanceur
    v_perso_int              integer; -- intelligence du lanceur
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
    select into v_perso_niveau,v_voie_magique,v_perso_int perso_niveau, perso_voie_magique, perso_int
    from perso
    where perso_cod = lanceur;
    v_perso_niveau := floor(v_perso_niveau / 10);
    v_perso_int := floor(v_perso_int / 5);
    if v_perso_niveau > v_perso_int then
        v_niv_lanceur := v_perso_niveau + 2;
    else
        v_niv_lanceur := v_perso_int + 2;
    end if;
    -- modif az le 30/09/2009, si on est pas maitre des arcanes,le nombre de cibles diminue
    If v_voie_magique = 2 then
        for ligne in
            select perso_cod, perso_nom
            from perso,
                 perso_position
            where ppos_pos_cod = v_pos
              -- and perso_type_perso != '3'
              and ppos_perso_cod = perso_cod
              and perso_actif = 'O'
            loop

                perform ajoute_bonus(ligne.perso_cod, 'DEP', 2, -2);
                code_retour := code_retour || '<br>' || ligne.perso_nom ||
                               ' a un bonus de -2 en déplacement pendant 2 tours. Sans votre pouvoir de maître des arcanes votre influence aurait été moindre.';
                texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
                insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                      levt_lu, levt_visible, levt_attaquant, levt_cible)
                values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, ligne.perso_cod);
                insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                      levt_lu, levt_visible, levt_attaquant, levt_cible)
                values (nextval('seq_levt_cod'), 14, now(), 1, ligne.perso_cod, texte_evt, 'N', 'O', lanceur,
                        ligne.perso_cod);
            end loop;
        -- on traite les non maitres des arcanes
    else
        --            for ligne in select perso_cod,perso_nom,perso_pv,perso_pv_max,lancer_des(1,100) as num
        --	from perso,perso_position,positions
        --	where perso_actif = 'O'
        -- and perso_tangible = 'O'
        -- and perso_type_perso != '3'
        --	and ppos_perso_cod = perso_cod
        --	and ppos_pos_cod = v_pos
        --      order by num limit v_niv_lanceur loop

        for ligne in
            select perso_cod, perso_nom
            from perso,
                 perso_position
            where ppos_pos_cod = v_pos
              and ppos_perso_cod = perso_cod
              and perso_actif = 'O'
            loop
            --  if ligne.perso_cod = lanceur then
            --	perform ajoute_bonus(ligne.perso_cod, 'DEP', 2, -2);
            --   code_retour := code_retour||'<br>'||ligne.perso_nom||' a un bonus de -2 en déplacement pendant 2 tours.';
            --   else

                perform ajoute_bonus(ligne.perso_cod, 'DEP', 2, -2);
                code_retour := code_retour || '<br>' || ligne.perso_nom ||
                               ' a un bonus de -2 en déplacement pendant 2 tours.';
                --  end if;
                texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';

                insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                      levt_lu, levt_visible, levt_attaquant, levt_cible)
                values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, ligne.perso_cod);
                if (lanceur != cible) then
                    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1,
                                          levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                    values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
                end if;

            end loop;

    end if;
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_bipbip_groupe(integer, integer, integer) OWNER TO delain;
