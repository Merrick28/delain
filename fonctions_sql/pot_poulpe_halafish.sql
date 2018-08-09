CREATE OR REPLACE FUNCTION potions.pot_poulpe_halafish(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_poulpe_halafish                          */
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
  v_intelligence integer;
  v_bonus_degats integer;
  v_diff integer;


begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 556;
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
    --modif de l'intelligence
    select into v_intelligence
      perso_int
    from perso
    where perso_cod = personnage;
    v_bonus_existant := floor(v_intelligence/2) - v_intelligence;
    v_bonus_degats := floor(v_intelligence/2);
    perform f_modif_carac(personnage,'INT',36,v_bonus_existant);
    code_retour := code_retour||'vous vous sentez soudain plus ... débile, ';
    -- dégâts augmentés
    perform ajoute_bonus(personnage, 'PDC', 3, v_bonus_degats);
    code_retour := code_retour||' mais en même temps plus pugnace, vous donnant un bonus en dégâts, ';
  end if;
  return code_retour;
end;
$function$

