--
-- Name: potion_generique(integer, integer, integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.potion_generique(integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function potion_générique                             */
/* Gestion des effets génériques à toutes les potions    */
/* parametres :                                          */
/*  $1 = personnage qui boit la potion                   */
/*  $2 = potion qui est bue						                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/*																														*/
/**************************************************************/
declare
  personnage alias for $1;	-- perso_cod
  cible alias for $2;	-- perso_cod
  potion alias for $3;	-- gobj_cod
  code_retour text;				-- code retour
  texte_evt text;			-- Texte pour événements
  --
  v_gobj_cod integer;			-- code de l'objet générique
  v_obj_cod integer;			-- obj_cod de la potion
  v_nom_potion text;			-- nom de la potion
  v_pa integer;					-- PA de l'utilisateur
  v_stabilite integer;			-- stabilite de la potion
  v_des_stabilite integer;	-- lancer de dés sur la stabilité
  v_texte_stabilite text;		-- texte lié à l'instabilité de la potion
  v_bonus_existant integer;	-- bonus existant ?
  --
  v_type_cible text;	-- type de cible autorisée (param admins)
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

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  code_retour := '0;'; --par défaut, tout va bien
  /*********************************************************/
  /*                  C O N T R O L E S                    */
  /*********************************************************/
  -- controle sur la possession de la potion (2019-03-07: la potion doit-être identifiée)
  select into v_obj_cod,v_nom_potion
    obj_cod, CASE WHEN lower(substring(trim(obj_nom),1,6))='potion' THEN trim(substring(trim(obj_nom),7)) else obj_nom END
  from objets,perso_objets
  where perobj_perso_cod = personnage
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = potion
        and perobj_identifie='O'
  limit 1;
  if not found then
    return '1;Erreur ! Vous ne possédez pas cette potion !';
  end if;

  -- controle sur les PA de l'utilisateur
  select into v_pa, v_type_perso, v_coterie, v_pos_cod, v_etage, v_pos_x, v_pos_y, v_distance_vue, v_nom_cible
    perso_pa, perso_type_perso, COALESCE(pgroupe_groupe_cod,0), pos_cod, pos_etage, pos_x, pos_y, distance_vue(perso_cod), perso_nom
  from perso
  join perso_position on ppos_perso_cod = perso_cod
  join positions on pos_cod = ppos_pos_cod
  left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1
  where perso_cod = personnage;
  if not found or v_pa < getparm_n(104) then
    return '1;Erreur ! Vous n''avez pas assez de PA pour effectuer cette action';
  end if;

  -- Verification distance de la cible, type de cible, etc...
  if cible != personnage then
    v_type_cible := getparm_t(141);
    if v_type_cible = 'S' then
      return '1;Erreur ! Vous ne pouvez cibler que vous même pour cette action';
    end if;

    -- récupération du compte joueur (pour identification de la triplette)
    v_compt_cod := 0 ;    -- pas de triplette pour les monstres
    if v_type_perso = 3 then
      select v_compt_cod pcompt_compt_cod from perso_familier join perso_compte on pcompt_perso_cod = pfam_perso_cod where pfam_familier_cod=personnage ;
    elsif v_type_perso = 1 then
      select into v_compt_cod  pcompt_compt_cod from perso_compte where pcompt_perso_cod=personnage ;
    end if;

    -- récupération des infos sur la cible
    select into v_coterie_cible, v_traj, v_dist, v_type_perso_cible, v_nom_cible, v_triplette
      COALESCE(pgroupe_groupe_cod,0), trajectoire_vue(v_pos_cod, pos_cod), distance(v_pos_cod, pos_cod), perso_type_perso, perso_nom, case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END
    from perso
    inner join perso_position on ppos_perso_cod = perso_cod
    inner join positions on pos_cod = ppos_pos_cod
    left join groupe_perso on pgroupe_perso_cod = perso_cod and pgroupe_statut = 1
    left join (
          select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=v_compt_cod and perso_actif='O'
          union
          select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=v_compt_cod and perso_actif='O'
      ) as triplette on triplette_perso_cod = perso_cod
    where perso_cod = cible and perso_actif='O';
    if not found then
      return '1;Erreur ! la cible n''a pas été trouvée';
    end if;

    -- Controle de la distance
    if (v_dist > getparm_n(140)) or (v_traj != 1) then
      return '1;Erreur ! la cible est trop loin ou hors de vue pour cette action';
    end if;

    -- controle du type de cible
    if (v_type_cible = '3') and (v_triplette = 0) then
      return '1;Erreur ! Vous ne pouvez cibler que votre triplette pour cette action';
    end if;

    if (v_type_cible = 'G') and (v_triplette = 0) and ((v_coterie != v_coterie_cible) or (v_coterie = 0)) then
      return '1;Erreur ! Vous ne pouvez cibler que votre triplette ou votre coterie pour cette action';
    end if;

    if (v_type_cible = 'A') and (v_type_perso!=2) and (v_type_perso_cible != 1) and (v_type_perso_cible != 3) then
      return '1;Erreur ! Vous ne pouvez cibler que des personages ou des familiers pour cette action';
    end if;

    if (v_type_cible = 'A') and (v_type_perso=2) and (v_type_perso_cible != 2) then
      return '1;Erreur ! Vous ne pouvez cibler que des monstres pour cette action';
    end if;
  end if;

  if cible = personnage then
    texte_evt := '[perso_cod1] a bu une potion ' ||  v_nom_potion ;
  else
    texte_evt := '[attaquant] a fait boire à [cible] la potion ' || v_nom_potion;
  end if;

  -- controles sur la stabilité de la potion
  select into v_stabilite
    gobj_stabilite
  from objet_generique
  where gobj_cod = potion;
  v_des_stabilite := lancer_des(1,100);
  /*********************************************************/
  /*                  EFFETS ALEATOIRES                    */
  /*********************************************************/
  -- PA
  update perso
  set perso_pa = perso_pa - getparm_n(104)
  where perso_cod = personnage;
  -- on enlève la potion
  v_pa := f_del_objet(v_obj_cod);
  -- on commence à générer un code retour
  if cible != personnage then
    code_retour := code_retour||'Vous faite boire la potion '||v_nom_potion||' à ' || v_nom_cible || '.<br>';
  else
    code_retour := code_retour||'Vous buvez la potion '||v_nom_potion||'.<br>';
  end if;

  if v_des_stabilite > v_stabilite then
    -- include ici le code pour les effets aléatoires
    v_texte_stabilite := potions.potion_instable(cible);
    code_retour := code_retour||' Votre potion est instable, et se transforme.<br>';
    if cible != personnage then
      code_retour := code_retour||'<br>Les effets sur ' || v_nom_cible || ' sont les suivants:<br>'||v_texte_stabilite||'<br>';
    else
      code_retour := code_retour|||v_texte_stabilite||'<br>';
    end if;

    texte_evt := texte_evt || ' qui était instable';
  end if;

  /**********************************************************/
  /*                 GESTION DE LA TOXICITE                 */
  /**********************************************************/
  insert into potions.perso_toxic
  (ptox_perso_cod,ptox_potion,ptox_toxicite)
  values
    (cible,potion,100-v_stabilite);
  --
  texte_evt  := texte_evt || '.';

  if cible != personnage then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'),104,now(),1,personnage,texte_evt,'O','O',personnage,cible);

    texte_evt := '[attaquant] vous a fait boire une potion ' || v_nom_potion ;
    if v_des_stabilite > v_stabilite then
      texte_evt := texte_evt || ' qui était instable';
    end if;
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'),104,now(),1,cible,texte_evt,'N','O',personnage,cible);
  else
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'),81,now(),1,personnage,texte_evt,'O','O',personnage,cible);
  end if;

  if lancer_des(1,100) < 20 then
    v_pa :=	cree_objet_perso(412,personnage);
    code_retour := code_retour||'<br>Vous réussissez à conserver le flacon vide, sans qu''il ne soit définitivement souillé.<br>Il pourra donc être réutilisé par un alchimiste qui pourra créer une nouvelle décoction.<br><br>';
  else
    code_retour := code_retour||'<br>Votre flacon est définitivement souillé. Des traces résiduelles sont incrustées.<br>Il ne pourra donc pas être réutilisé par un alchimiste.<br><br>';
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.potion_generique(integer, integer, integer) OWNER TO delain;