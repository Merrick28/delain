CREATE or replace FUNCTION public.nv_magie_eclair_glace_m(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function éclair : éclair de glace                             */
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
    has_bloque               integer; -- compétence blocage ?
    pv_cible                 integer; -- pv de la cible
    nb_sort_tour             integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 numeric; -- PX gagnes
    duree                    integer; -- Durée du sort
    proj_bloque              integer; -- projectile bloqué ?
    degats                   integer; -- degats de l'éclair
    niveau_sort              integer; -- niveau du sort
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des rune à dropper
    temp_ameliore_competence text; -- chaine temporaire pour amélioration
    v_bloque_magie           integer; -- vérif si bloque magique
    -------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_reussite               integer;
    v_pv_cible               integer;
    texte_mort               text;
    valeur_des               integer;
    v_bonus_pa_dep           integer;
    v_resiste                integer; -- prends la valeur 1 si non resiste
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 170;
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
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');
-- On détermine les dégâts
    degats := 0;
    select into v_perso_int,v_voie_magique perso_int, perso_voie_magique
    from perso
    where perso_cod = lanceur;
    v_perso_int := floor(v_perso_int / 4);
    degats := lancer_des(1, 5);
    if v_perso_int > 5 then
        degats := degats * 6;
    elsif v_perso_int > 4 then
        degats := degats * 5;
    elsif v_perso_int > 3 then
        degats := degats * 4;
    elsif v_perso_int > 2 then
        degats := degats * 3;
    elsif v_perso_int > 1 then
        degats := degats * 2;
    end if;
-- ajout du bonus de degats de la voie magique
    v_bonus_pa_dep := 0;
    if v_voie_magique = 6 then
        degats := degats + 5 + v_perso_int;
        v_bonus_pa_dep := 1;
        code_retour := code_retour ||
                       '<br>Un mage de bataille comme vous fait toujours plus de dégats avec un éclair de glace que n''importe quel autre mage, et le malus lié au déplacement est toujours plus efficace.<br>';
    end if;
    code_retour := code_retour || '<br>Vous lancez un éclair qui vient frapper votre cible, ' || nom_cible || '<br>';
-- on regarde si resisté
    v_resiste := 1;
    if v_bloque_magie != 0 then
        code_retour := code_retour || ' que votre adversaire réussit à repousser, partiellement.<br>';
        v_resiste := 0;
        degats := degats / 2;
        degats := degats + v_perso_int;
    else
        -- sort non bloqué
        code_retour := code_retour || 'que votre adversaire encaisse de plein fouet.<br>';
        v_resiste := 1;
        degats := degats + v_perso_int;
    end if;
    des := effectue_degats_perso(cible, degats, lanceur);
    if des != degats then
        code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                       trim(to_char(des, '999999999')) || '.<br />';
        insert into trace (trc_texte)
        values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
    end if;
    degats := des;
    code_retour := code_retour || 'L''éclair en frappant sa cible fait <b>' || trim(to_char(degats, '99')) ||
                   '</b> dégats ';
-- maintenant on regarde les PV de la cible
    select into pv_cible perso_pv
    from perso
    where perso_cod = cible;
    code_retour := code_retour || '<hr>';
    code_retour := code_retour || '<p>' || nom_cible || ' subit un total de <b>' || trim(to_char(degats, '9999')) ||
                   '</b> dégats.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], effectuant ' || trim(to_char(degats, '999')) ||
                 ' dégats.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, cible, texte_evt, 'N', 'O', lanceur, cible);
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.25 * degats * ln(v_pv_cible));
    if pv_cible > degats then
        -- pas mort
        update perso set perso_pv = perso_pv - degats where perso_cod = cible;
        select into nom_cible,v_pv_cible perso_nom, perso_pv_max
        from perso
        where perso_cod = cible;
        v_bonus_pa_dep := v_bonus_pa_dep + 1;
        if v_resiste = 1 then
            v_bonus_pa_dep := v_bonus_pa_dep + 2;
        end if;
        code_retour := code_retour || 'Votre adversaire est engourdi.<br>';
        if ajoute_bonus(cible, 'DEP', 3, v_bonus_pa_dep) != 0 then
            insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
            values (2, lanceur, cible, ln(v_pv_cible));
        end if;
        code_retour := code_retour || '<br>' || nom_cible || ' a un malus au déplacement de ' ||
                       trim(to_char(v_bonus_pa_dep, '99')) || 'PA pendant 3 tours.';
        --  insert into -- ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        -- 	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
        --   insert into -- ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
        -- 	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
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
    ---------------------------
    -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
    ---------------------------
    code_retour := code_retour || execute_effet_auto_mag(lanceur, cible, num_sort, 'L') || execute_effet_auto_mag(cible, lanceur, num_sort, 'C');

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_eclair_glace_m(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_eclair_glace_m(integer, integer, integer) IS 'EdG pour monstre';
