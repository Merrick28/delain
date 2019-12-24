--
-- Name: f_carac_deteriore(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_carac_deteriore(integer, integer) RETURNS f_resultat
    LANGUAGE plpgsql
    AS $_$/********************************************************/
/* fonction f_carac_deteriore : effectue toutes les     */
/* actions liées à la diminution de carac. elle est     */
/* appelée  lors du rituel de modif de carac.           */
/* la fonction retourne une chaine exploitable en html  */
/* on passe en paramètres :                             */
/*   $1 = le perso_cod du passage                       */
/*   $2 = le type dé détérioration choisie              */
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
    personnage alias for $1;      -- perso_cod
    amel alias for $2;            -- type amélioration
    v_pa integer;                 -- nombre de PA du perso
    v_niveau_actu integer;        -- niveau actuel du perso
    v_limite_niveau integer;      -- PX nécessaires pour monter de niveau
    v_px numeric;                 -- PX du perso
    v_nom_perso text;             -- nom du perso
    v_constitution integer;       -- constit du perso
    pv_max_actuel integer;        -- PV max du perso
    temp integer;                 -- temp
    fait integer;                 -- amélioration faite ou non ?
    v_temps_actuel integer;       -- temps de tour du perso en minutes
    amel_temps integer;           -- amélioration de temps en minutes
    v_repar integer;              -- capacité de réparation
    v_modif_repar integer;        -- ce qu'on monte en répar
    v_enc_max integer;            -- encombrement du perso
    v_lvl_vamp integer;
    v_nb_amel_comp integer;
    v_race integer;               -- race du perso
    compt integer;
    v_force integer;
    v_deg_dex integer;
    v_regen integer;
    v_degats integer;
    v_armure integer;
    v_vue integer;
    v_nb_sort integer;
    v_nb_sort_memo integer;
    v_nb_sort_memo_max integer;
    v_nb_receptacle integer;
    v_int integer;
    v_con integer;
    v_dex integer;
    v_for integer;
    v_multi numeric;
    limite integer;                 -- Seuil limite qui permet ou pas l'amélioration
    v_resultat f_resultat;          -- resultat de cette fonction pour la fonction appelante
    v_diff integer;                 -- détérioration réelle, car s'il y a des bonus de caracs en changeant la base on change aussi les min/max
begin

-- Les fonctions de modifications de carac doivent-être rendu atomique par les guard/release.
-- Celle-ce est protégé lors de l'appel par f_passe_niveau ou f_rituel_modif_caracs

v_resultat.etat = 0 ; -- par défaut tout ce passe bien

-- etape 1 : on vérifie
    select into     v_pa,
                        v_niveau_actu,
                        v_limite_niveau,
                        v_px,
                        v_nom_perso,
                        pv_max_actuel,
                        v_repar,
                        v_nb_amel_comp,
                        v_race,
                        v_deg_dex,
                        v_regen,
                        v_degats,
                        v_armure,
                        v_vue,
                        v_nb_sort,
                        v_int,
                        v_con,
                        v_dex,
                        v_for,
                        v_enc_max
            perso_pa,
            perso_niveau,
            limite_niveau(perso_cod),
            perso_px,
            perso_nom,
            perso_pv_max,
            perso_capa_repar,
            perso_nb_amel_comp,
            perso_race_cod,
            perso_amel_deg_dex,
            perso_des_regen,
            perso_amelioration_degats,
            perso_amelioration_armure,
            perso_amelioration_vue,
            perso_amelioration_nb_sort,
            f_carac_base(perso_cod,'INT'),
            f_carac_base(perso_cod,'CON'),
            f_carac_base(perso_cod,'DEX'),
            f_carac_base(perso_cod,'FOR'),
            perso_enc_max
        from perso
        where perso_cod = personnage
        and perso_actif = 'O';
    if not found then
        v_resultat.code_retour := '<p>Erreur ! Perso non trouvé !';
        v_resultat.etat = -1 ;
        return v_resultat;
    end if;

--------------------------------------------------
-- Controle sur les seuils : on calcule la limite
--------------------------------------------------
    if v_niveau_actu <= 3 then
        limite := 2;
    else limite := floor(v_niveau_actu::numeric/2);
    end if;

--------------------------------------------------
-- tout semble correct, on passe aux améliorations
--------------------------------------------------
-- spécifiques
-- temps
    if amel = 1 then
        select into v_temps_actuel
            perso_temps_tour from perso
            where perso_cod = personnage;
        amel_temps := 30;
        if v_temps_actuel > 660 then
          v_resultat.code_retour := '<p>Vous avez déjà atteint le maximum de temps au tour !!';
          v_resultat.etat := -1 ;
          return v_resultat;
        end if;
        if ((v_temps_actuel >585) and (v_temps_actuel <= 660)) then
            amel_temps := 30;
        end if;
        if ((v_temps_actuel >525) and (v_temps_actuel <= 585)) then
            amel_temps := 25;
        end if;
        if ((v_temps_actuel >480) and (v_temps_actuel <= 525)) then
            amel_temps := 20;
        end if;
        if ((v_temps_actuel >450) and (v_temps_actuel <= 480)) then
            amel_temps := 15;
        end if;
        if v_temps_actuel <= 450 then
            amel_temps := 10;
        end if;
        update perso set perso_temps_tour = perso_temps_tour + amel_temps where perso_cod = personnage;
-- dégats à distance
    elsif amel = 2 then
        if v_deg_dex < 1 then
                v_resultat.code_retour := '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;
        update perso set perso_amel_deg_dex = perso_amel_deg_dex - 1 where perso_cod = personnage;
-- régénération
    elsif amel = 3 then
        if v_regen < 2 then
                v_resultat.code_retour := '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;
        update perso set perso_des_regen = perso_des_regen - 1 where perso_cod = personnage;
-- dégats corps à corps
    elsif amel = 4 then
        if v_degats < 1 then
                v_resultat.code_retour := '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;
        update perso set perso_amelioration_degats = perso_amelioration_degats - 1 where perso_cod = personnage;
-- armure
    elsif amel = 5 then
        if v_armure< 1 then
                v_resultat.code_retour :=  '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;
        update perso set perso_amelioration_armure = perso_amelioration_armure - 1 where perso_cod = personnage;
-- vue
    elsif amel = 6 then
        if v_vue < 1 then
                v_resultat.code_retour := '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;
        update perso set perso_amelioration_vue = perso_amelioration_vue - 1 where perso_cod = personnage;
        --perform update_automap(personnage);
-- réparation
    elsif amel = 7 then
        v_resultat.code_retour := '<p>Cette amélioration n''existe plus !';
        v_resultat.etat := -1 ;
        return v_resultat;
    elsif amel = 8 then
        select into v_nb_sort_memo count(*)from perso_sorts where psort_perso_cod = personnage;
        select into v_nb_sort_memo_max  nb_sort_memorisable(personnage);
        if v_race in (1,3) then
            temp := 4 ;
        else
            temp :=1 ;
        end if;
        if (v_nb_sort_memo_max - temp)<v_nb_sort_memo then
          v_resultat.code_retour := '<p>Vous avez déjà appris trop de sorts pour baisser cette caractéristique !';
          v_resultat.etat := -1 ;
          return v_resultat;
        end if;
        if (v_nb_sort-temp)<0 then
          v_resultat.code_retour := '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
          v_resultat.etat := -1 ;
          return v_resultat;
        end if;
        update perso set perso_amelioration_nb_sort = perso_amelioration_nb_sort - temp where perso_cod = personnage;
    elsif amel = 9 then
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into v_lvl_vamp, compt perso_niveau_vampire, perso_vampirisme from perso
            where perso_cod = personnage;
        if v_lvl_vamp = 0 then
            v_resultat.code_retour := 'Erreur ! Vous ne pouvez pas perdre cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        if compt < 0.05 then
            v_resultat.code_retour := '<p>Vous avez déjà atteint le minimum pour cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        update perso set perso_vampirisme = perso_vampirisme - 0.05 where perso_cod = personnage;
    elsif amel = 10 then    -- retour af1 vers af0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 62;   --af2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 61;   --af1
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'af1
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 61;
        -- on met l'af0 (avec les caracs de af1)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,25,round(compt));
    elsif amel = 11 then  -- retour af2 vers af1
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 62;   --af2
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'af2
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 62;
        -- on met l'af1 (avec les caracs de af2)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,61,round(compt));
    elsif amel = 12 then  --suppression f0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod in (64, 65);   --f1 ou f2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 63;   --f0
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'f0
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 63;
    elsif amel = 13 then     -- retour f1 vers f0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 65;   --f2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 64;   --f1
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'f1
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 64;
        -- on met l'f0 (avec les caracs de af1)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,63,round(compt));
    elsif amel = 14 then  -- retour f2 vers f1
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 65;   --f2
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'f2
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 65;
        -- on met l'af1 (avec les caracs de af2)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,64,round(compt));
    elsif amel = 15 then    -- supression cg0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod in (67, 68);   --cg1 ou cg2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 66;   --cg0
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'cg0
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 66;
    elsif amel = 16 then    -- retour acg1 vers cg0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 68;   --cg2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 67;   --cg1
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'cg1
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 67;
        -- on met l'cg0 (avec les caracs de cg1)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,66,round(compt));
    elsif amel = 17 then  -- retour cg2 vers cg1
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 68;   --cg2
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'cg2
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 68;
        -- on met l'cg1 (avec les caracs de cg2)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,67,round(compt));
    elsif amel = 18 then    -- supression af0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        if (v_race = 2 ) then
            v_resultat.code_retour := 'Erreur ! Les nains ne peuvent pas perdre la compétence attaque foudroyante obtenue par leur naissance!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod in (61, 62);   --af1 ou af2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 25;   --af0
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'af0
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 25;
    elsif amel = 19 then
        if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into v_nb_receptacle perso_nb_receptacle from perso where perso_cod = personnage and perso_nb_receptacle >0 ;
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas de réceptacle !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on met le recept
        update perso set perso_nb_receptacle = perso_nb_receptacle - 1 where perso_cod = personnage;
        -- si on a trop de récéptacle rempli, on en supprime 1 au hazard
        select into compt count(*) from recsort where recsort_perso_cod = personnage;
        if compt>=v_nb_receptacle then
          delete from recsort where recsort_cod in ( select recsort_cod from  recsort where recsort_perso_cod = personnage order by random() limit 1) ;
        end if;
    elsif amel = 20 then
        v_resultat.code_retour := '<p>Cette amélioration n''existe plus !';
        v_resultat.etat := -1 ;
        return v_resultat;
    elsif amel = 27 then
        if (v_for <=6) or (v_enc_max < 3) then
                v_resultat.code_retour := 'Erreur ! Le minimum de caractéristique a été atteint!';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;

        select into temp, v_diff
            corig_carac_valeur_orig, perso_for - f_modif_carac_limit('FOR', corig_carac_valeur_orig-1, perso_for-1+valeur_bonus(corig_perso_cod, 'FOR')::integer)
            from carac_orig join perso on perso_cod=corig_perso_cod
            where corig_perso_cod = personnage
            and corig_type_carac = 'FOR' limit 1;
        if found then
            update carac_orig
                set corig_carac_valeur_orig = corig_carac_valeur_orig - 1
                where corig_perso_cod = personnage
                and corig_type_carac = 'FOR';
        else
          v_diff := 1 ;
        end if;

        update perso set perso_for = perso_for - v_diff, perso_enc_max = perso_enc_max - (v_diff * 3) where perso_cod = personnage;

    elsif amel = 21 then    -- supression bp0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod in (73, 74);   --bp1 ou bp2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 72;   --bp0
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'bp0
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 72;
    elsif amel = 22 then    -- retour bp1 vers bp0
       if (v_nb_amel_comp <=0 ) then
          v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
          v_resultat.etat := -1 ;
          return v_resultat;
       end if;
       select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 74;   --bp2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 73;   --bp1
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'af1
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 73;
        -- on met l'bp0 (avec les caracs de bp1)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,72,round(compt));
    elsif amel = 23 then  -- retour bp2 vers bp1
        if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 74;   --bp2
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'bp2
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 74;
        -- on met l'bp1 (avec les caracs de bp2)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,73,round(compt));
    elsif amel = 24 then    -- supression tp0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod in (76, 77);   --tp1 ou tp2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 75;   --tp0
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'tp0
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 75;
    elsif amel = 25 then    -- retour tp1 vers tp0
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 77;   --tp2
        if found then
            v_resultat.code_retour := 'Erreur ! Vous pouvez pas perdre cette amélioraton si vous possèdez le niveau suivant !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 76;   --tp1
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'af1
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 76;
        -- on met l'tp0 (avec les caracs de tp1)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,75,round(compt));
    elsif amel = 26 then  -- retour tp2 vers tp1
         if (v_nb_amel_comp <=0 ) then
            v_resultat.code_retour := 'Erreur ! Le minimum d''amélioration a été atteint!';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        select into compt pcomp_modificateur
            from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 77;   --tp2
        if not found then
            v_resultat.code_retour := 'Erreur ! Vous ne possèdez pas cette amélioration !';
            v_resultat.etat := -1 ;
            return v_resultat;
        end if;
        -- on met le nb d'amélioration
        update perso set perso_nb_amel_comp = perso_nb_amel_comp - 1
            where perso_cod = personnage;
        -- on supprime l'tp2
        delete from perso_competences
            where pcomp_perso_cod = personnage
            and pcomp_pcomp_cod = 77;
        -- on met l'tp1 (avec les caracs de tp2)
        insert into perso_competences
            (pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
            values
            (personnage,76,round(compt));
    elsif amel = 28 then
        if (v_dex <= 6) or (v_repar < 3) then
                v_resultat.code_retour := 'Erreur ! Le minimum de caractéristique a été atteint!';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;

        select into temp, v_diff
            corig_carac_valeur_orig, perso_dex - f_modif_carac_limit('DEX', corig_carac_valeur_orig-1, perso_dex-1+valeur_bonus(corig_perso_cod, 'DEX')::integer)
            from carac_orig join perso on perso_cod=corig_perso_cod
            where corig_perso_cod = personnage
            and corig_type_carac = 'DEX' limit 1;
        if found then
            update carac_orig
                set corig_carac_valeur_orig = corig_carac_valeur_orig - 1
                where corig_perso_cod = personnage
                and corig_type_carac = 'DEX';
        else
          v_diff := 1;
        end if;

        update perso set perso_dex = perso_dex - v_diff, perso_capa_repar = perso_capa_repar - (v_diff * 3) where perso_cod = personnage;

    elsif amel = 29 then
        if (v_con <=6) or (pv_max_actuel <= 3) then
                v_resultat.code_retour := 'Erreur ! Le minimum de caractéristique a été atteint!';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;

        select into temp, v_diff
            corig_carac_valeur_orig, perso_con - f_modif_carac_limit('CON', corig_carac_valeur_orig-1, perso_con-1+valeur_bonus(corig_perso_cod, 'CON')::integer)
            from carac_orig join perso on perso_cod=corig_perso_cod
            where corig_perso_cod = personnage
            and corig_type_carac = 'CON' limit 1;
        if found then
            update carac_orig
                set corig_carac_valeur_orig = corig_carac_valeur_orig - 1
                where corig_perso_cod = personnage
                and corig_type_carac = 'CON';
        else
          v_diff = 1 ;
        end if;

        update perso set perso_con = perso_con - v_diff, perso_pv_max = perso_pv_max - (v_diff*3), perso_pv = GREATEST(1, perso_pv - (v_diff * 3)) where perso_cod = personnage;

    elsif amel = 30 then
        if (v_int <= 6) or (v_repar < 3) then
                v_resultat.code_retour := 'Erreur ! Le minimum de caractéristique a été atteint!';
                v_resultat.etat := -1 ;
                return v_resultat;
        end if;

        select into temp, v_diff
            corig_carac_valeur_orig, perso_int - f_modif_carac_limit('INT', corig_carac_valeur_orig-1, perso_int-1+valeur_bonus(corig_perso_cod, 'INT')::integer)
            from carac_orig join perso on perso_cod=corig_perso_cod
            where corig_perso_cod = personnage
            and corig_type_carac = 'INT' limit 1;
        if found then
            update carac_orig
                set corig_carac_valeur_orig = corig_carac_valeur_orig - 1
                where corig_perso_cod = personnage
                and corig_type_carac = 'INT';
        else
          v_diff = 1 ;
        end if;

        update perso set perso_int = perso_int - v_diff, perso_capa_repar = perso_capa_repar - (v_diff * 3) where perso_cod = personnage;

    end if;

    return v_resultat;
end;$_$;


ALTER FUNCTION public.f_carac_deteriore(integer, integer) OWNER TO delain;