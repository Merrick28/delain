CREATE or replace FUNCTION public.nv_magie_balrog(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_boule_feu : lance le sort boule de feu         */
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
    v_bloque_magie           integer;
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    pv_cible                 integer;
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 integer; -- PX gagnes
    ligne                    record; -- enregistrements
    pos_lanceur              integer; -- pos_cod du lanceur
    x_lanceur                integer; -- x du lanceur
    y_lanceur                integer; -- y du lanceur
    e_lanceur                integer; -- etage du lanceur
    v_degats                 integer; -- dégats effectués
    reussite_esquive         integer; -- réussite on non de l'esquive
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
    compt                    integer; -- fourre tout
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 68;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible perso_nom
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
    px_gagne := 1;
    -- a partir d'ici on s'amuse
-- on prend la position
    select into pos_lanceur,x_lanceur,y_lanceur,e_lanceur pos_cod,
                                                          pos_x,
                                                          pos_y,
                                                          pos_etage
    from positions,
         perso_position
    where ppos_perso_cod = cible
      and ppos_pos_cod = pos_cod;
-- on commence par la cible
    v_bloque_magie := split_part(magie_commun_txt, ';', 2);
    if v_bloque_magie = 0 then
        ------------------------
-- magie non résistée --
------------------------
        v_degats := lancer_des(3, 10);
        code_retour := code_retour || 'Votre adversaire n''arrive pas à résister au sort.<br>';
    else
        --------------------
-- magie résistée --
--------------------		   
        v_degats := lancer_des(1, 10);
        code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
    end if;
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
    -- on regarde si esquive
    code_retour := code_retour || 'Vous provoquez ' || trim(to_char(v_degats, '99999')) || ' dégâts sur ' ||
                   nom_cible || '<br>';
    texte_evt := texte_evt || ' causant ' || trim(to_char(v_degats, '999999')) || ' dégâts';

    perform insere_evenement(lanceur, cible, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

    select into pv_cible perso_pv
    from perso
    where perso_cod = cible;
    if pv_cible <= v_degats then
        -- on a tué l'adversaire !!
        px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, cible), ';', 1), '999999999999999');
        code_retour := code_retour || 'Vous avez <b>tué</b> ' || nom_cible || '<br><br>';
    else
        code_retour := code_retour || nom_cible || ' a survécu à votre attaque<br><br>';
        update perso set perso_pv = perso_pv - v_degats where perso_cod = cible;
    end if;


-- ensuite on fait les gens autour
    for ligne in select perso_cod, perso_nom, perso_pv
                 from perso,
                      perso_position,
                      positions
                 where perso_actif = 'O'
                   and perso_tangible = 'O'
                   and ppos_perso_cod = perso_cod
                   and ppos_pos_cod = pos_cod
                   and pos_cod = pos_lanceur
                   and perso_cod != cible
        loop
            texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
            v_degats := lancer_des(1, 10);
            -- on regarde si esquive
            code_retour := code_retour || 'Sur <b>' || ligne.perso_nom || '</b>, vous provoquez ' ||
                           trim(to_char(v_degats, '99999')) || ' dégâts.<br>';
            texte_evt := texte_evt || ' causant ' || trim(to_char(v_degats, '999999')) || ' dégâts';

            perform insere_evenement(lanceur, ligne.perso_cod, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

            if ligne.perso_pv <= v_degats then
                -- on a tué l'adversaire !!
                px_gagne := px_gagne + to_number(split_part(tue_perso_final(lanceur, ligne.perso_cod), ';', 1),
                                                 '99999999999999999');
                code_retour := code_retour || 'Vous avez <b>tué</b> ' || ligne.perso_nom || '<br><br>';
            else
                code_retour := code_retour || ligne.perso_nom || ' a survécu à votre attaque<br><br>';
                update perso set perso_pv = perso_pv - v_degats where perso_cod = ligne.perso_cod;
            end if;

        end loop;
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '999')) || ' PX pour cette action.<br>';
    return code_retour;
end;

$_$;


ALTER FUNCTION public.nv_magie_balrog(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_balrog(integer, integer, integer) IS 'Lancer de Boules de feu';
