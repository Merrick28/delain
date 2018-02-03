
--
-- Name: pot_cachette(integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_cachette(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_cachette                                 */
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
  v_bonus_decouvre integer;	-- bonus pour creuser

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 541;
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
    perform ajoute_bonus(personnage, 'CAC', 3, 30);
    code_retour := code_retour||'<br>Un sentiment d''ultravision vous envahit ... Comment ça, vous n''avez jamais eu de sentiment d''ultravision ? Il faut bien une première fois, et celle là pourrait vous apporter des surprises';
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_cachette(integer) OWNER TO delain;
