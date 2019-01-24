--
-- Name: f_rituel_modif_caracs(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_rituel_modif_caracs(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/********************************************************/
/* fonction f_rituel_modif_caracs: effectue toutes      */
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
    demel alias for $2;              -- type détérioration
    amel alias for $3;              -- type amélioration
    v_perso_po integer;                       -- nombre de PO du perso
    v_perso_nb_obj integer;                       -- nombre de d'ojet du rituel dans le sac du perso
    temp integer;                       -- vraible passe partout
    texte_evt text;                 -- texte pour évènements
    v_resultat  f_resultat;        -- resultat de f_carac_ameliore()
begin
    -- On rend la fonction atomique, pour éviter les problèmes de double clic.
    perform guard('f_modif_carac'); -- Ne pas oublier le perform release à chaque branche de sortie de fonction !

    -- on verifie que le service de rituel est bien configuré comme ouvert!
    if getparm_t(139) <> 'O' then
        code_retour := '<p>L''officine est fermée, le rituel n''est pas faisable actuellement!';
        perform release('f_modif_carac');
        return code_retour;
    end if;

    -- Un minimum de vérification (cout nécéssaire etc...) !!!
    -- d'abord on le perso
    select into v_perso_po
            perso_po
        from perso
        where perso_cod = personnage
        and perso_actif = 'O';
    if not found then
        code_retour := '<p>Erreur ! Perso non trouvé !';
        perform release('f_modif_carac');
        return code_retour;
    end if;

    -- Vérification Brouzoufs
    if v_perso_po < getparm_n(137) then
        code_retour := '<p>Vous n''avez pas assez de brouzouf !';
        perform release('f_modif_carac');
        return code_retour;
    end if;

    -- comptage des items dans le sac du perso nécéssaire au rituel
    select into v_perso_nb_obj count(*) perso_nb_obj from perso_objets join objets on obj_cod = perobj_obj_cod where perobj_perso_cod = personnage and obj_gobj_cod = getparm_n(135) ;
    if not found then
        code_retour := '<p>Vous n''avez d''objets pour le rituel !';
        perform release('f_modif_carac');
        return code_retour;
    end if;
    if v_perso_nb_obj < getparm_n(136) then
        code_retour := '<p>Vous n''avez assez d''objets pour le rituel !';
        perform release('f_modif_carac');
        return code_retour;
    end if;

    -- vérification du délai entre 2 rituels!
    select into temp prcarac_cod from perso_rituel_caracs where prcarac_perso_cod=personnage and prcarac_date_rituel > now()-((getparm_n(138)::text)||' DAYS')::interval ;
    if found then
        code_retour := '<p>Trop peu de temps vous sépare du dernier rituel pour en faire un nouveau !!';
        perform release('f_modif_carac');
        return code_retour;
    end if;

    --------------------------------------------------
    -- réalise la détérioration de caracs
    --------------------------------------------------
    v_resultat := f_carac_deteriore(personnage, demel);
    if v_resultat.etat != 0 then
      -- il y a eu un problème lors de la détérioration de caract
      perform release('f_modif_carac');
      return v_resultat.code_retour;
    end if;

    -- Tout c'est bien passé (pour la détérioration, on met le texte d'events!
    texte_evt := '[perso_cod1] a réalisé un rituel de transformation de caractéristiques.';
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
            values(nextval('seq_levt_cod'),11,now(),1,personnage,texte_evt,'O','N');

    -- texte invisible au joueur mais qui permettra eventuelelemnt un debugage
    texte_evt := 'Rituel de transformation, option détériorée : '||trim(to_char(demel,'999999'));
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
            values(nextval('seq_levt_cod'),-1,now(),1,personnage,texte_evt,'O','N');

    --------------------------------------------------
    -- réalise l'amélioration de caracs
    --------------------------------------------------
    v_resultat := f_carac_ameliore(personnage, amel);
    if v_resultat.etat != 0 then
      -- il y a eu un problème lors de l'amelioration de caract
      perform release('f_modif_carac');
      return v_resultat.code_retour;
    end if;

    -- texte invisible au joueur mais qui permettra eventuelelemnt un debugage
    texte_evt := 'Rituel de transformation, option améliorée : '||trim(to_char(amel,'999999'));
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
		insert into perso_rituel_caracs( prcarac_perso_cod, prcarac_amelioration_carac_cod, prcarac_diminution_carac_cod) values (personnage, amel, demel);

    code_retour := '<p>Vous avez réalisé un <b>rituel de transformation</b> de caractéristiques.<br>';

    perform release('f_modif_carac');
    return code_retour;
end;$_$;


ALTER FUNCTION public.f_rituel_modif_caracs(integer, integer, integer) OWNER TO delain;