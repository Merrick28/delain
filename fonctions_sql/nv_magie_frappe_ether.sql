CREATE or replace FUNCTION public.nv_magie_frappe_ether(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function sort : Frappe d’ether                                */
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
    compteur_text            text; -- alpha du nombre de sorts
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
    v_code_cible             integer ;
    nom_cible                text; -- nom de la cible
    pv_cible                 integer; -- pv de la cible
    int_cible                integer; -- intelligence de la cible

    nb_sort_tour             integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 numeric; -- PX gagnes
    duree                    integer; -- Durée du sort
    degats                   integer; -- dégâts de l’attaque
    degats_temp              integer;
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
    ligne                    record;
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    compteur                 integer; -- compteur sorts détruits
    v_bloque_magie           integer;
    v_reussite               integer;
    v_pv_cible               integer;
    texte_mort               text;
    valeur_des               integer;
    v_diff_int               integer; -- diff int
    v_bonus_deg              integer; -- plus la cible est intelligente, plus il subit de dégats
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 135;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into niveau_sort,nom_sort sort_niveau, sort_nom from sorts where sort_cod = num_sort;
    select into nom_cible,v_pv_cible,int_cible,v_code_cible perso_nom, perso_pv_max, perso_int, perso_cod
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
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    --effet du sort
    -- on regarde pour chaque sort en réceptacle par la cible si on enleve le sort du réceptacle ou si on inflige des dégâts
    degats := 0;
    degats_temp := 0;
    v_bonus_deg := 0;
    compteur := 0;
    select into v_perso_int, v_voie_magique perso_int, perso_voie_magique
    from perso
    where perso_cod = lanceur;
    compt := 0;
    v_diff_int := v_perso_int - int_cible;
    -- si la différence d’intelligence est négative on permet le blocage magique
    if v_diff_int < 0 then
        -- blocage magique
        if v_bloque_magie > 0 then
            -- sort totalement bloqué
            code_retour := code_retour || 'Votre adversaire résiste à votre sort.<br>';
            compt := 1;
            return code_retour;
        else
            compt := 0;
        end if;
    end if;
    if compt = 0 then
        -- on lit les sorts en réceptacles et on passe au délire
        degats_temp := 0;
        degats := 0;
        for ligne in select recsort_cod, perso_cod
                     from recsort,
                          perso
                     where recsort_perso_cod = perso_cod
                       and perso_cod = cible
            loop
                -- ajout d’augmentation de chance de détruire un sort en réceptacle
                if v_voie_magique = 6 then
                    compt := lancer_des(1, 3);
                else
                    compt := lancer_des(1, 2);
                end if;
                if compt = 1 then
                    -- le sort n’est pas détruit, le mage prends des dégâts
                    v_bonus_deg := floor(int_cible / 2);
                    degats_temp := lancer_des(1, 10);
                    degats := degats + degats_temp + v_bonus_deg;
                    -- ajout dans la boucle des dégâts du bonus voie magique
                    if v_voie_magique = 6 then
                        degats := degats + 2;
                    end if;
                else
                    -- on suprime le sort
                    delete from recsort where recsort_cod = ligne.recsort_cod;
                    compteur := compteur + 1;
                end if;
            end loop;
        compt := 0;
        compteur_text := compteur;
    end if;
    code_retour := code_retour ||
                   '<br>Vous attaquez l’esprit de votre adversaire, lui infligeant une douleur insoutenable et détruisant ses sortilèges en réceptacle.<br>';
    des := effectue_degats_perso(cible, degats, lanceur);
    if des != degats then
        code_retour := code_retour || '<br>Les dégâts réels liés à l’initiative sont de ' ||
                       trim(to_char(des, '999999999')) || '.<br />';
        insert into trace (trc_texte)
        values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
    end if;
    degats := des;
    code_retour := code_retour || 'La frappe d’éther fait <b>' || trim(to_char(degats, '99')) || '</b> dégâts, ';
    code_retour := code_retour || 'et elle détruit <b>' || compteur_text || '</b> sortilèges en réceptacles.';
    -- maintenant on regarde les PV de la cible
    select into pv_cible perso_pv
    from perso
    where perso_cod = cible;
    code_retour := code_retour || '<hr>';
    code_retour := code_retour || '<p>' || nom_cible || ' subit un total de <b>' || trim(to_char(degats, '9999')) ||
                   '</b> dégâts.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], effectuant ' || trim(to_char(degats, '999')) ||
                 ' dégâts.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    if (lanceur != cible) then
        insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                              levt_visible, levt_attaquant, levt_cible)
        values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
    end if;

    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.25 * degats * ln(v_pv_cible));
    if pv_cible > degats then
        -- pas mort
        update perso set perso_pv = perso_pv - degats where perso_cod = cible;
        code_retour := code_retour || '<p>Votre adversaire a survécu à cette attaque. Vous gagnez ' ||
                       trim(to_char(px_gagne, '9999990D99')) || ' PX pour cette action.';
        return code_retour;
    else
        -- on appelle la fonction qui gère la mort
        texte_mort := tue_perso_final(lanceur, cible);
        px_gagne := px_gagne + to_number(split_part(texte_mort, ';', 1), '999999999');
        code_retour := code_retour || '<p>Vous avez tué votre adversaire.<br>';
        code_retour := code_retour || split_part(texte_mort, ';', 2);
        code_retour := code_retour || '<hr><br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) ||
                       ' PX pour cette action.<br>';
    end if;

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_frappe_ether(integer, integer, integer) OWNER TO delain;
