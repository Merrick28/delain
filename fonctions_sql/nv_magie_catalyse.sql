CREATE or replace FUNCTION public.nv_magie_catalyse(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function catalyse : sortilège catalyse                        */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 23/03/2012                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 :                                                */
/*   29/01/2004 :                                                */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
    code_retour              text; -- chaine html de sortie
    texte_evt                text; -- texte pour évènements
    nom_sort                 text; -- nom du sort
    texte_mort               text;
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
    px_gagne                 numeric; -- PX gagnes
    text_vregen              text;

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
    v_regen                  integer; -- D régénération lanceur
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_pv_cible               integer; -- pv max de la cible
    v_bonus_tour             integer;
    v_bonus_valeur           integer;
    degats                   integer;
    pv_cible                 integer; -- pv actuel de la cible

begin
    -------------------------------------------------------------
    -- Etape 1 : intialisation des variables
    -------------------------------------------------------------
    -- on renseigne d abord le numéro du sort
    num_sort := 166;
    -- les px
    px_gagne := 0;
    -------------------------------------------------------------
    -- Etape 2 : contrôles
    -------------------------------------------------------------
    select into nom_cible, v_pv_cible perso_nom, perso_pv_max from perso where perso_cod = cible;
    select into nom_sort sort_nom from sorts where sort_cod = num_sort;

    magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);

    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;

    code_retour := split_part(magie_commun_txt, ';', 3);
    px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');

    v_bloque_magie := split_part(magie_commun_txt, ';', 2);

    -- effet de la voie magique
    select into v_voie_magique, v_regen perso_voie_magique, perso_des_regen
    from perso
    where perso_cod = lanceur;

    if v_voie_magique = 3 then
        v_regen := v_regen * 2;
    end if;

    text_vregen := v_regen;
    compt := 0;

    select into v_bonus_tour,v_bonus_valeur bonus_nb_tours, bonus_valeur
    from bonus
    where bonus_perso_cod = cible
      and bonus_valeur > 0
      and bonus_tbonus_libc = 'MDS';

    if found then
        compt := compt + v_bonus_valeur;
    end if;

    select into v_bonus_tour, v_bonus_valeur bonus_nb_tours,
                                             bonus_valeur
    from bonus
    where bonus_perso_cod = cible
      and bonus_valeur > 0
      and bonus_tbonus_libc = 'POI';

    if found then
        compt := compt + v_bonus_valeur;
    end if;

    select into v_bonus_tour, v_bonus_valeur bonus_nb_tours,
                                             bonus_valeur
    from bonus
    where bonus_perso_cod = cible
      and bonus_valeur > 0
      and bonus_tbonus_libc = 'VEN';

    if found then
        compt := compt + v_bonus_valeur;
    end if;

    if v_bloque_magie = 0 then
        ------------------------
        -- magie non résistée --
        ------------------------
        code_retour := code_retour || 'Votre adversaire n’arrive pas à résister au sort.<br>';
    else
        -- on laisse le code de résistance mais le sort est non blocable dans sa définition.
        code_retour := code_retour || 'Votre adversaire arrive à résister au sort.<br>';
        compt := floor(compt / 2);
    end if;

    degats := floor(compt * 2.2);

    perform ajoute_bonus(cible, 'MDS', 3, v_regen);
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.25 * v_regen * ln(v_pv_cible));

    code_retour := code_retour || ' Vous infectez le sang de votre cible. Ce poison (' || text_vregen ||
                   ') sera actif durant 3 tours.<br>';
    if degats = 0 then
        code_retour := code_retour ||
                       'Votre cible n’avait ni poison, ni venin, ni sang infecté : votre sortilège est sans effets supplémentaires.<br>';
    else
        des := effectue_degats_perso(cible, degats, lanceur);
        if des != degats then
            code_retour := code_retour || '<br>Les dégâts réels liés à l’initiative sont de ' ||
                           trim(to_char(des, '999999999')) || '.<br />';
            insert into trace (trc_texte)
            values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                    ' init ' || trim(to_char(degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
        end if;
        degats := des;
        code_retour := code_retour || 'La catalyse provoque une alchimie dans le corps de votre cible qui fait <b>' ||
                       trim(to_char(degats, '999')) || '</b> dégâts.';

        -- maintenant on regarde les PV de la cible
        select into pv_cible perso_pv
        from perso
        where perso_cod = cible;

        code_retour := code_retour || '<hr>';
        code_retour := code_retour || '<p>' || nom_cible || ' subit un total de <b>' || trim(to_char(degats, '9999')) ||
                       '</b> dégâts.<br>';

        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (2, lanceur, cible, 0.25 * degats * ln(v_pv_cible));

        if pv_cible > degats then
            -- pas mort
            update perso set perso_pv = perso_pv - degats where perso_cod = cible;
            code_retour := code_retour || '<p>Votre adversaire a survécu à cette attaque.<br />';
        else
            -- on appelle la fonction qui gère la mort
            texte_mort := tue_perso_final(lanceur, cible);
            --
            px_gagne := px_gagne + to_number(split_part(texte_mort, ';', 1), '999999999');
            code_retour := code_retour || '<p>Vous avez <b>tué</b> votre adversaire.<br>';
            code_retour := code_retour || split_part(texte_mort, ';', 2);
        end if;
    end if;

    code_retour := code_retour || '<hr><br>Vous gagnez ' || trim(to_char(px_gagne, '9999990D99')) ||
                   ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], effectuant ' || trim(to_char(degats, '999')) ||
                 ' dégâts.';
    perform insere_evenement(lanceur, cible, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

    ---------------------------
    -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
    ---------------------------
    code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_catalyse(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_catalyse(integer, integer, integer) IS 'Lance le sort Catalyse';
