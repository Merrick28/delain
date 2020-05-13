CREATE FUNCTION public.dv_mg_protection(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function dv_mg_faiblesse : lance le protection du juste       */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        doit être fixé à 3                                     */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 21/07/2006                                            */
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
    v_niveau_lanceur         integer;
    v_pv_lanceur             integer;
    v_pv_max_lanceur         integer;
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    v_pv_cible               integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 integer; -- PX gagnes
    v_bonus                  integer;
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne                    record; -- record des rune à dropper
    temp_ameliore_competence text;
    -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_x                      integer;
    v_y                      integer;
    v_e                      integer;
    cpt_brouzoufs            integer;
    tmp_brouzoufs            integer;
    des_objet                integer;
    v_gobj_cod               integer;
    nom_obj                  text;
    nb_des_deg               integer;
    val_des_deg              integer;
    v_degats                 integer;
    v_pv_cible_m             integer;
    v_niveau_dper            integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 155;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,v_pv_cible_m perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
    select into nom_sort sort_nom
    from sorts
    where sort_cod = num_sort;
    magie_commun_txt := magie_commun_dieu(lanceur, cible, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    -- on cherche le niveau de dieu du lanceur
    select into v_niveau_dper dper_niveau
    from dieu_perso
    where dper_perso_cod = lanceur;
    -- on efface l'existant
    perform ajoute_bonus(cible, 'JUS', 5, 5);
    code_retour := code_retour || 'Vous êtes maintenant protégé par votre dieu pour 5 tours.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
    select into v_pv_lanceur,v_pv_max_lanceur perso_pv, perso_pv_max
    from perso
    where perso_cod = lanceur;
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (3, lanceur, cible, 0.5 * ln(v_pv_cible_m));
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '999')) || ' PX pour cette action.<br>';

    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    return code_retour;
end;
$_$;


ALTER FUNCTION public.dv_mg_protection(integer, integer, integer) OWNER TO delain;
