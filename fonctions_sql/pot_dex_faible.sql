
--
-- Name: pot_dex_faible(integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or REPLACE FUNCTION potions.pot_dex_faible(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_force_faible	                         */
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
  duree integer;



begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 527;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,v_gobj_cod);
  if not found then
    code_retour := code_retour||'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour from 1 for 6) = 'Erreur' then
    return code_retour;
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    duree := lancer_des(1,10) + 20;
    perform f_modif_carac(personnage,'DEX',duree,1);
    code_retour := code_retour||'<br>Votre acuité semble s''être améliorée';
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_dex_faible(integer) OWNER TO delain;
