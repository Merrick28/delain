
--
-- Name: pot_creusage_fort(integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or REPLACE  FUNCTION potions.pot_creusage_fort(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
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
  v_gobj_cod := 555;
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
    perform ajoute_bonus(personnage, 'CRE', 3, 45);
    code_retour := code_retour||'<br>Rien ne peut arrêter vos bras qui se saisissent de la pioche la plus proche et martèlent la roche';
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_creusage_fort(integer) OWNER TO delain;
