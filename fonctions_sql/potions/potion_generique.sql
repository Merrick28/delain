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

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  code_retour := '0;'; --par défaut, tout va bien
  /*********************************************************/
  /*                  C O N T R O L E S                    */
  /*********************************************************/
  -- controle sur la possession de la potion
  select into v_obj_cod,v_nom_potion
    obj_cod,obj_nom
  from objets,perso_objets
  where perobj_perso_cod = personnage
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = potion
  limit 1;
  if not found then
    return '1;Erreur ! Vous ne possédez pas cette potion !';
  end if;

  -- Verification distance de la cible, potion ident, etc...

  -- controle sur les PA de l'utilisateur
  select into v_pa
    perso_pa
  from perso
  where perso_cod = personnage;
  if v_pa < getparm_n(104) then
    return '1;Erreur ! Vous n''avez pas assez de PA pour effectuer cette action';
  end if;
  if cible = personnage then
    texte_evt := '[perso_cod1] a bu une ' || CASE WHEN lower(substring(trim(v_nom_potion),1,6))='potion' THEN v_nom_potion else 'potion ' || v_nom_potion end;
  else
    texte_evt := '[attaquent] a fait boire une ' || CASE WHEN lower(substring(trim(v_nom_potion),1,6))='potion' THEN v_nom_potion else 'potion ' || v_nom_potion || ' a [cible]' end;
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
  code_retour := code_retour||'Vous buvez la potion '||v_nom_potion||'.<br>';
  if v_des_stabilite > v_stabilite then
    -- include ici le code pour les effets aléatoires
    v_texte_stabilite := potions.potion_instable(personnage);
    code_retour := code_retour||' Votre potion est instable, et se transforme.<br>'||v_texte_stabilite;
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

  insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'),81,now(),1,personnage,texte_evt,'O','O',cible,personnage);

  if cible != personnage then
    texte_evt := '[attaquant] vous a fait boire une ' || CASE WHEN lower(substring(trim(v_nom_potion),1,6))='potion' THEN v_nom_potion else 'potion ' || v_nom_potion end;
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'),81,now(),1,cible,texte_evt,'N','N',cible,personnage);
  end if;

  if lancer_des(1,100) < 20 then
    v_pa :=	cree_objet_perso(412,personnage);
    code_retour := code_retour||'<br>Vous réussissez à conserver le flacon vide, sans qu''il ne soit définitivement souillé.<br>Il pourra donc être réutilisé par un alchimiste qui pourra créer une nouvelle décoction.<br>';
  else
    code_retour := code_retour||'<br>Votre flacon est définitivement souillé. Des traces résiduelles sont incrustées.<br>Il ne pourra donc pas être réutilisé par un alchimiste.<br>';
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.potion_generique(integer, integer, integer) OWNER TO delain;