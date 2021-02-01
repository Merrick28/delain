--
-- Name: pot_hortophilie_faible(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_hortophilie_faible(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_hortophilie_faible                       */
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
  personnage alias for $1; -- perso_cod
  cible alias for $2; -- perso_cod
  code_retour text;        -- code retour
  v_gobj_cod integer;      -- code de l’objet générique
  duree integer;           -- durée de l’effet
  force integer;           -- force de l’effet

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 894;
  code_retour := '';
  duree := 2;
  force := -2;

  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,cible, v_gobj_cod);
  if not found then
    code_retour := code_retour || 'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour, 1, 1) != '0' then
    return split_part(code_retour, ';', 2);

  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    code_retour := split_part(code_retour, ';', 2);

    perform ajoute_bonus(cible, 'HOR', duree, force);
	if cible = personnage then
		code_retour := code_retour || '<br>Aaaah, avec ça, vous voyez tout de suite mieux comment mélanger ces fichus ingrédients !';
	else
		code_retour := code_retour || '<br>Aaaah, avec ça, le buveur voit tout de suite mieux comment mélanger ces fichus ingrédients !';
	end if;
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_hortophilie_faible(integer,integer) OWNER TO delain;

--
-- Name: FUNCTION pot_hortophilie_faible(integer,integer); Type: COMMENT; Schema: potions; Owner: delain
--

COMMENT ON FUNCTION potions.pot_hortophilie_faible(integer,integer) IS 'Gère la potion de Kharrah’ch le méthodique';