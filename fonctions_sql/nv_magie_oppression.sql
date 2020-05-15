CREATE or replace FUNCTION public.nv_magie_oppression(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* fonction magie_oppression : lance le sort oppression !        */
/* permet de faire des dégâts en fonction de l'armure de la cible*/
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 24/05/2006                                            */
/* Liste des modifications :                                     */
/*  								 */
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
    v_armure                 integer; -- armure de la cible
    total_degats             integer; -- degats occasionnés
    has_bloque               integer; -- compétence blocage ?
    pv_cible                 integer; -- pv de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 numeric; -- PX gagnes
    proj_bloque              integer; -- projectile bloqué ?
    v_degats                 integer; -- degats du sort
    niveau_sort              integer; -- niveau du sort
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des runes à dropper
    temp_ameliore_competence text;
    -- chaine temporaire pour amélioration
    v_bloque_magie           integer; -- vérif si bloque magique

-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_reussite               integer;
    v_pv_cible               integer;
    texte_mort               text;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 110;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into niveau_sort,nom_sort sort_niveau, sort_nom from sorts where sort_cod = num_sort;
    select into nom_cible,v_pv_cible perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
    magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');
    v_reussite := to_number(split_part(magie_commun_txt, ';', 5), '99999999999999D99');
--On calcule les dégâts subits par la cible
    v_armure := f_armure_perso(cible);
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    if v_bloque_magie = 0 then
        ------------------------
-- magie non résistée --
------------------------
        v_degats := v_armure - 2 + lancer_des(1, 10);
        code_retour := code_retour || 'Votre adversaire n''arrive pas à résister au sort.<br>';
    else
        --------------------
-- magie résistée --
--------------------		   
        v_degats := (v_armure / 2) - 2 + lancer_des(1, 10);
        code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
    end if;
    code_retour := code_retour || '<br>Vous lancez le sort <b>' || nom_sort || '</b> sur ' || nom_cible ||
                   ', faisant ' || trim(to_char(v_degats, '99')) || '<br> !';
    if v_pv_cible < v_degats then
        -- personnage tué
        code_retour := code_retour || '<p>Vous avez tué votre adversaire.<br><hr>';
        texte_mort := tue_perso_final(cible, cible);
        code_retour := code_retour || split_part(texte_mort, ';', 2);
    else
        -- personnage survit
        update perso set perso_pv = v_pv_cible - v_degats where perso_cod = cible;
    end if;

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
    code_retour := code_retour || execute_effet_auto_mag(lanceur, cible, num_sort, 'L') || execute_effet_auto_mag(cible, lanceur, num_sort, 'C');

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_oppression(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_oppression(integer, integer, integer) IS 'En cours d''écriture - rajout de dégâts variables';
