CREATE OR REPLACE FUNCTION potions.pot_vue_de_la_tour(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_vue_de_la_tour                           */
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
  v_bonus_regen integer; --bonus de regen à intégrer
  v_pv integer; --pv du perso
  v_pv_max integer; --pv max du perso

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 551;
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
    -- sinon, on a les effets normaux de la potion maintenant.
    -- le non décalage de dlt en cas de blessure
    perform ajoute_bonus(personnage, 'PVU', 3, 1);
    code_retour := code_retour||'Votre vue porte plus loin, bénéficiant d''une certaine acuité visuelle, ';
    -- la régénération accrue
    -- On calcule le bonus de régénération
    perform ajoute_bonus(personnage, 'PAR', 3, 1);
    code_retour := code_retour||'vous êtes plus résitant et bénéficiez d''un bonus de 1 en armure, ';
  end if;
  return code_retour;
end;
$function$

