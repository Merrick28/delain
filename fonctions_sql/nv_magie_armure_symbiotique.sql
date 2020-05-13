CREATE or replace FUNCTION public.nv_magie_armure_symbiotique(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_armure_symbiotique : armue symbiotique         */
/*   importants                                                  */
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
    bonus_flux               text; -- pvies gagnés grace a conscience des flux
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_perso_int              integer; -- intelligence du lanceur
    v_perso_for              integer; -- force lanceur
    v_perso_dex              integer ; -- dexterité  lanceur
    v_perso_con              integer; -- constitution du lanceur
    v_voie_magique           integer; -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    sexe_cible               text; -- sexe de la cible
    v_pv                     integer; -- PV de la cible
    v_pv_max                 integer; -- PV max de la cible
    nouveau_pv               integer;
    malus                    integer;
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
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    v_compt                  integer; -- duree du sort
    v_puissance              integer; -- niveau du malus de glace
    v_force                  integer; -- niveau de l'armure
    v_dext                   integer; -- force de la defense
    temp_bonus               integer; -- comptage temp
    v_bonus_flux             integer; -- pvies gagnés grace a conscience des flux
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 200;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,sexe_cible perso_nom, perso_sex
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
-- on met à jour les variables de travail
    select into v_pv,v_pv_max perso_pv, perso_pv_max
    from perso
    where perso_cod = cible;
    select into v_perso_int,v_perso_for,v_perso_dex,v_perso_con,v_voie_magique perso_int,
                                                                               perso_force,
                                                                               perso_dexterité,
                                                                               perso_constitution,
                                                                               perso_voie_magique
    from perso
    where perso_cod = lanceur;
    if v_perso_int > 30 then
        v_compt := 5;
    elseif v_perso_int > 25 then
        v_compt := 4;
    elseif v_perso_int > 20 then
        v_compt := 3;
    elseif v_perso_int > 15 then
        v_compt := 2;
    elseif v_perso_int > 10 then
        v_compt := 1;
    end if;
    -- augmentation due à la voie magique
    if v_voie_magique = 1 then
        v_compt := v_compt + 1;
        code_retour := code_retour ||
                       '<br>Votre savoir de mage de guerre vous permet de prolonger plus longtemps l''armure symbiotique.<br>';
    end if;
    if v_perso_for > 30 then
        v_puissance := -5;
    elseif v_perso_for > 25 then
        v_puissance := -4;
    elseif v_perso_for > 20 then
        v_puissance := -3;
    elseif v_perso_for > 15 then
        v_dext := -2;
    elseif v_perso_for > 10 then
        v_dext := -1;
    end if;
    if v_perso_dex > 30 then
        v_dext := 70;
    elseif v_perso_dex > 25 then
        v_dext := 60;
    elseif v_perso_dex > 20 then
        v_dext := 50;
    elseif v_perso_dex > 15 then
        v_dext := 40;
    elseif v_perso_dex > 10 then
        v_dext := 30;
    end if;
    if v_perso_con > 30 then
        v_force := 8;
    elseif v_perso_con > 25 then
        v_force := 7;
    elseif v_perso_con > 20 then
        v_force := 6;
    elseif v_perso_con > 15 then
        v_force := 5;
    elseif v_perso_con > 10 then
        v_force := 4;
    end if;
    -- On rajoute le bonus dans la table, et on rajoute une action pour le partage de PX si le bonus est nouveau
    if ajoute_bonus(cible, 'GLA', v_compt, v_puissance) != 0 then
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (3, lanceur, cible, 0.1 * ln(v_pv_cible));
    end if;
    if ajoute_bonus(cible, 'ESQ', v_compt, v_dext) != 0 then
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (3, lanceur, cible, 0.1 * ln(v_pv_cible));
    end if;
    if ajoute_bonus(cible, 'ARM', v_compt, v_force) != 0 then
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (3, lanceur, cible, 0.1 * ln(v_pv_cible));
    end if;
    code_retour := code_retour || '<br>' || nom_cible || ' devient entouree d''une armure symbiotique pour' ||
                   v_compt || ' tours.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_armure_symbiotique(integer, integer, integer) OWNER TO delain;
