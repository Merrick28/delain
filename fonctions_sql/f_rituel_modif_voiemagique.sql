--
-- Name: f_rituel_modif_voiemagique(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_rituel_modif_voiemagique(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/********************************************************/
/* fonction f_rituel_modif_voiemagique: effectue toutes */
/* les actions liées aux modifications de carac et      */
/* retourne une chaine exploitable en html              */
/* on passe en paramètres :                             */
/*   $1 = le perso_cod du passage                       */
/*   $2 = le type de détérioration choisie              */
/*   $3 = le type d'amélioration choisie                */
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
    code_retour text;               -- chaine de retour
    personnage alias for $1;        -- perso_cod
    voie alias for $2;              -- nouvelle voie
    v_perso_voie_magique integer;   -- voie magique actuelle
    v_perso_po integer;             -- nombre de PO du perso
    v_perso_nb_obj integer;         -- nombre de d'ojet du rituel dans le sac du perso
    temp integer;                   -- vraible passe partout
    v_mvoie_libelle text;           -- nom de la nouvelle voie
    texte_evt text;                 -- texte pour évènements
begin

    select into v_mvoie_libelle mvoie_libelle from voie_magique where mvoie_cod = voie ;
    if not found then
        code_retour := '<p>La nouvelle voie choisie est inconnue!';
        return code_retour;
    end if;

    -- on verifie que le service de rituel est bien configuré comme ouvert!
    if getparm_t(139) <> 'O' then
        code_retour := '<p>L''officine est fermée, le rituel n''est pas faisable actuellement!';
        return code_retour;
    end if;

    -- Un minimum de vérification (cout nécéssaire etc...) !!!
    -- d'abord on le perso
    select into v_perso_po, v_perso_voie_magique
            perso_po,
            perso_voie_magique
        from perso
        where perso_cod = personnage
        and perso_actif = 'O';
    if not found then
        code_retour := '<p>Erreur ! Perso non trouvé !';
        return code_retour;
    end if;

    -- Vérification Brouzoufs
    if v_perso_po < getparm_n(137) then
        code_retour := '<p>Vous n''avez pas assez de brouzouf !';
        return code_retour;
    end if;

    -- comptage des items dans le sac du perso nécéssaire au rituel
    select into v_perso_nb_obj count(*) perso_nb_obj from perso_objets join objets on obj_cod = perobj_obj_cod where perobj_perso_cod = personnage and obj_gobj_cod = getparm_n(135) ;
    if not found then
        code_retour := '<p>Vous n''avez d''objets pour le rituel !';
        return code_retour;
    end if;
    if v_perso_nb_obj < getparm_n(136) then
        code_retour := '<p>Vous n''avez assez d''objets pour le rituel !';
        return code_retour;
    end if;

    -- vérification du délai entre 2 rituels!
    select into temp prcarac_cod from perso_rituel_caracs where prcarac_perso_cod=personnage and prcarac_date_rituel > now()-((getparm_n(138)::text)||' DAYS')::interval and prcarac_type_rituel=2 ;
    if found then
        code_retour := '<p>Trop peu de temps vous sépare du dernier rituel pour en faire un nouveau !!';
        return code_retour;
    end if;

    --------------------------------------------------
    -- réalise la modification de voie
    --------------------------------------------------
    update perso set perso_voie_magique=voie where perso_cod=personnage;


    -- Tout c'est bien passé (pour la modification), on met le texte d'events!
    texte_evt := '[perso_cod1] a réalisé un rituel de changement de voie magique.';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
            values(nextval('seq_levt_cod'),11,now(),1,personnage,texte_evt,'O','N');

    -- texte invisible au joueur mais qui permettra eventuelelemnt un debugage
    texte_evt := 'Rituel de changement de voie, option détériorée : '||trim(to_char(v_perso_voie_magique,'999999'));
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
            values(nextval('seq_levt_cod'),-1,now(),1,personnage,texte_evt,'O','N');

    -- texte invisible au joueur mais qui permettra eventuelelemnt un debugage
    texte_evt := 'Rituel de changement de voie, option améliorée : '||trim(to_char(voie,'999999'));
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
            values(nextval('seq_levt_cod'),-1,now(),1,personnage,texte_evt,'O','N');

    --------------------------------------------------
    -- tout semble correct, le rituel a été fait
    -- 1 détérioration au profit d'1 amélioration
    --------------------------------------------------
    update perso set perso_po = perso_po - getparm_n(137) where perso_cod = personnage;

    -- suppression des objets qui ont servit au rituel
		perform f_del_objet(obj_cod)
        from perso_objets join objets on obj_cod = perobj_obj_cod
        where perobj_perso_cod = personnage and obj_gobj_cod = getparm_n(135)
        limit getparm_n(136);

    -- garder une trace du rituel
		insert into perso_rituel_caracs( prcarac_perso_cod, prcarac_amelioration_carac_cod, prcarac_diminution_carac_cod, prcarac_type_rituel) values (personnage, voie, v_perso_voie_magique, 2);

    code_retour := '<p>Vous avez réalisé un <b>rituel de modification</b> de voie magique, votre nouvelle voie est: <b>' || v_mvoie_libelle || '<b>.<br>';

    return code_retour;
end;$_$;


ALTER FUNCTION public.f_rituel_modif_voiemagique(integer, integer) OWNER TO delain;