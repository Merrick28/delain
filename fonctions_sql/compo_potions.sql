--
-- Name: compo_potions(integer, integer, integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE FUNCTION compo_potions(integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function compo_potions                                */
/* Ajout d’un ingrédient à une potion                    */
/* parametres :                                          */
/*  $1 = personnage qui compose la potion                */
/*  $2 = flacon concerné (pour gérer les compo en cours  */
/*  $3 = Composant ajouté                                */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/*********************************************************/
/* 25/01/2013 : Ajout d’événements de réussite / échec   */
/*********************************************************/
declare
  personnage alias for $1;  -- perso_cod
  objet alias for $2;       -- obj_cod
  composant alias for $3;   -- type de composant ajouté
  code_retour text;         -- code retour
  v_compo integer;          -- Composant qui va être consommé
  v_nom_compo text;      -- Composant qui va être consommé
  v_type_flacon integer;    --TYPE de fiole
  v_nombre integer;         --nombre de composants déjà présent dans la potion incomplète
  v_temp integer;           --fourre tout
  v_nouvelle integer;       --indique une nouvelle potion (afin de traiter des messages de type différent en retour)
  v_cree_monstre integer;   --Crée une gelée
  pos_personnage integer;   --position du personnage
  des integer;
  v_pv integer;

  v_gobj_cod integer;     -- code de l’objet générique
  v_obj_cod integer;      -- obj_cod de la potion
  v_nom_potion text;      -- nom de la potion
  v_pa integer;           -- PA de l’utilisateur
  v_stabilite integer;    -- stabilite de la potion
  v_des_stabilite integer;-- lancer de dés sur la stabilité
  v_texte_stabilite text; -- texte lié à l’instabilité de la potion
  v_bonus_existant integer;-- bonus existant ?
  gain_renommee numeric;  -- gain (ou perte) de renommée artisanale
  texte_evt text;

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  code_retour := '0;'; --par défaut, tout va bien
  gain_renommee := 0.1;
  /*********************************************************/
  /*                  C O N T R O L E S                    */
  /*********************************************************/
  -- controle sur la possession du flacon
  select into v_obj_cod, v_type_flacon
    obj_cod, obj_gobj_cod
  from objets, perso_objets
  where perobj_perso_cod = personnage
        and perobj_obj_cod = objet
        and obj_cod = perobj_obj_cod;
  if not found then
    return '1;Erreur ! Vous ne possédez pas cette potion !';
  end if;

  -- controle sur la possession d’un composant du type indiqué
  select into v_compo, v_nom_compo
    obj_cod, obj_nom
  from objets, perso_objets
  where perobj_perso_cod = personnage
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = composant
  limit 1;
  if not found then
    return '1;Erreur ! Vous ne possédez pas ce type de composant !';
  end if;

  -- controle sur le nombre de composants déjà présents dans le flacon, pour ne pas en avoir plus de 7
  if v_type_flacon = 561 then
    select into v_nombre sum(flaccomp_number) from potions.flacon_composants where flaccomp_obj_cod = objet;
    if v_nombre >= 21 then
      return '1;<br>Voyons, vous ne pouvez pas intégrer plus de composants dans cette potion ! Vous voyez bien que ce flacon est totalement rempli !';
    end if;
  end if;

  /*********************************************************/
  /*                  Gestion de la compo                  */
  /*********************************************************/
  --En fontion du type d’objet, on va s’occuper du flacon : si vide, on le remplace par une potion incomplète sinon rien
  if v_type_flacon = 412 then
    update objets
    set obj_gobj_cod = 561, obj_nom = 'Potion incomplète',
      obj_description = 'Cette potion ne semble pas totalement terminée, les ingrédients encore mal mélangés...'
    where obj_cod = objet;
    v_nouvelle := 1;
  end if;

  --on s’occupe du composant, en le supprimant de l’inventaire, et en l’ajoutant à la potion
  v_temp := f_del_objet (v_compo);
  code_retour := code_retour || '<br>Vous avez ajouté le composant dans le flacon';

  --on regarde si il est déjà présent dans le flacon
  select into v_nombre flaccomp_number from potions.flacon_composants
  where flaccomp_comp_cod = composant
        and flaccomp_obj_cod = objet;

  if found then
    v_nombre = v_nombre + 1; --C’est le premier composant, donc les effets seront limités
    update potions.flacon_composants set flaccomp_number = v_nombre
    where flaccomp_obj_cod = objet
          and flaccomp_comp_cod = composant;
  else
    insert into potions.flacon_composants (flaccomp_obj_cod, flaccomp_comp_cod, flaccomp_number) values (objet, composant, 1);
  end if;

  /*********************************************************/
  /*                  Gestion des effets / messages        */
  /*********************************************************/

  select into pos_personnage, v_pv
    ppos_pos_cod, perso_pv
  from perso_position, perso
  where ppos_perso_cod = personnage
        and ppos_perso_cod = perso_cod;

  des := lancer_des(1, 100);
  if des <= 1 and des < 5 and v_nombre > 1 then
    code_retour := code_retour || '<br>Une petite fumée blanche s’échappe du flacon';
  elsif des <= 5 and des < 10 and v_nombre > 1 then
    code_retour := code_retour || '<br>Une petite fumée bleue s’échappe du flacon';
  elsif des <= 10 and des < 15 and v_nombre > 1 then
    code_retour := code_retour || '<br>Une petite fumée rouge s’échappe du flacon';
  elsif des <= 15 and des < 20 and v_nombre > 1 then
    code_retour := code_retour || '<br>Une petite fumée jaune s’échappe du flacon';
  elsif des <= 20 and des < 25 and v_nombre > 1 then
    code_retour := code_retour || '<br>Vous sentez une odeur âcre';
  elsif des <= 25 and des < 30 and v_nombre > 1 then
    code_retour := code_retour || '<br>Vous sentez une odeur sucrée';
  elsif des <= 30 and des < 35 and v_nombre > 1 then
    code_retour := code_retour || '<br>Le mélange se met à bouillir doucement, puis revient à la normal';
  elsif des <= 35 and des < 40 and v_nombre > 1 then
    code_retour := code_retour || '<br>un sifflement se produit !';
  elsif des <= 40 and des < 45 and v_nombre > 1 then
    code_retour := code_retour || '<br>Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
    v_cree_monstre := cree_monstre_pos(31, pos_personnage);
  elsif des <= 45 and des < 50 and v_nombre > 1 then
    code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
    v_cree_monstre := cree_monstre_pos(32, pos_personnage);
  elsif des = 50 and v_nombre > 1 then
    code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
    v_cree_monstre := cree_monstre_pos(33, pos_personnage);
  elsif des = 51 and v_nombre > 1 then
    code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
    v_cree_monstre := cree_monstre_pos(34, pos_personnage);
  elsif des = 52 and v_nombre > 1 then
    code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
    v_cree_monstre := cree_monstre_pos(46, pos_personnage);
  elsif des = 53 and v_nombre > 1 then
    code_retour := code_retour || '<br>Une petite explosion se produit ! Heureusement qu’elle reste limitée à l’intérieur de la fiole !';
  elsif des <= 54 and des < 56 and v_nombre > 1 then
    code_retour := code_retour || '<br>Pendant quelques instants, rien ne se passe... Et tout d’un coup, le mélange explose vous provoquant quelques contusions mineures';
    v_pv := v_pv - 4;
    if v_pv <= 0 then
      v_temp = tue_perso_final(personnage);
    else
      update perso set perso_pv = v_pv where perso_cod = personnage;
    end if;
  elsif des <= 56 and des < 58 and v_nombre > 1 then
    code_retour := code_retour || '<br>Vous lâchez le flacon qui devenait brûlant ! Malheureusement, ce n’était pas que l’enveloppe qui l’était, et un peu de mélange se répand sur vous.';
    v_pv := v_pv - 6;
    if v_pv <= 0 then
      v_temp = tue_perso_final(personnage);
    else
      update perso set perso_pv = v_pv where perso_cod = personnage;
    end if;
  elsif des = 58 and v_nombre > 1 then
    code_retour := code_retour || '<br>Le liquide se solidifie, et commence à se mouvoir, puis à se structurer, prenant toute la place dans le flacon. Le verre ne résiste pas longtemps, et des bras et des jambes se forment ! Vous venez d’assister à la naissance d’un farfadet !!';
    v_cree_monstre := cree_monstre_pos(355, pos_personnage);
  elsif des = 59 and v_nombre > 1 then
    code_retour := code_retour || '<br>Le liquide se solidifie, et commence à se mouvoir, puis à se structurer, prenant toute la place dans le flacon. Le verre ne résiste pas longtemps, et des bras et des jambes se forment ! Vous venez d’assister à la naissance d’un farfadet !!';
    v_cree_monstre := cree_monstre_pos(223, pos_personnage);
  elsif des = 60 and v_nombre > 1 then
    code_retour := code_retour || '<br>Le liquide se solidifie, et commence à se mouvoir, puis à se structurer, prenant toute la place dans le flacon. Le verre ne résiste pas longtemps, et des bras et des jambes se forment ! Vous venez d’assister à la naissance d’un farfadet !!';
    v_cree_monstre := cree_monstre_pos(245, pos_personnage);
  else
    code_retour := code_retour;
  end if;
  code_retour := code_retour || '<br>Vous parvenez à préparer la potion ! Cela pourra prendre un peu de temps quand même, mais il vaut mieux être patient, les accidents sont si vite arrivés...';

  update perso
  set perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
  where perso_cod = personnage;

  texte_evt := '[perso_cod1] a rajouté un peu de ' || v_nom_compo || ' dans une potion.';
  perform insere_evenement(personnage, personnage, 91, texte_evt, 'O', NULL);

  return code_retour;
end;$_$;


ALTER FUNCTION potions.compo_potions(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION compo_potions(integer, integer, integer); Type: COMMENT; Schema: potions; Owner: delain
--

COMMENT ON FUNCTION compo_potions(integer, integer, integer) IS 'Ajout d’un ingrédient à une potion.';

