CREATE FUNCTION public.nv_magie_glyphe_resurrection(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    STRICT
AS
$_$/*****************************************************************/
/* function magie_glyphe_resurrection :                          */
/*  cree un glyphe de resurrection sur la position du lanceur    */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 29/01/2011                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
    code_retour              text; -- chaine html de sortie
    nom_sort                 text; -- nom du sort pour evt
    texte_evt                text; -- texte pour évènement
-------------------------------------------------------------
-- variables concernant le lanceur  
-------------------------------------------------------------
    lanceur alias for $1; -- perso_cod du lanceur
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
    temp_glyphe              integer; -- pour voir si glyphe existe
    num_objet                integer; -- le numéro d'objet qui va être créé
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
    num_sort := 165;
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
    px_gagne := split_part(magie_commun_txt, ';', 4);
-- on vérifie que le lanceur n''a pas déjà un glyphe de résurrection
    select into temp_glyphe pglyphe_resurrection
    from perso_glyphes
    where pglyphe_perso_cod = cible
      and pglyphe_type = 'R';
    if found then
        code_retour := '<p>Le sort a échoué car vous avez déjà un glyphe de résurrection !</p>
        Si vous l''avez perdu, une analyse mystique pourrait vous aider à le retrouver.';
        return code_retour;
    end if;

    num_objet := nextval('seq_obj_cod');
    insert into objets (obj_cod, obj_gobj_cod) values (num_objet, 859);
    update objets
    set obj_nom           = 'Glyphe de résurrection de ' || nom_cible,
        obj_nom_generique = 'Glyphe de résurrection de ' || nom_cible
    where obj_cod = num_objet;
    insert into perso_glyphes (pglyphe_perso_cod, pglyphe_obj_cod, pglyphe_type)
    values (cible, num_objet, 'R');

    --update perso_glyphes set pglyphe_resurrection = num_objet where pglyphe_perso_cod = cible;
    --if not found then
    --    insert into perso_glyphes (pglyphe_perso_cod, pglyphe_resurrection) 
    --        values (cible, num_objet);
    --end if;
    insert into objet_position (pobj_obj_cod, pobj_pos_cod)
    values (num_objet, (select ppos_pos_cod from perso_position where ppos_perso_cod = cible));
    code_retour := code_retour ||
                   '<br>Vous avez créé sur le sol un glyphe de résurrection. Vous rejoindrez ce glyphe à votre prochaine mort.';
    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
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


ALTER FUNCTION public.nv_magie_glyphe_resurrection(integer, integer, integer) OWNER TO delain;
