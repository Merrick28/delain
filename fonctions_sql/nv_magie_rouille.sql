CREATE FUNCTION public.nv_magie_rouille(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function Rouille : lance le sort rouille, qui détériore       */
/*   l'équipement porté                                          */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 16/08/2006                                            */
/* Liste des modifications :                                     */
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
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    obj_cible                integer; -- objet de la cible qui va rouiller
    obj_cible_etat           numeric; -- impact après rouille
    obj_cible_nom            text; -- nom de l'objet cible
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
    ligne_rune               record; -- record des runes à dropper
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
begin
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 117;
-- les px
    px_gagne := 0;
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
    select into nom_cible,v_pv_cible perso_nom, perso_pv_max
    from perso
    where perso_cod = cible;
    select into obj_cible,obj_cible_etat,obj_cible_nom obj_cod, obj_etat, obj_nom
    from objets,
         perso_objets
    where perobj_perso_cod = cible
      and perobj_equipe = 'O'
      and perobj_obj_cod = obj_cod
    order by random()
    limit 1;

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

--Pas d'objet porté
    if obj_cible is null then
        code_retour := code_retour || 'Le sort n''a aucune conséquence sur cette cible<br>';
        return code_retour;
    end if;

--Pas de résistance à la magie pour ce sort	

    code_retour := code_retour || 'L''équipement porté par votre cible subit un coup important.<br>';

    obj_cible_etat := obj_cible_etat - 20;
--L'objet se détruit
    if obj_cible_etat <= 0 then
        texte_evt := 'Votre ' || obj_cible_nom || ' a fini en morceaux sous le coup d''un sort !';

        compt := f_del_objet(obj_cible);

        insert into ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
        values (75, now(), cible, texte_evt, 'N', 'O');


--l'objet résiste
    else
        update objets set obj_etat = obj_cible_etat where obj_cod = obj_cible;
        texte_evt := 'Votre ' || obj_cible_nom ||
                     ' a subit un sort direct, rendant son utilisation plus hasardeuse sans réparation !';

        insert into ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
        values (75, now(), cible, texte_evt, 'N', 'O');
    end if;


--Rajout des Pxs gagnés
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee)
    values (2, lanceur, cible, 3 * ln(v_pv_cible));

    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible] ';

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


ALTER FUNCTION public.nv_magie_rouille(integer, integer, integer) OWNER TO delain;
COMMENT ON FUNCTION public.nv_magie_rouille(integer, integer, integer) IS 'En cours d''écriture pour les futurs monstres';
