
--
-- Name: pot_bloque_magie_forte(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_bloque_magie_forte(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_bloque_magie_forte                       */
/* parametres :                                          */
/*  $1 = personnage qui utilise la potion                */
/*  $2 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
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
  v_bonus integer;	-- bonus final calculé

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 693;
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
    -- on a les effets normaux de la potion maintenant.
    -- le bloque magie
    perform ajoute_bonus(cible, 'BLM', 3, 40);
	if cible = personnage then
		code_retour := code_retour || 'Vous vous sentez un peu plus ... nain ... ';
	else
		code_retour := code_retour || 'Votre cible se sent un peu plus ... nain ... ';
	end if;
    -- les chances de toucher
    perform ajoute_bonus(cible, 'TOU', 3, -30);
	if cible = personnage then
		code_retour := code_retour || '<br>Le nain est petit, il manque donc souvent sa cible. Vous gagnez un malus de 30% de chances de toucher au combat.';
	else
		code_retour := code_retour || '<br>Le nain est petit, il manque donc souvent sa cible. Elle gagne un malus de 30% de chances de toucher au combat.';
	end if;
    -- l'esquive
    perform ajoute_bonus(cible, 'ESQ', 3, -20);
	if cible = personnage then
		code_retour := code_retour || '<br>Le nain est lourd, il a du mal à éviter les coups. Vous gagnez un malus de 20% de chances d''esquiver les coups.';
	else
		code_retour := code_retour || '<br>Le nain est lourd, il a du mal à éviter les coups. Elle gagne un malus de 20% de chances d''esquiver les coups.';
	end if;
  end if;
  return code_retour;
end;	$_$;


ALTER FUNCTION potions.pot_bloque_magie_forte(integer,integer) OWNER TO delain;