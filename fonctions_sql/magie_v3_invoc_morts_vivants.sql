CREATE or replace FUNCTION public.magie_v3_invoc_morts_vivants(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/********************************************************************/
/* function magie_v3_invoc_morts_vivants : invocation de morts-vivants */
/*  magique                                                            */
/* On passe en paramètres                                              */
/*   $1 = lanceur                                                      */
/*   $2 = cible                                                        */
/*   $3 = type lancer                                                  */
/*        0 = rune                                                     */
/*        1 = mémo                                                    */
/* Le code sortie est une chaine html utilisable directement          */
/**********************************************************************/
/* Créé le 16/10/2024                                                 */
/* Liste des modifications :                                          */
/*                                                                    */
/*                                                                   */
/*********************************************************************/

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
    has_bloque               integer; -- compétence blocage ?
    v_pos_cible              integer; -- position de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 integer; -- PX gagnes
    duree                    integer; -- Durée du sort
    temp_nb_proj             numeric; -- nb temp de projectiles
    nb_proj                  integer; -- nb de projectiles
    proj_bloque              integer; -- projectile bloqué ?
    degats_proj              integer; -- degats du projectile
    niveau_sort              integer; -- niveau du sort
    nb_zombies              integer; -- Nombre de zombies à invoquer
    nb_squelettes           integer; -- Nombre de squelettes de géants à invoquer
    temp_ia                  text;
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
    magie_commun_txt         text; -- texte pour magie commun
    res_commun               integer; -- partie 1 du commun
    distance_cibles          integer; -- distance entre lanceur et cible
    ligne_rune               record; -- record des rune à dropper
    temp_ameliore_competence text; -- chaine temporaire pour amélioration

-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
    des                      integer; -- lancer de dés
    compt                    integer; -- fourre tout
    v_degats                 integer;
    v_monstre                integer;

begin
    -- return 'fini';
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 300;
-- les px
    px_gagne := 1;
-- Lancer de dés pour le nombre de zombies (1d6)
    nb_zombies := trunc(random() * 5 + 1);
-- Lancer de dés pour le nombre de squelettes de géants (1d3)
    nb_squelettes := trunc(random() * 2 + 1);
    -------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------        
    select into niveau_sort,nom_sort sort_niveau, sort_nom from sorts where sort_cod = num_sort;
    select into nom_cible perso_nom
    from perso
    where perso_cod = cible;
    magie_commun_txt := magie_commun(lanceur, cible, type_lancer, num_sort);
    res_commun := split_part(magie_commun_txt, ';', 1);
    if res_commun = 0 then
        code_retour := split_part(magie_commun_txt, ';', 2);
        return code_retour;
    end if;
    code_retour := split_part(magie_commun_txt, ';', 3);
    px_gagne := 1;
    select into v_pos_cible ppos_pos_cod
    from perso_position
    where ppos_perso_cod = cible;
    
    -- Lancer de dés pour déterminer le nombre de zombies (1d6) et de squelettes de géants (1d3)
    total_monstres := trunc(random() * 5 + 1) + trunc(random() * 2 + 1); -- Somme des deux lancers de dés

    -- Boucle d'invocation des monstres
    FOR i IN 1..total_monstres LOOP
        type_monstre := trunc(random() * 3 + 1); -- Détermine aléatoirement le type de monstre à invoquer (1-3 pour zombies, 4 pour squelette)      
        IF type_monstre <= 3 THEN
            v_monstre := cree_monstre_pos(210, v_pos_cible); -- 210 est le code pour les zombies
        ELSE
            v_monstre := cree_monstre_pos(1457, v_pos_cible); -- 1457 est le code pour les squelettes de géants
        END IF;
    END LOOP;

    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (2, lanceur, cible, 2);
    code_retour := code_retour || 'Vous avez invoqué des mort-vivants.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '999')) || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort || ' sur [cible].';
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
    code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_invoc_farfa(integer, integer, integer) OWNER TO delain;
