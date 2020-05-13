CREATE or replace FUNCTION public.nv_magie_soin_majeur(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_soins_importants : lance le sort soins         */
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
    v_compt                  integer; -- fourre tout
    temp_bonus               integer; -- comptage temp
    amel_pv                  integer; -- nb de PV ajoutés
    v_bonus_flux             integer; -- pvies gagnés grace a conscience des flux
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 129;
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
    select into v_perso_int,v_voie_magique perso_int, perso_voie_magique
    from perso
    where perso_cod = lanceur;
    v_compt := 1;
    if v_pv_max > 240 then
        v_compt := 6;
    elseif v_pv_max > 200 then
        v_compt := 5;
    elseif v_pv_max > 160 then
        v_compt := 4;
    elseif v_pv_max > 120 then
        v_compt := 3;
    elseif v_pv_max > 80 then
        v_compt := 2;
    end if;
    -- augmentation due à la voie magique
    if v_voie_magique = 1 then
        v_compt := v_compt + 1;
        code_retour := code_retour || '<br>Votre savoir de guérisseur vous permet de mieux soigner votre cible.<br>';
    end if;
    temp_bonus := v_perso_int * v_compt;
-- on regarde si la personne est malade
    temp_bonus := temp_bonus - valeur_bonus(cible, 'MAL');
    if temp_bonus < 0 then
        temp_bonus := 0;
    end if;
    -- on regarde si le lanceur est sous conscience des flux de soin
    v_bonus_flux := valeur_bonus(lanceur, 'CFS');
    if v_bonus_flux > 0 then
        -- le bonus est triplé pour soin majeur
        v_bonus_flux := v_bonus_flux * 3;
        -- on regarde si le lanceur est la cible sont la meme personne
        if lanceur = cible then
            temp_bonus := temp_bonus + v_bonus_flux;
            bonus_flux := v_bonus_flux;
            code_retour := code_retour || '<br>Vous gagnez ' || bonus_flux ||
                           ' points de vie grace à la conscience des flux de soins.<br>';
        else
            -- sinon on augmente les pvies du lanceur
            select into v_pv,v_pv_max perso_pv, perso_pv_max
            from perso
            where perso_cod = lanceur;
            nouveau_pv := v_pv + v_bonus_flux;
            if nouveau_pv > v_pv_max then
                v_bonus_flux := v_pv_max - v_pv;
                nouveau_pv := v_pv_max;
            end if;
            update perso set perso_pv = nouveau_pv where perso_cod = lanceur;
            bonus_flux := v_bonus_flux;
            code_retour := code_retour || '<br>Vous gagnez ' || bonus_flux ||
                           ' points de vie grace à la conscience des flux de soins.<br>';
        end if;
    end if;
-- on passe à l'augmentation de pvies
    select into v_pv,v_pv_max perso_pv, perso_pv_max
    from perso
    where perso_cod = cible;
    nouveau_pv := v_pv + temp_bonus;
    if nouveau_pv > v_pv_max then
        nouveau_pv := v_pv_max;
    end if;
    update perso set perso_pv = nouveau_pv where perso_cod = cible;
    perform soin_compteur_pvp(cible);
    amel_pv := nouveau_pv - v_pv;

    if amel_pv > 0 then
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (3, lanceur, cible, 3 * ln(amel_pv));
    end if;

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
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible], lui faisant gagner ' ||
                 trim(to_char(amel_pv, '9999')) || ' points de vie.';
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


ALTER FUNCTION public.nv_magie_soin_majeur(integer, integer, integer) OWNER TO delain;
