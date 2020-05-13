CREATE or replace FUNCTION public.nv_magie_gigantisme(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function lancement : Gigantisme                               */
/*  magique                                                      */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 22/09/2008                                            */
/* Liste des modifications :                                     */
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
    v_perso_int              integer; -- int du lanceur
    v_voie_magique           integer; -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    pv_cible                 integer; -- pv de la cible
    Taille_cible             integer; -- taille de la cible
    nb_sort_tour             integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 numeric; -- PX gagnes
    degats                   integer; -- degats de l'attaque
    niveau_sort              integer; -- niveau du sort
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
    v_reussite               integer;
    v_pv_cible               integer;
    texte_mort               text;
    valeur_des               integer;
    v_diff_taille            integer; -- diff de taille
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 136;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into niveau_sort,nom_sort sort_niveau, sort_nom from sorts where sort_cod = num_sort;
    select into nom_cible,v_pv_cible,taille_cible perso_nom, perso_pv_max, perso_taille
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
-- on détermine l'augmentation de taille, sachant que c'est limité à int/5
    select into v_perso_int,v_voie_magique perso_int, perso_voie_magique
    from perso
    where perso_cod = lanceur;
    compt := floor(v_perso_int / 4);
-- pour les enchanteurs runiques, on implémente un bonus lié à la voie magique
    if v_voie_magique = 5 then
        compt := compt + 1;
        code_retour := code_retour ||
                       '<br>Votre connaissance des enchantements vous permet d''acroitre plus vite la taille de votre cible.''<br>';
    end if;
-- On détermine les dégâts
    degats := 0;
    -- les dégats sont égaux à 2d4 * l'augmentation de la taille de la cible,
-- aucune cible ne peut au final avoir plus de 10 en taille. ce sort étant
-- sans  effet pour certains monstres.
-- on commence donc par voir de combien augmente au final la taille de la cible
    if taille_cible < 10 then
-- on traite en limitant la variation à la différence de taille jusqu'à 10
        v_diff_taille := 10 - taille_cible;
        if v_diff_taille < compt then
            compt := v_diff_taille;
        end if;
        -- on effectue les dégats
        degats := 0;
        degats := lancer_des(2, 4);
        degats := degats * compt;
        code_retour := code_retour ||
                       '<br>Vous agrandissez la taille réelle de votre adversaire, lui infligeant des dégats insuportables ' ||
                       nom_cible || '<br>';
        des := effectue_degats_perso(cible, degats, lanceur);
        if des != degats then
            code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                           trim(to_char(des, '999999999')) || '.<br />';
            insert into trace (trc_texte)
            values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                    ' init ' || trim(to_char(degats, '99999999')) || 'fin ' || trim(to_char(des, '99999999')));
        end if;
        degats := des;
        code_retour := code_retour || 'Le Gigantisme fait <b>' || trim(to_char(degats, '99')) || '</b> dégats ';
        -- maintenant on regarde les PV de la cible
        select into pv_cible perso_pv
        from perso
        where perso_cod = cible;
        code_retour := code_retour || '<hr>';
        code_retour := code_retour || '<p>' || nom_cible || ' subit un total de <b>' || trim(to_char(degats, '9999')) ||
                       '</b> dégats.<br>';
        texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], effectuant ' ||
                     trim(to_char(degats, '999')) || ' dégats.';
        insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                              levt_visible, levt_attaquant, levt_cible)
        values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
        if (lanceur != cible) then
            insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte,
                                  levt_lu, levt_visible, levt_attaquant, levt_cible)
            values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
        end if;

        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (2, lanceur, cible, 0.25 * degats * ln(v_pv_cible));
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (2, lanceur, cible, 4 * ln(v_pv_cible));
        if pv_cible > degats then
            -- pas mort
            -- comme la cible n'est pas morte on augmente sa taille
            update perso set perso_taille = perso_taille + compt where perso_cod = cible;
            update perso set perso_pv = perso_pv - degats where perso_cod = cible;
            return code_retour;
            code_retour := code_retour || '<p>Votre adversaire a survécu à cette attaque. Vous gagnez ' ||
                           trim(to_char(px_gagne, '9999990D99')) || ' PX pour cette action.';
        else
            -- on appelle la fonction qui gère la mort
            texte_mort := tue_perso_final(lanceur, cible);
            --
            px_gagne := px_gagne + to_number(split_part(texte_mort, ';', 1), '999999999');
            code_retour := code_retour || '<p>Vous avez tué votre adversaire.<br>';
            code_retour := code_retour || split_part(texte_mort, ';', 2);
            code_retour := code_retour || '<hr><br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) ||
                           ' PX pour cette action.<br>';
        end if;
        return code_retour;
    end if;
    code_retour := code_retour || '<p>' || nom_cible ||
                   ' est bien trop grand pour grandir encore. Votre sort est sans effet<br>';
    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_gigantisme(integer, integer, integer) OWNER TO delain;
