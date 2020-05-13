CREATE FUNCTION public.dv_mg_aimant(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function dv_mg_aimant : lance le sort aimant                  */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        doit être fixé à 3                                     */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   18/04/2006 : Rajout de l'évènement qui va bien              */
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
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
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
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 73;
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
    magie_commun_txt := magie_commun_dieu(lanceur, cible, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    select into v_x,v_y,v_e pos_x,
                            pos_y,
                            pos_etage
    from positions,
         perso_position
    where ppos_perso_cod = lanceur
      and ppos_pos_cod = pos_cod;
    cpt_brouzoufs := 0;
    for ligne in select pos_cod
                 from positions
                 where pos_etage = v_e
                   and pos_x between (v_x - 1) and (v_x + 1)
                   and pos_y between (v_y - 1) and (v_y + 1)
        loop
            select into tmp_brouzoufs sum(por_qte)
            from or_position
            where por_pos_cod = ligne.pos_cod
              and por_palpable = 'O';
            if tmp_brouzoufs is not null then
                cpt_brouzoufs := cpt_brouzoufs + tmp_brouzoufs;
            end if;
            delete from or_position where por_pos_cod = ligne.pos_cod;
        end loop;
    update perso set perso_po = perso_po + cpt_brouzoufs where perso_cod = lanceur;
    code_retour := code_retour || 'Vous avez récupéré ' || trim(to_char(cpt_brouzoufs, '999999999999999999')) ||
                   ' dans votre inventaire.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible]';
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


ALTER FUNCTION public.dv_mg_aimant(integer, integer, integer) OWNER TO delain;
