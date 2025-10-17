--
-- Name: f_passe_niveau(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_passe_niveau(integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/********************************************************/
/* fonction f_passe_niveau: effectue toutes les actions */
/*   liées au passage de niveau et retourne une chaine  */
/*   exploitable en html                                */
/* on passe en paramètres :                             */
/*   $1 = le perso_cod du passage                       */
/*   $2 = le type d'amélioration choisie                */
/*        1 = temps                                     */
/*        2 = dégats distance                           */
/*        3 = régénération                              */
/*        4 = dégats corps à corps                      */
/*        5 = armure                                    */
/*        6 = vue                                       */
/*        7 = réparation                                */
/*        8 = sorts mémorisables                        */
/*        9 = vampirisme                                */
/*       10 = af lvl 2                                  */
/*       11 = af lvl 3                                  */
/*       12 = feinte                                    */
/*       13 = feinte lvl 2                              */
/*       14 = feinte lvl 3                              */
/*       15 = Coup de grace                             */
/*       16 = Coup de grace lvl 2                       */
/*       17 = Coup de grace lvl 3                       */
/*       18 = af                                        */
/*       19 = réceptacle magique                        */
/*       20 = amel memo                                 */
/*       21 -> 23 : bour portant                        */
/*       24 -> 26 : tir précis                          */
/*       27 -> force                                    */
/*       28 -> dext.                                    */
/*       29 -> constit                                  */
/*       30 -> intelligence                             */
/********************************************************/
declare
    code_retour      text; -- chaine de retour
    personnage alias for $1; -- perso_cod
    amel alias for $2; -- type amélioration
    v_pa             integer; -- nombre de PA du perso
    v_niveau_actu    integer; -- niveau actuel du perso
    v_limite_niveau  integer; -- PX nécessaires pour monter de niveau
    v_px             numeric; -- PX du perso
    v_con            integer;
    gain_pv          integer; -- gain en PV du perso
    pv_max_actuel    integer; -- PV max du perso
    pv_max_theorique integer; -- PV max theorique du perso
    temp             integer; -- vraible passe partout
    texte_evt        text; -- texte pour évènements
    v_resultat       f_resultat; -- resultat de f_carac_ameliore()
begin
    -- On rend la fonction atomique, pour éviter les problèmes de double clic.
    perform guard('f_modif_carac', personnage);
    -- Ne pas oublier le perform release à chaque branche de sortie de fonction !


    -- Un minimum de vérification !!!
    select into v_pa,
        v_niveau_actu,
        v_limite_niveau,
        v_px,
        pv_max_actuel,
        v_con perso_pa,
              perso_niveau,
              limite_niveau(perso_cod),
              perso_px,
              perso_pv_max,
              f_carac_base(perso_cod, 'CON') as perso_con
    from perso
    where perso_cod = personnage
      and perso_actif = 'O';
    if not found then
        code_retour := '<p>Erreur ! Perso non trouvé !';
        perform release('f_modif_carac', personnage);
        return code_retour;
    end if;
    if v_px < v_limite_niveau then
        code_retour := '<p>Erreur ! Pas assez de PX pour monter de niveau !';
        perform release('f_modif_carac', personnage);
        return code_retour;
    end if;
    if v_pa < getparm_n(8) then
        code_retour := '<p>Erreur ! Pas assez de PA pour monter de niveau !';
        perform release('f_modif_carac', personnage);
        return code_retour;
    end if;
    if amel is null then
        code_retour := '<p>Erreur ! Vous n''avez pas choisi d''amélioration !';
        perform release('f_modif_carac', personnage);
        return code_retour;
    end if;

    --------------------------------------------------
    -- réalise l'amélioration de caracs
    --------------------------------------------------
    v_resultat := f_carac_ameliore(personnage, amel);
    if v_resultat.etat != 0 then
        -- il y a eu un problème lors de l'amelioration de caract
        perform release('f_modif_carac', personnage);
        return v_resultat.code_retour;
    end if;

    --------------------------------------------------
    -- tout semble correct, l'amélioration a été faite
    -- Reste la gestion des PX/Niveau/PA
    --------------------------------------------------
    -- On recharge les pv_max, ils ont pû être déjà mis à jour si amélioration de la constit
    select into pv_max_actuel perso_pv_max from perso where perso_cod = personnage;
    -- On maximalise si le perso est trop loin des standards
    temp := round(floor(v_con / 4));
    gain_pv := lancer_des(1, temp);
    gain_pv := gain_pv + 1;
    pv_max_theorique := cast((2 * v_con + (v_niveau_actu - 1) * (v_con + 12) / 8) as integer);
    if pv_max_actuel + temp + 1 < pv_max_theorique then
        gain_pv := max(gain_pv, temp+1);
    end if;

    update perso
    set perso_pa     = perso_pa - getparm_n(8),
        perso_niveau = perso_niveau + 1,
        perso_pv_max = pv_max_actuel + gain_pv
    where perso_cod = personnage;
    v_niveau_actu := v_niveau_actu + 1;

    -- si le joueur à fait une amélioration de CONSTIT, il a aussi eu un gain de 3PV (voir f_carac_ameliore), pour l'affichage et les events on les ajoutes
    if amel = 29 then
        gain_pv := gain_pv + 3;
    end if;
    --------------------------------------------------
    -- Tout c'est bien passé, on met le texte de passage de niveau et les events!
    code_retour := '<p>Vous êtes maintenant niveau <b>' || trim(to_char(v_niveau_actu, '999999')) || '</b>.<br>';
    code_retour := code_retour || 'Vous gagnez <b>' || trim(to_char(gain_pv, '999999')) || '</b> points de vie.<br>';
    texte_evt := '[perso_cod1] s''est entrainé, est passé au niveau ' || trim(to_char(v_niveau_actu, '999'));
    texte_evt := texte_evt || ' et a gagné ' || trim(to_char(gain_pv, '999')) || ' points de vie.';
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible)
    values (nextval('seq_levt_cod'), 11, now(), 1, personnage, texte_evt, 'O', 'N');
    texte_evt := 'Passage de niveau, option choisie : ' || trim(to_char(amel, '999999'));
    insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu,
                          levt_visible)
    values (nextval('seq_levt_cod'), -1, now(), 1, personnage, texte_evt, 'O', 'N');

    perform release('f_modif_carac', personnage);
    return code_retour;
end;
$_$;


ALTER FUNCTION public.f_passe_niveau(integer, integer) OWNER TO delain;