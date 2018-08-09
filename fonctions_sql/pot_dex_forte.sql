CREATE OR REPLACE FUNCTION potions.pot_dex_forte(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_force_forte                              */
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
  duree integer;	-- Duree de l'effet
  effet integer;	-- Force de l'effet



begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 529;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,v_gobj_cod);
  if not found then
    code_retour := code_retour||'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour from 1 for 6) = 'Erreur' then
    return code_retour;
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    duree := lancer_des(1,10) + 25;
    effet := lancer_des(2,4) + 2;
    perform f_modif_carac(personnage,'DEX',duree,effet);
    code_retour := code_retour||'<br>Bobin des Rois était votre frère, et Cunégonde la bretteuse votre soeur. Vous en avez rêvé pendant des années de pouvoir les égaler, et là, ce sont quelques instants de bonheur !!';
  end if;
  return code_retour;
end;	$function$

