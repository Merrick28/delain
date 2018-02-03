--
-- Name: pot_intelligence_moyenne(integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_intelligence_moyenne(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_intelligence_moyenne                     */
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
  v_gobj_cod := 522;
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
    duree := lancer_des(1,10) + 23;
    effet := lancer_des(1,3) + 1;
    perform f_modif_carac(personnage,'INT',duree,effet);
    code_retour := code_retour||'<br>Vous vous trouvez subitement plus intelligent, mais n''en abusez pas, n''est pas qui veut Einstein ...';
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_intelligence_moyenne(integer) OWNER TO delain;