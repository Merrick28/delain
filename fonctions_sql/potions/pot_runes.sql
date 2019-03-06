--
-- Name: pot_runes(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_runes(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_runes                                    */
/* augmente les chances de garder ses rune en cas de     */
/* ratage de sort                                        */
/* parametres :                                          */
/*  $1 = personnage qui utilise la potion                */
/*  $2 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/* 	       			                              */
/**************************************************************/
declare
  personnage alias for $1;	-- perso_cod
  cible alias for $2;	-- perso_cod
  code_retour text;				-- code retour
  v_gobj_cod integer;			-- code de l'objet générique
  v_bonus_runes integer;	-- bonus pour creuser

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 542;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,cible,v_gobj_cod);
  if not found then
    code_retour := code_retour || 'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour,1,1) != '0' then
    return split_part(code_retour,';',2);
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    code_retour := split_part(code_retour,';',2);
    -- on enlève les bonus existants
    perform ajoute_bonus(cible, 'PER', 3, 30);
	if cible = personnage then
		code_retour := code_retour || '<br>Dire que vous sentez mieux les arcanes magiques serait exagéré, mais en tout cas, c''est moins pire qu''avant !';
	else
		code_retour := code_retour || '<br>Dire que votre cible ressent mieux les arcanes magiques serait exagéré, mais en tout cas, c''est moins pire qu''avant !';
	end if;
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_runes(integer,integer) OWNER TO delain;