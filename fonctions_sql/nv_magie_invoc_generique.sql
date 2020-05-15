CREATE or replace FUNCTION public.nv_magie_invoc_generique(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    STRICT
AS
$_$/*****************************************************************/
/* function nv_magie_invoc_generique : invoque des monstres      */
/* Le type de monstre invoqué dépend du lanceur. Les associations*/
/* entre le lanceur et le type invoqué sont dans la table        */
/* invocation.                                                   */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 27/01/2011                                            */
/* Liste des modifications :                                     */
/*   - 06/02/2011 Bleda: Retrait des événements                  */
/*                                                               */
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
    temp_ia                  text;
    type_monstre             integer; -- Le type de monstre invoqué.
    nombre_monstres          integer; -- Le nombre de monstres à invoquer
    duree_vie                integer; -- Durée de vie en jours
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
    -------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort 
    num_sort := 89;
-- les px
    px_gagne := 1;
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
    select into type_monstre, nombre_monstres, duree_vie invoc_monstre_gmon_cod,
                                                         invoc_nombre,
                                                         case when invoc_duree = 0 then 999 else invoc_duree end -- 999 jours max.
    from invocation
    where invoc_lanceur_gmon_cod =
          (select perso_gmon_cod from perso where perso_cod = lanceur)
    order by random(); -- On en prend un au hasard s''il y en a plusieurs
    if not found then
        type_monstre = 1; -- Rat commun
        nombre_monstres = 1;
        duree_vie = 1;
    end if;

    for compt in 1..nombre_monstres
        loop
            v_monstre := cree_monstre_pos(type_monstre, v_pos_cible);
            update perso
            set perso_cible = cible,
                perso_dfin  = now() +
                              duree_vie * '1 day'::interval
            where perso_cod = v_monstre;
            temp_ia := ia_monstre(v_monstre);
        end loop;
    insert into action (act_tact_cod, act_perso1, act_perso2, act_donnee) values (2, lanceur, cible, 2);
    code_retour := code_retour || 'Vous avez invoqué un monstre qui attaque votre cible.<br>';
    code_retour := code_retour || '<br>Vous gagnez ' || trim(to_char(px_gagne, '999')) || ' PX pour cette action.<br>';
    -- Bleda 06/02/2001: On retire l'invocation des événements du lanceur et de la cible.
--         texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible], causant '||trim(to_char(v_degats,'999999'))||' dégats.';
--         insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
--         values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
--         if (lanceur != cible) then
--             insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
--             values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
--         end if;

    ---------------------------
    -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
    ---------------------------
    code_retour := code_retour || execute_effet_auto_mag(lanceur, cible, num_sort, 'L') || execute_effet_auto_mag(cible, lanceur, num_sort, 'C');

    return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_invoc_generique(integer, integer, integer) OWNER TO delain;
