--
-- Name: pot_oeil_dalga(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or REPLACE FUNCTION potions.pot_oeil_dalga(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_oeil_dalga                               */
/* parametres :                                          */
/*  $1 = personnage qui utilise la potion                */
/*  $2 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/* modifs du 30/01/2007 (l'anniversaire de ma femme pour ceux */
/*  que ça intéresse )                                        */
/* - gestion des effets aléatoires                            */
/**************************************************************/
declare
  personnage alias for $1;	-- perso_cod
  cible alias for $2;	-- perso_cod
  code_retour text;				-- code retour
  --
  v_gobj_cod integer;			-- code de l'objet générique
  v_obj_cod integer;			-- obj_cod de la potion
  v_nom_potion text;			-- nom de la potion
  v_pa integer;					-- PA de l'utilisateur
  v_stabilite integer;			-- stabilite de la potion
  v_des_stabilite integer;	-- lancer de dés sur la stabilité
  v_texte_stabilite text;		-- texte lié à l'instabilité de la potion
  v_bonus_existant integer;	-- bonus existant ?

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 485;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,cible,v_gobj_cod);
  if not found then
    return 'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour,1,1) != '0' then
    return split_part(code_retour,';',2);
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    --code_retour := split_part(code_retour,';',2);
    code_retour := split_part(code_retour,';',2);
    -- on a les effets normaux de la potion maintenant.
    -- la vue
    perform ajoute_bonus(cible, 'PVU', 6, 1);
	if cible = personnage then
		code_retour := code_retour || 'Vous gagnez un bonus de 1 en vue, ';
	else
		code_retour := code_retour || 'Votre cible gagne un bonus de 1 en vue, ';
	end if;
    -- les chances de toucher
    perform ajoute_bonus(cible, 'PTD', 6, 35);
	if cible = personnage then
		code_retour := code_retour || 'et vous gagnez un bonus de 35% de chances de toucher à distance. ';
	else
		code_retour := code_retour || 'et elle gagne un bonus de 35% de chances de toucher à distance. ';
	end if;
  end if;
  return code_retour;
end;
$_$;


ALTER FUNCTION potions.pot_oeil_dalga(integer,integer) OWNER TO delain;
