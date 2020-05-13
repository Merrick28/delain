CREATE FUNCTION public.nv_magie_conjuration_majeure(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_conjuration_majeure : lance le sort conjuration*/
/*   majeure                                                     */
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
/*   18/07/2012 : élargissement de l’utilisation du sort         */
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
    niveau_lanceur           integer; -- niveau du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    niveau_cible             integer; -- niveau de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
    v_malus_armure           interval; -- Pa modifiés
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
    v_gmon_cod               integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 98;
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

    select into v_gmon_cod, niveau_cible perso_gmon_cod, perso_niveau
    from perso
    where perso_cod = cible;
    if not found then
        code_retour := 'Vous ne pouvez lancer ce sort que sur un monstre !';
        return code_retour;
    end if;
    select into niveau_lanceur perso_niveau
    from perso
    where perso_cod = lanceur;

    if v_gmon_cod IS NULL then
        code_retour := 'Vous ne pouvez lancer ce sort que sur un monstre !';
        return code_retour;
    end if;

    if niveau_lanceur > niveau_cible then
        code_retour := code_retour ||
                       'Ce sort ne peut impacter que des monstres plus puissants que vous.<br />Votre adversaire n’en ressent aucun effet.<br>';
    else
        if v_bloque_magie = 0 then
            ------------------------
            -- magie non résistée --
            ------------------------
            v_malus_armure := '30 minutes';
            code_retour := code_retour || 'Votre adversaire n’arrive pas à résister au sort.<br>';
        else
            --------------------
            -- magie résistée --
            --------------------
            v_malus_armure := '15 minutes';
            code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
        end if;
        update perso set perso_dlt = perso_dlt + v_malus_armure where perso_cod = cible;
        code_retour := code_retour || '<br>' || nom_cible || ' semble s’affaiblir.';
    end if;

    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';

    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (2, lanceur, cible, 1);

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

    return code_retour;
end;

$_$;


ALTER FUNCTION public.nv_magie_conjuration_majeure(integer, integer, integer) OWNER TO delain;
