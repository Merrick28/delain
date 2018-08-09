CREATE OR REPLACE FUNCTION potions.pot_langueur_duurstaf(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_langueur_durrstaf                           */
/* parametres :                                          */
/*  $1 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
declare
  personnage alias for $1;	-- perso_cod
  code_retour text;				-- code retour
  --
  v_gobj_cod integer;			-- code de l'objet générique
  v_obj_cod integer;			-- obj_cod de la potion
  v_nom_potion text;			-- nom de la potion
  v_pa integer;					-- PA de l'utilisateur
  v_stabilite integer;			-- stabilite de la potion
  v_des_stabilite integer;	-- lancer de dés sur la stabilité
  v_texte_stabilite text;		-- texte lié à l'instabilité de la potion
  v_bonus_existant integer;	-- bonus existant ?
  v_pv integer;
  v_pv_max integer;
  v_diff integer;

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 547;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,v_gobj_cod);
  if not found then
    code_retour := code_retour||'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour,1,1) != '0' then
    return split_part(code_retour,';',2);
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    code_retour := split_part(code_retour,';',2);
    -- on a les effets normaux de la potion maintenant.
    -- les pv
    select into v_pv,v_pv_max
      perso_pv,perso_pv_max
    from perso
    where perso_cod = personnage;
    v_diff := lancer_des(5,5);
    v_pv := v_pv + v_diff;
    if v_pv > v_pv_max then
      v_pv := v_pv_max;
    end if;
    update perso set perso_pv = v_pv where perso_cod = personnage;
    code_retour := code_retour||'Vous gagnez '||trim(to_char(v_diff,'999999999'))||' points de vie, ';
    -- cout d'une attaque
    perform ajoute_bonus(personnage, 'PPA', 6, 1);
    code_retour := code_retour||'vous avez un malus de +1 PA par attaque, ';
    -- competences de combat
    perform ajoute_bonus(personnage, 'PCC', 6, -10);
    code_retour := code_retour||'vous avez un malus de 10% de chances de toucher au corps à corps.';

  end if;
  return code_retour;
end;
$function$

