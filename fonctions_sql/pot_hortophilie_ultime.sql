CREATE OR REPLACE FUNCTION potions.pot_hortophilie_ultime(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_hortophilie_ultime                       */
/* parametres :                                          */
/*  $1 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/* 	       			                              */
/**************************************************************/
declare
  personnage alias for $1; -- perso_cod
  code_retour text;        -- code retour
  v_gobj_cod integer;      -- code de l’objet générique
  duree integer;           -- durée de l’effet
  force integer;           -- force de l’effet

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 897;
  code_retour := '';
  duree := 6;
  force := -3;

  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage, v_gobj_cod);
  if not found then
    code_retour := code_retour || 'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour, 1, 1) != '0' then
    return split_part(code_retour, ';', 2);

  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    code_retour := split_part(code_retour, ';', 2);

    perform ajoute_bonus(personnage, 'HOR', duree, force, 1);
    code_retour := code_retour||'<br>Aaaah, avec ça, vous voyez tout de suite mieux comment mélanger ces fichus ingrédients !';
  end if;
  return code_retour;
end;$function$

