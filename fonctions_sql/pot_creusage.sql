CREATE OR REPLACE FUNCTION potions.pot_creusage(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_creusage                                 */
/* parametres :                                          */
/*  $1 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/* 	       			                              */
/**************************************************************/
declare
  personnage alias for $1;	-- perso_cod
  code_retour text;				-- code retour
  v_gobj_cod integer;			-- code de l'objet générique
  v_bonus_creusage integer;	-- bonus pour creuser

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 540;
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
    -- on enlève les bonus existants
    perform ajoute_bonus(personnage, 'CRE', 3, 30);
    code_retour := code_retour||'<br>Vous sentez une certaine puissance dans vos bras, qui ne peuvent s''empêcher d''aller d''avant en arrière, frappant la roche la plus proche de vous';
  end if;
  return code_retour;
end;	$function$

