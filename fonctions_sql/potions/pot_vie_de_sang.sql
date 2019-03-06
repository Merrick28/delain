--
-- Name: pot_vie_de_sang(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_vie_de_sang(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_vie_de_sang                          */
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
  v_bonus_regen integer; --bonus de regen à intégrer
  v_pv integer; --pv du perso
  v_pv_max integer; --pv max du perso

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 550;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,cible,v_gobj_cod);
  if not found then
    code_retour := code_retour  ||  'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour,1,1) != '0' then
    return split_part(code_retour,';',2);
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    code_retour := split_part(code_retour,';',2);
    -- sinon, on a les effets normaux de la potion maintenant.
    -- le non décalage de dlt en cas de blessure
    perform ajoute_bonus(cible, 'PDL', 3, 1);
	if cible = personnage then
		code_retour := code_retour || 'Aucune douleur ne vous étreint malgré vos blessures, ';
	else
		code_retour := code_retour || 'Aucune douleur n''étreint votre cible malgré ses blessures, ';
	end if;
    -- la régénération accrue
    -- On calcule le bonus de régénération
    v_bonus_regen := lancer_des(4, 5);
    perform ajoute_bonus(cible, 'REG', 3, v_bonus_regen);
	if cible = personnage then
		code_retour := code_retour || 'vos blessures semblent se refermer plus rapidement. ';
	else
		code_retour := code_retour || 'ses blessures semblent se refermer plus rapidement. ';
	end if;
  end if;
  return code_retour;
end;
$_$;


ALTER FUNCTION potions.pot_vie_de_sang(integer,integer) OWNER TO delain;