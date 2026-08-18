--
-- Name: objet_utilisation(integer, integer, integer, integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION public.objet_utilisation(integer, integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function objet_utilisation                           */
/* parametres :                                         */
/*  $1 = v_perso_cod qui utilise l'objet                 */
/*  $2 = v_obj_cod						                    */
/*  $3 = v_objusebm_cod						                    */
/*  $4 = v_cible_cod                                          */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/*																														*/
/**************************************************************/
declare
  v_perso_cod alias for $1;	-- perso_cod
  v_obj_cod alias for $2;	-- obj_cod
  v_objusebm_cod alias for $3;	-- usage
  v_cible_cod alias for $4;	-- cible

  v_temp integer;				-- variable temporaire
  v_pa integer;					-- PA de l'utilisateur

  v_cout integer;
  v_bonus_distance integer;
  v_soi_meme char(1);
  v_bonus_monstre  char(1);
  v_bonus_joueur  char(1);
  v_bonus_familier  char(1);
  v_obj_nom text;	-- nom de l'objet
  v_nb_usage_max integer;			-- nombre d'utilisation maximum de l'objet
  v_nb_usage integer;				-- nombre d'utilisation de l'objet
  v_vide_detruit char(1);		    -- destruction de l'objet si vide
  v_perso_nom_cible text;		    -- nom de la cible
  v_malchance numeric;
  v_bonus_mode char(1);
  v_bonus_valeur integer;
  v_bonus_nb_tours integer;
  v_tbonus_libc char(4);
  v_des integer;			-- lancer de dés
  v_bonmal_actuel numeric ;   -- valeur du BM actuel

  v_compt_cod integer;	-- compte du lanceur
  v_coterie integer;	-- coterie du lanceur
  v_coterie_cible integer;	-- coterie de la cible
  v_pos_cod integer;	-- pos_cod du lanceur
  v_etage integer;	-- etage du lanceur
  v_pos_x integer;	-- posx du lanceur
  v_pos_y integer;	-- posy du lanceur
  v_distance_vue integer;	-- vue du lanceur
  v_traj integer;	-- trajectoire lanceur/cible
  v_dist integer;	-- distance lanceur/cible
  v_nom_cible text;	-- nom cible
  v_type_perso integer;	-- type de lanceur
  v_type_perso_cible integer;	-- type de cible
  v_triplette integer;	-- 1 si la cible est de la même triplette que le lanceur
  code_retour text;				-- code retour
  texte_evt text;			-- Texte pour événements

begin

    -- validation de l'existance de l'usage de l'objet (le creer si le code fourni est celui du générique), code d'erreur
    --  -1 : objet non trouvé
    --  -2 : objet non possédé (ou non identifié) par le personnage
    --  -3 : usage de sur l'objet et son générique non trouvé
    --  -4 : erreur lors de la création de l'usage spécifique
    --  -5 : erreur lors de la récupération du nombre d'utilisation
    --  -6 : nombre d'utilisation maximum atteint
    --  -7 : nombre d'utilisation maximum atteint et l'objet est détruit
    select objet_valide_usage(v_perso_cod,v_obj_cod,v_objusebm_cod) into v_temp ;
    if not found then
        return 'Erreur : Impossible de valider l''usage de l''objet !';
    elsif v_temp <= 0 then
        return 'Erreur ' || v_temp::text || ': L''usage de l''objet n''est pas valide !';
    end if;

    -- On récupère le code de l'usage de l'objet validé
    v_objusebm_cod := v_temp;


   -- recuperation des information sur l'objet est son l'usage
    select  obj_nom, objusebm_cout, objusebm_bonus_soi_meme, objusebm_bonus_monstre, objusebm_bonus_joueur, objusebm_bonus_familier, objusebm_bonus_distance,
            objusebm_nb_utilisation_max, objusebm_nb_utilisation, objusebm_vide_detruit, coalesce(objusebm_malchance,0), objusebm_bonus_mode,
            f_lit_des_roliste(COALESCE(objusebm_bonus_valeur,'0')), f_lit_des_roliste(COALESCE(objusebm_bonus_nb_tours,'0')), tbonus_libc
        into v_obj_nom, v_cout, v_soi_meme, v_bonus_monstre, v_bonus_joueur, v_bonus_familier, v_bonus_distance,
            v_nb_usage_max, v_nb_usage, v_vide_detruit, v_malchance, v_bonus_mode,
            v_bonus_valeur, v_bonus_nb_tours, v_tbonus_libc
        from objets_usage_bm
        join objets on objusebm_obj_cod = obj_cod
        join bonus_type on tbonus_cod = objusebm_tbonus_cod
        where objusebm_cod = v_objusebm_cod
        limit 1;
    if not found then
        return 'Erreur : Impossible de récupérer les informations sur l''usage de l''objet';
    end if;


  -- controle sur les PA de l'utilisateur (recupération compte et coetrie au passage)
    select into v_pa, v_type_perso, v_coterie, v_pos_cod, v_etage, v_pos_x, v_pos_y, v_distance_vue, v_nom_cible
        perso_pa, perso_type_perso, COALESCE(pgroupe_groupe_cod,0), pos_cod, pos_etage, pos_x, pos_y, distance_vue(perso_cod), perso_nom
    from perso
        join perso_position on ppos_perso_cod = perso_cod
        join positions on pos_cod = ppos_pos_cod
        left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1
    where perso_cod = v_perso_cod;
    if not found or v_pa < v_cout then
        return 'Erreur : Vous n''avez pas assez de PA pour effectuer cette action';
    end if;

    -- récupération des infos sur la cible
    select into v_coterie_cible, v_traj, v_dist, v_type_perso_cible, v_nom_cible, v_triplette, v_perso_nom_cible
        COALESCE(pgroupe_groupe_cod,0), trajectoire_vue(v_pos_cod, pos_cod), distance(v_pos_cod, pos_cod), perso_type_perso, perso_nom, case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END, perso_nom
        from perso
        inner join perso_position on ppos_perso_cod = perso_cod
        inner join positions on pos_cod = ppos_pos_cod
        left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1
        left join (
              select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=v_compt_cod and perso_actif='O'
              union
              select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=v_compt_cod and perso_actif='O'
          ) as triplette on triplette_perso_cod = perso_cod
        where perso_cod = v_cible_cod and perso_actif='O';
    if not found then
          return 'Erreur : la cible n''a pas été trouvée';
    end if;

    -- verification de la distance et de la trajectoire
    if (v_traj != 1) or (v_dist > v_distance_vue) or (v_dist > v_bonus_distance) then
        return 'Erreur : La cible est trop loin';
    end if;

    -- verification du ciblage (soi-meme, monstre, joueur, familier)
    if (v_perso_cod = v_cible_cod) and (v_soi_meme='N') then
        return 'Erreur : Vous ne pouvez pas vous cibler pour cette action';
    end if;

    if (v_type_perso_cible = 1) and (v_bonus_joueur='N') then
      return 'Erreur : Vous ne pouvez cibler des joueurs pour cette action';
    end if;

    -- verifcation du ciblage (soi-meme, monstre, joueur, familier)
    if (v_type_perso_cible = 2) and (v_bonus_monstre='N') then
      return 'Erreur : Vous ne pouvez cibler des monstres pour cette action';
    end if;

    if (v_type_perso_cible = 3) and (v_bonus_familier='N') then
      return 'Erreur : Vous ne pouvez cibler des familiers pour cette action';
    end if;

    if (v_type_perso_cible = 1) then
        if (v_bonus_joueur='C') and (v_coterie!=v_coterie_cible) then
            return 'Erreur : Vous ne pouvez cibler des joueurs qui ne sont pas dans votre coterie pour cette action';
        end if;

        if  (v_bonus_joueur='3') and (v_triplette!=1) then
            return 'Erreur : Vous ne pouvez cibler des joueurs qui ne sont pas dans votre triplette pour cette action';
        end if;
    end if;

    if (v_type_perso_cible = 3) then
        if (v_bonus_familier='C') and (v_coterie!=v_coterie_cible) then
            return 'Erreur : Vous ne pouvez cibler des familiers qui ne sont pas dans votre coterie pour cette action';
        end if;

        if  (v_bonus_familier='3') and (v_triplette!=1) then
            return 'Erreur : Vous ne pouvez cibler des familiers qui ne sont pas dans votre triplette pour cette action';
        end if;
    end if;


    -- mise à jour des PA du lanceur
    update perso set perso_pa = perso_pa - v_cout where perso_cod = v_perso_cod;

    -- mise à jour du nombre d'utilisation de l'objet
    update objets_usage_bm set objusebm_nb_utilisation = objusebm_nb_utilisation + 1 where objusebm_cod = v_objusebm_cod;

    -- ajout de l'événement dans la table ligne_evt
    texte_evt := '[attaquant] a utilise l''objet « ' || v_obj_nom || ' » sur [cible]'  ;
    code_retour := 'Vous avez utilisé l''objet « ' || v_obj_nom || ' » sur ' || v_perso_nom_cible || '.'  ;

    -- supression de l'objet si le nombre d'utilisation maximum est atteint et que l'objet est détruit
    if (v_nb_usage_max > 0) and (v_nb_usage + 1 >= v_nb_usage_max) and (v_vide_detruit = 'O') then
        perform f_del_objet(v_obj_cod);
       texte_evt :=  texte_evt || ', l''objet a été détruit.' ;
       code_retour :=  code_retour || ', l''objet a été détruit car il est vide.' ;
    end if;

    -- Il y a certains objets qui possède un facteur de malchance, faisant échoué son utilisation
    if v_malchance >0 then
        v_des := 100 * lancer_des(1,100);   -- facteur_malchance a une précision à 0.01 %
        if v_des <= 100 * v_malchance then

            texte_evt := '[attaquant] a tenté d''utiliser  l''objet « ' || v_obj_nom || ' » sur [cible] et a échoué.'  ;

            insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
                values(nextval('seq_levt_cod'),116,now(),1,v_perso_cod,texte_evt,'O','O',v_perso_cod,v_cible_cod);

            if v_perso_cod != v_cible_cod then
                insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
                    values(nextval('seq_levt_cod'),116,now(),1,v_cible_cod,texte_evt,'N','O',v_perso_cod,v_cible_cod);
            end if;

            return 'Oups, l''objet « ' || v_obj_nom || ' » n''a pas fonctionné correctement, il n''a eu aucun effet sur ' || v_perso_nom_cible || '.' ;
        end if;
    end if;

    -- on ne joue pas avec les bonus de caractéristiques, on ne peut pas les supprimer (on peut les ajouter avec des conditions)
    if v_bonus_nb_tours = 0  and v_tbonus_libc not in ('DEX', 'INT', 'FOR', 'CON')  then
        -- supression du BM au lieu de le donner
        if v_bonus_valeur = '0' then -- on supprime tous les malus mais aussi tous les bonus
            delete from bonus where bonus_perso_cod = v_cible_cod and  bonus_tbonus_libc = v_tbonus_libc and bonus_mode!='E';
        else
            select bonus_valeur into v_bonmal_actuel
                from bonus
                where sign(bonus_valeur) != sign(v_bonmal_valeur) and bonus_perso_cod = cible and  bonus_tbonus_libc = v_tbonus_libc and bonus_mode in ('S', 'A')
                order by abs(bonus_valeur) desc
                limit 1;
            if found then -- nota: si pas trouvé, il n'y a rien a supprimer
                if abs(v_bonmal_actuel) < abs(v_bonmal_valeur) then
                    -- on supprime plus qu'il n'en faut, on retire tout
                    delete from bonus where sign(bonus_valeur) != sign(v_bonmal_valeur) and bonus_perso_cod = cible and  bonus_tbonus_libc = v_tbonus_libc and bonus_mode in ('S', 'A') ;
                else
                    -- Il reste un peu de bonus/malus on diminue l'encours
                    update bonus set bonus_valeur= sign(bonus_valeur) * (abs(v_bonmal_actuel) - abs(v_bonmal_valeur)) where sign(bonus_valeur) != sign(v_bonmal_valeur) and bonus_perso_cod = cible and  bonus_tbonus_libc = v_tbonus_libc and bonus_mode='S' ;
                end if;
            end if;
        end if;

    elsif v_bonus_nb_tours!=0 and v_bonus_valeur!=0 then
        -- faire l'action d'ajouter le BM sur la cible
        perform ajoute_bonus(v_cible_cod, CASE WHEN v_bonus_mode = 'C' THEN '+'||v_tbonus_libc  ELSE v_tbonus_libc END, v_bonus_nb_tours, v_bonus_valeur);
    end if;

    if v_perso_cod != v_cible_cod then
        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
            values(nextval('seq_levt_cod'),116,now(),1,v_perso_cod,texte_evt,'O','O',v_perso_cod,v_cible_cod);

        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
            values(nextval('seq_levt_cod'),116,now(),1,v_cible_cod,texte_evt,'N','O',v_perso_cod,v_cible_cod);
    else
        insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
            values(nextval('seq_levt_cod'),116,now(),1,v_perso_cod,texte_evt,'O','O',v_perso_cod,v_cible_cod);
    end if;

    return code_retour;

end;	$_$;


ALTER FUNCTION public.objet_utilisation(integer, integer, integer, integer) OWNER TO delain;