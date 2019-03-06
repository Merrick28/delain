
--
-- Name: pot_dex_moyenne(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or REPLACE FUNCTION potions.pot_dex_moyenne(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_force_moyenne                            */
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
  duree integer;	-- Duree de l'effet
  effet integer;	-- Force de l'effet



begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 528;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,cible,v_gobj_cod);
  if not found then
    code_retour := code_retour || 'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour from 1 for 6) = 'Erreur' then
    return code_retour;
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    duree := lancer_des(1,10) + 23;
    effet := lancer_des(1,3) + 1;
    perform f_modif_carac(cible,'DEX',duree,effet);
	if cible = personnage then
		code_retour := code_retour || '<br>Vous aviez toujours rêvé de mettre dans le mille ! Profitez en avant que vos adversaires ne s''en aperçoivent ';
	else
		code_retour := code_retour || '<br>Votre cible a toujours toujours rêvé de mettre dans le mille ! Qu''elle en profite avant que ses adversaires ne s''en aperçoivent ';
	end if;
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_dex_moyenne(integer,integer) OWNER TO delain;
