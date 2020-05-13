CREATE or replace FUNCTION public.nv_magie_implosion(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function lancement : Implosion                                */
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
    v_int_perso              integer; -- int du lanceur
    v_voie_magique           integer; -- voie magique du lanceur
    v_niv_lanceur            integer; -- niveau du lanceur
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
    ratio                    numeric;
    max_degat                integer; -- seuil maximum de dégats

begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 137;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into niveau_sort,nom_sort sort_niveau, sort_nom from sorts where sort_cod = num_sort;
    select into nom_cible,v_pv_cible,taille_cible perso_nom, perso_pv_max, perso_taille
    from perso
    where perso_cod = cible;
    select into v_int_perso,v_voie_magique,v_niv_lanceur perso_int, perso_voie_magique, perso_niveau
    from perso
    where perso_cod = lanceur;
    magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    px_gagne := to_number(split_part(magie_commun_txt, ';', 4), '99999999999999D99');
    v_reussite := to_number(split_part(magie_commun_txt, ';', 5), '99999999999999D99');
-- ajout azaghal 30/09/2010 seuil max dégats de 7 fois le niveau du lanceur
    max_degat := v_niv_lanceur * 7;
-- on détermine les dégats liés à la taille de la cible
    degats := 0;
    -- les dégats sont égaux 3.5% * taille de la cible * pvies actuels, avec une limite à
-- la taille de la cible mais avec un minimum d'int en dégats
    If taille_cible > 10 then
        compt := 10;
    else
        compt := taille_cible;
    end if;
-- on recupere les vpies actuels de la cible
    select into pv_cible perso_pv
    from perso
    where perso_cod = cible;
-- on effectue les dégats
    degats := 0;
    ratio := compt * 0.035;
-- introduction du bonus liée à la voie magique
    if v_voie_magique = 4 then
        ratio := compt * 0.040;
        code_retour := code_retour ||
                       '<br>La puissance du mage de guerre se reflète aujourd''hui dans la force de votre implosion.''<br>';
-- ajout azaghal, on max à 8 * niveau du lanceur pour un mage de guerre
        max_degat := v_niv_lanceur * 8;
    end if;
    degats := floor(ratio * pv_cible);
-- ajout azaghal, on max à 7 * niveau du lanceur pour un non mage de guerre
    if degats > max_degat then
        degats := max_degat;
    end if;
-- degats minimum de l'intelligence du lanceur
    if degats < v_int_perso then
        degats := v_int_perso;
    end if;
    code_retour := code_retour || '<br>Vous faites imploser ' || nom_cible ||
                   ' profitant de sa taille pour lui faire des dégats internes immenses.''<br>';
    des := effectue_degats_perso(cible, degats, lanceur);
    if des != degats then
        code_retour := code_retour || '<br>Les dégats rééls liés à l''initiative sont de ' ||
                       trim(to_char(des, '999999999')) || '.<br />';
        insert into trace (trc_texte)
        values ('att ' || trim(to_char(lanceur, '99999999')) || ' cib ' || trim(to_char(cible, '99999999')) ||
                ' init ' || trim(to_char(degats, '99999999')) || ' fin ' || trim(to_char(des, '99999999')));
    end if;
    degats := des;
    code_retour := code_retour || 'L''implosion fait <b>' || trim(to_char(degats, '999')) || '</b> dégats ';
-- maintenant on regarde les PV de la cible
    code_retour := code_retour || '<hr>';
    code_retour := code_retour || '<p>' || nom_cible || ' subit un total de <b>' || trim(to_char(degats, '9999')) ||
                   '</b> dégats.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], effectuant ' || trim(to_char(degats, '999')) ||
                 ' dégats.';
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
end;
$_$;


ALTER FUNCTION public.nv_magie_implosion(integer, integer, integer) OWNER TO delain;
