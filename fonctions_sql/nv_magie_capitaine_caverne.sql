CREATE or replace FUNCTION public.nv_magie_capitaine_caverne(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function magie_capitaine_caverne : lance capitaine_caverne    */
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
    code_retour      text; -- chaine html de sortie
    texte_evt        text; -- texte pour évènements
    nom_sort         text; -- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible        text; -- nom de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort         integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    px_gagne         text; -- PX gagnes
    poids_objet      numeric; -- poids de l’objet créé
    code_objet       integer; -- code de l’objet créé
    nom_objet        text; -- nom de l’objet créé
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt text; -- texte pour magie commun
    res_commun       integer; -- partie 1 du commun
    v_bloque_magie   integer; -- vérif si résistance magique
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    v_pv_cible       integer;
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
    num_sort := 8;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
    select into nom_cible, v_pv_cible perso_nom, perso_pv_max
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

    v_bloque_magie := split_part(magie_commun_txt, ';', 2);

    -- Création de l’objet
    SELECT INTO code_objet, nom_objet, poids_objet gobj_cod, gobj_nom, gobj_poids
    FROM objet_generique
    WHERE (v_bloque_magie = 0 AND gobj_cod IN (60, 61, 62, 63, 64, 65, 66, 163, 256, 560))
       OR                                                                                             -- Magie non résistée
        (v_bloque_magie <> 0 AND gobj_cod IN (67, 68, 69, 70, 71, 165, 349, 414, 253, 257, 255, 254)) -- Magie résistée
    order by random()
    limit 1;
    perform cree_objet_perso(code_objet, cible, 'N');

    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';

    if v_bloque_magie = 0 then -- magie non résistée --
        code_retour := code_retour || 'Votre adversaire n’arrive pas à résister au sort.<br>';
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (2, lanceur, cible, 3 * ln(v_pv_cible));
    else -- magie résistée --
        code_retour := code_retour || 'Votre adversaire résiste partiellement au sort.<br>';
        insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
        values (2, lanceur, cible, 1.5 * ln(v_pv_cible));
        texte_evt := texte_evt || ', qui a partiellement résisté au sort.';
    end if;

    code_retour := code_retour || '<br>Vous avez créé l’objet <b>' || nom_objet || '</b>, pesant ' ||
                   poids_objet::text || ', dans l’inventaire de ' || nom_cible || '.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';

    perform insere_evenement(lanceur, cible, 14, texte_evt, 'O', '[sort_cod]=' || num_sort::text);

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_capitaine_caverne(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_capitaine_caverne(integer, integer, integer) IS 'Lance Capitaine Caverne';
