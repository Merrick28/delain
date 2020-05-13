CREATE or replace FUNCTION public.nv_magie_fam_demon_traitre(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
AS
$_$/*****************************************************************/
/* function nv_magie_fam_demon_traitre : lance le sort                        */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
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
    pos_lanceur              integer; -- position du lanceur
    v_groupe                 integer; -- numéro de coterie du lanceur
    -------------------------------------------------------------
    -- variables concernant la cible
    -------------------------------------------------------------
    cible alias for $2; -- perso_cod de la cible
    nom_cible                text; -- nom de la cible
    v_fam                    integer; -- code familier
    v_des                    integer;
    v_sort_1                 integer;
    v_sort_2                 integer;
    v_ancien_proprio         integer; -- perso_cod de l’ancien propriétaire
    v_nom_ancien_proprio     text; -- nom de l’ancien propriétaire
    v_pos_ancien_proprio     integer; -- position de l’ancien propriétaire
    v_image_familier         integer; -- perso_cod de l’image
    v_vie_image              integer; -- durée de vie de l’image du familier
    -------------------------------------------------------------
    -- variables concernant le sort
    -------------------------------------------------------------
    num_sort                 integer; -- numéro du sort à lancer
    type_lancer alias for $3; -- type de lancer (memo ou rune)
    cout_pa                  integer; -- Cout en PA du sort
    px_gagne                 text; -- PX gagnes
    v_nom_familier           text; -- nom du familier
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
begin
    -------------------------------------------------------------
    -- Etape 1 : intialisation des variables
    -------------------------------------------------------------
    -- on renseigne d abord le numéro du sort
    num_sort := 149;
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

    -- on regarde s'il n'y a pas déjà un familier
    select into v_fam pfam_familier_cod
    from perso_familier,
         perso
    where pfam_perso_cod = cible
      and pfam_familier_cod = perso_cod
      and perso_actif = 'O';
    if found then
        code_retour :=
                    '<p>Malgré la réussite de votre invocation rien ne se passe. Il est impossible d''invoquer un familier alors que vous en avez déjà un.' ||
                    to_char(v_fam, '99999999999');
        return code_retour;
    end if;

    select into pos_lanceur ppos_pos_cod from perso_position where ppos_perso_cod = lanceur;
    -- id du demon: Parametre 111
    select into v_fam parm_valeur
    from parametres,
         perso
    where parm_cod = 111
      and parm_valeur = perso_cod;
    if not found then
        -- Il nous en faut un nouveau. Les joueurs ont cassé l'ancien
        v_fam := cree_monstre_pos(61, pos_lanceur);
        update perso set perso_con = 11, perso_temps_tour = 600 where perso_cod = v_fam;

        -- on ajoute trois sorts, drain de vie, poison, transfert de pouvoir
        insert into perso_sorts (psort_sort_cod, psort_perso_cod) values (34, v_fam);
        insert into perso_sorts (psort_sort_cod, psort_perso_cod) values (55, v_fam);
        insert into perso_sorts (psort_sort_cod, psort_perso_cod) values (140, v_fam);

        update parametres set parm_valeur = v_fam where parm_cod = 111;
        --Première partie : A, An, Ar, As, Ba, Be, Bi, Ca, Cro, Farn, Flap, Fur, Ga, Grod, Ha, Ko, Kro, Li, Lu, Mal, Mam, Mo, Nis, Nog, Nyb, Ou, Sa, Scox, Sha, Soh, U, Va, Ve

        --Partie(s) intermédiaire(s) : ag, al, an, ba, bal, bas, be, cell, ci, dall, do, dre, dro, el, en, fer, for, frons, fur, gi, gull, ha, han, ik, ka, la, lam, le, leust, li, ma, mi, mo, mon, na, nos, phar, phas, phir, phus, pu, rax, roch, sklurk, tan, thus, ti, us, ym, yt, zi, zob

        --Suffixe : al, as, bas, cell, dee, el, eth, ex, fer, for, frons, fur, gull, is, ka, la, lam, leth, leust, lith, mon, na, nos, phar, phas, phir, phus, rax, rith, roch, roth, sklurk, tan, thus, ti, us, ym
    end if;


    -------------------------------------------------------------
    -- Etape n : on crée une image du démon pour l’affecter à son ancien propriétaire.
    -------------------------------------------------------------
    -- Récupération ancien propriétaire
    select into v_ancien_proprio, v_nom_ancien_proprio, v_pos_ancien_proprio, v_vie_image pfam_perso_cod,
                                                                                          per.perso_nom,
                                                                                          ppos_pos_cod,
                                                                                          per.perso_int
    from perso_familier
             inner join perso fam ON fam.perso_cod = pfam_familier_cod
             inner join perso per ON per.perso_cod = pfam_perso_cod
             inner join perso_position ON ppos_perso_cod = per.perso_cod
    where pfam_familier_cod = v_fam
      AND fam.perso_actif = 'O';

    if found then
        -- Copie du familier
        v_image_familier := cree_ombre_familier(v_fam);

        -- On change le nom de l’image
        update perso
        set perso_actif           = 'O',
            perso_nom             = 'Image démoniaque, Familier de ' || v_nom_ancien_proprio,
            perso_lower_perso_nom = 'image démoniaque, familier de ' || lower(v_nom_ancien_proprio),
            perso_type_perso      = 3
        where perso_cod = v_image_familier;

        -- Affectation de l’ombre à l’ancien propriétaire
        insert into perso_position (ppos_pos_cod, ppos_perso_cod) values (v_pos_ancien_proprio, v_image_familier);
        insert into perso_familier (pfam_perso_cod, pfam_familier_cod, pfam_duree_vie)
        values (v_ancien_proprio, v_image_familier, v_vie_image);
    end if;

    -- On désaffecte le familier démon
    delete from perso_familier where pfam_familier_cod = v_fam;

    -------------------------------------------------------------
    -- Etape n + 1 : on gère l’affectation de Kirga.
    -------------------------------------------------------------
    -- on ranime le demon si mort
    -- on casse les blocages si combat
    -- on déplace le familier
    update perso_position set ppos_pos_cod = pos_lanceur where ppos_perso_cod = v_fam;

    -- on change le nom
    update perso
    set perso_actif           = 'O',
        perso_nom             = 'Kirga-Uh-Kmot, Familier de ' || nom_cible,
        perso_lower_perso_nom = 'kirga uh kmot familier de ' || lower(nom_cible),
        perso_type_perso      = 3,
        perso_kharma=0
    where perso_cod = v_fam;

    -- on le rattache au perso
    insert into perso_familier (pfam_perso_cod, pfam_familier_cod) values (lanceur, v_fam);

    -- Ajout du familier à la coterie de son maître
    DELETE FROM groupe_perso WHERE pgroupe_perso_cod = v_fam;
    select into v_groupe pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = lanceur and pgroupe_statut = 1;
    if found then
        insert into groupe_perso
        (pgroupe_groupe_cod, pgroupe_perso_cod, pgroupe_statut, pgroupe_messages, pgroupe_message_mort)
        values (v_groupe, v_fam, 1, 0, 0);
    end if;

    code_retour := code_retour || '<br>Vous gagnez ' || px_gagne || ' PX pour cette action.<br>';
    texte_evt := '[attaquant] a lancé ' || nom_sort;
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


ALTER FUNCTION public.nv_magie_fam_demon_traitre(integer, integer, integer) OWNER TO delain;
