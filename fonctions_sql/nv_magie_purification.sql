CREATE or replace FUNCTION public.nv_magie_purification(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_frayeur : lance le sort purification           */
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
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
    v_perso_niveau           integer;
    v_voie_magique           integer; -- voie magique du lanceur
    v_int_perso              integer; -- intelligence du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    v_pv                     integer; -- pvie actuel de la cible
    v_pv_max                 integer; -- pvie max de la cible
    sexe_cible               text; -- sexe de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
    v_pa_attaque             integer; -- Pa modifiés
    v_chance_toucher         integer; -- chance de toucher
    v_malus_degats           integer; -- malus aux dégats
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
    v_pv_cible               integer;
    nb_sort_tour             integer;
    v_poison                 integer;
    v_maladie                integer;
    temp_bonus               integer;
    nouveau_pv               integer;
    amel_pv                  integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 150;
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
--
    select into v_int_perso perso_int
    from perso
    where perso_cod = lanceur;
---on passe au sort.suppression de poison et de maladie et maladie de sang
    delete
    from bonus
    where bonus_perso_cod = cible
      and bonus_tbonus_libc = 'POI';
    delete
    from bonus
    where bonus_perso_cod = cible
      and bonus_tbonus_libc = 'MAL';
    delete
    from bonus
    where bonus_perso_cod = cible
      and bonus_tbonus_libc = 'REG';
    delete
    from bonus
    where bonus_perso_cod = cible
      and bonus_tbonus_libc = 'MDS';
    delete
    from bonus
    where bonus_perso_cod = cible
      and bonus_tbonus_libc = 'VEN';
-- soin sur le lanceur
    select into v_pv,v_pv_max,v_voie_magique perso_pv, perso_pv_max, perso_voie_magique
    from perso
    where perso_cod = cible;
    select into v_perso_niveau perso_niveau
    from perso
    where perso_cod = lanceur;
    compt := 1;
    temp_bonus := 0;
    if v_perso_niveau > 8 then
        v_perso_niveau := 8;
    end if;
    while compt <= v_perso_niveau
        loop
            temp_bonus := temp_bonus + lancer_des(1, 4);
            compt := compt + 1;
        end loop;
-- bonus de la voie magique
    if v_voie_magique = 1 then
        temp_bonus := temp_bonus + 10;
    end if;
-- on passe à l'augmentation de pvies
    nouveau_pv := v_pv + temp_bonus;
    if nouveau_pv > v_pv_max then
        nouveau_pv := v_pv_max;
    end if;
    update perso set perso_pv = nouveau_pv where perso_cod = cible;
    amel_pv := nouveau_pv - v_pv;
    code_retour := code_retour || '<br>' || nom_cible || ' a regagné ' || trim(to_char(amel_pv, '9999')) ||
                   ' points de vie.<br>';
    if lanceur = cible then
        code_retour := code_retour || 'Vous êtes maintenant ';
    elsif sexe_cible = 'M' then
        code_retour := code_retour || 'Il est maintenant ';
    else
        code_retour := code_retour || 'Elle est maintenant ';
    end if;
    code_retour := code_retour || '<b>' || etat_perso(cible) || '</b>.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], lui faisant gagner ' ||
                 trim(to_char(amel_pv, '9999')) || ' points de vie.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
--  mise à jour des bonus       
    insert into bonus (bonus_perso_cod, bonus_tbonus_libc, bonus_valeur, bonus_nb_tours)
    values (cible, 'REG', v_int_perso, 3);
-- 	insert into action (act_tact_cod,act_perso1,act_perso2,act_donnee) values (2,lanceur,cible,5*ln(v_pv_cible));
    code_retour := code_retour || '<br>' || nom_cible || ' a un bonus de ' || trim(to_char(v_int_perso, '99')) ||
                   ' en regénération pendant trois tours et n''a plus de malus de maladie ni de poison.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant, levt_cible)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur, cible);
    ---------------------------
    -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
    ---------------------------
    code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_purification(integer, integer, integer) OWNER TO delain;
