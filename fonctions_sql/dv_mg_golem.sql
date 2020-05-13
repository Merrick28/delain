CREATE FUNCTION public.dv_mg_golem(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function dv_mg_golem : lance le sort golem de brouzoufs       */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 06/01/2010                                            */
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
    cible alias for $2; -- pos_cod
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort         integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa          integer; -- Cout en PA du sort
    px_gagne         text; -- PX gagnes
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt text; -- texte pour magie commun
    res_commun       integer; -- partie 1 du commun
    v_monstre        integer; --numéro du monstre créé
    nb_monstre       integer;
    num_monstre      integer;
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des              integer; -- lancer de dés
    compt            integer; -- fourre tout
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 162;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_sort sort_nom
    from sorts
    where sort_cod = num_sort;
    magie_commun_txt := magie_commun_dieu_case(lanceur, cible, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);

-- OK on créé le golem de brouzoufs	
    v_monstre := cree_monstre_pos(531, cible);
    code_retour := code_retour || '<br>Vous avez invoqué avec succès un golem de brouzoufs.';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || '.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible, levt_attaquant)
    values (nextval('seq_levt_cod'), 14, now(), 1, lanceur, texte_evt, 'O', 'O', lanceur);
    return code_retour;
end;
$_$;


ALTER FUNCTION public.dv_mg_golem(integer, integer, integer) OWNER TO delain;
