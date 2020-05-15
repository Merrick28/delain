CREATE or replace FUNCTION public.nv_magie_transfert_energie(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_mercurochrome : lance le sort transfert energie*/
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
    code_retour               text; -- chaine html de sortie
    texte_evt                 text; -- texte pour évènements
    nom_sort                  text; -- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_pv_lanceur              integer; -- pv du lanceur
    v_pv_max_lanceur          integer; -- pv max du lanceur
    nouveau_pv_lanceur        integer; -- nouveau pv du lanceur
    v_int_lanceur             integer; -- int du lanceur
    nom_lanceur               text; -- nom du lanceur
    v_niv_lanceur             integer; -- niveau du lanceur
    v_voie_magique            integer; -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                 text; -- nom de la cible
    sexe_cible                text; -- sexe de la cible ... Et non, la réponse n'est pas "grand"
    v_pv                      integer; -- PV de la cible
    v_pv_max                  integer; -- PV max de la cible
    nouveau_pv                integer; -- nouveau pv cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                  integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                   integer; -- Cout en PA du sort
    px_gagne                  text; -- PX gagnes
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt          text; -- texte pour magie commun
    res_commun                integer; -- partie 1 du commun
    distance_cibles           integer; -- distance entre lanceur et cible
    ligne_rune                record; -- record des rune à dropper
    temp_ameliore_competence  text;
    -- chaine temporaire pour amélioration
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                       integer; -- lancer de dés
    compt                     integer; -- fourre tout
    compt_lanceur             integer; -- fourre tout
    temp_bonus                integer; -- comptage temp
    temp_bonus_lanceur        integer; -- comptage temp
    amel_pv                   integer; -- nb de PV ajoutés
    perte_pv                  integer; -- dégats infligés
    v_act_numero              integer;
    pourcent_blessure         numeric(5, 2);
    pourcent_blessure_lanceur numeric(5, 2);
    bonus_niv                 integer;
begin
    v_act_numero := nextval('seq_act_numero');
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 134;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
-- on récupère les infos
    select into v_pv,v_pv_max perso_pv, perso_pv_max
    from perso
    where perso_cod = cible;
    select into v_pv_lanceur,v_pv_max_lanceur,v_int_lanceur,v_niv_lanceur,v_voie_magique perso_pv,
                                                                                         perso_pv_max,
                                                                                         perso_int,
                                                                                         perso_niveau,
                                                                                         perso_voie_magique
    from perso
    where perso_cod = lanceur;
-- on regarde les % de blessures
    pourcent_blessure := ((v_pv * 100) / v_pv_max) / 100.00;
    pourcent_blessure_lanceur := ((v_pv_lanceur * 100) / v_pv_max_lanceur) / 100.00;
    -- on applique les % à l'autre pour calculer des pvies temporaires
-- sauf si le lanceur est moins blessé que sa cible
    if pourcent_blessure <= pourcent_blessure_lanceur then
        return 'Erreur: Vous ne pouvez lancer ce sort que sur une cible moins blessée que vous !';
    end if;

    select into nom_cible,sexe_cible perso_nom, perso_sex
    from perso
    where perso_cod = cible;
    select into nom_lanceur,sexe_cible perso_nom, perso_sex
    from perso
    where perso_cod = lanceur;
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
-- on récupère les infos
    select into v_pv,v_pv_max perso_pv, perso_pv_max
    from perso
    where perso_cod = cible;
    select into v_pv_lanceur,v_pv_max_lanceur,v_int_lanceur,v_niv_lanceur,v_voie_magique perso_pv,
                                                                                         perso_pv_max,
                                                                                         perso_int,
                                                                                         perso_niveau,
                                                                                         perso_voie_magique
    from perso
    where perso_cod = lanceur;
-- on regarde les % de blessures
    pourcent_blessure := ((v_pv * 100) / v_pv_max) / 100.00;
    pourcent_blessure_lanceur := ((v_pv_lanceur * 100) / v_pv_max_lanceur) / 100.00;
    -- on applique les % à l'autre pour calculer des pvies temporaires
-- sauf si le lanceur est moins blessé que sa cible


    if pourcent_blessure > pourcent_blessure_lanceur then

        nouveau_pv := floor(v_pv_max * pourcent_blessure_lanceur);
        -- on calcule la perte temporaire de pvies pour la cible
        -- si c'est supérieur à 1.75 int du lanceur on limite à 1.75 * int + niv/5
        -- en cas de guerisseur la limite est de 1.90 du lanceur
        compt := v_pv - nouveau_pv;
        bonus_niv := floor(v_niv_lanceur / 5);
        temp_bonus := floor(v_int_lanceur * 1.75);
        -- impacte de la voie magique
        if v_voie_magique = 1 then
            temp_bonus := floor(v_int_lanceur * 1.90);
            code_retour := code_retour ||
                           '<br>Votre connnaissance de guérisseur accroit votre puissance dans ce  sort.<br>';
            temp_bonus := temp_bonus + bonus_niv;
        end if;
        if compt > temp_bonus then
            compt = temp_bonus;
        end if;
        -- on traite le gain du lanceur
        -- on calcule le gain temporaire de pvies pour le lanceur
        -- si c'est supérieur à 1.75 int du lanceur on limite à 1.75 int
        -- en cas de guerisseur la limite est de 1.90 du lanceur
        nouveau_pv_lanceur := floor(v_pv_max_lanceur * pourcent_blessure);
        compt_lanceur := nouveau_pv_lanceur - v_pv_lanceur;
        if compt_lanceur > temp_bonus then
            compt_lanceur = temp_bonus;
        end if;
        -- compt et compt_lanceur contienne donc les deux variations de pvies
        -- remise à zero de 4 variables pour réutilisation
        nouveau_pv := 0;
        nouveau_pv_lanceur := 0;
        temp_bonus := 0;
        temp_bonus_lanceur := 0;
    else
        compt_lanceur := 0;
        compt := 0;
    end if;
-- on traite les gains du lanceur
    nouveau_pv_lanceur := v_pv_lanceur + compt_lanceur;
    if nouveau_pv_lanceur > v_pv_max_lanceur then
        compt_lanceur := v_pv_max_lanceur - v_pv_lanceur;
        nouveau_pv_lanceur := v_pv_max_lanceur;
    end if;
    -- on traite la perte de pvies de la cible, sachant que comme on agit
-- par % elle ne peut pas mourrir;
    nouveau_pv := v_pv - compt;
-- on passe à la mise à jour des persos
    update perso set perso_pv = nouveau_pv where perso_cod = cible;
    update perso set perso_pv = nouveau_pv_lanceur where perso_cod = lanceur;
    perform soin_compteur_pvp(lanceur);
    perte_pv := v_pv - nouveau_pv;
    amel_pv := nouveau_pv_lanceur - v_pv_lanceur;
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 0.25 * ln(v_pv) * perte_pv);
    code_retour := code_retour || '<br>' || nom_cible || ' a perdu ' || trim(to_char(perte_pv, '9999999')) ||
                   ' points de vie.<br>';
    code_retour := code_retour || '<br>' || nom_lanceur || ' a regagné ' || trim(to_char(amel_pv, '9999999')) ||
                   ' points de vie.<br>';
    code_retour := code_retour || 'Vous êtes maintenant ';
    code_retour := code_retour || '<b>' || etat_perso(lanceur) || '</b>.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], lui faisant perdre ' ||
                 trim(to_char(perte_pv, '9999')) || ' points de vie.';
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


ALTER FUNCTION public.nv_magie_transfert_energie(integer, integer, integer) OWNER TO delain;
