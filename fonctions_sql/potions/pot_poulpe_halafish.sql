--
-- Name: pot_poulpe_halafish(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_poulpe_halafish(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_poulpe_halafish                          */
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
  v_intelligence integer;
  v_bonus_degats integer;
  v_diff integer;


begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 556;
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
    --modif de l'intelligence
    select into v_intelligence
      perso_int
    from perso
    where perso_cod = cible;
    v_bonus_existant := floor(v_intelligence/2) - v_intelligence;
    v_bonus_degats := floor(v_intelligence/2);
    perform f_modif_carac(cible,'INT',36,v_bonus_existant);
	if cible = personnage then
		code_retour := code_retour || 'vous vous sentez soudain plus ... débile, ';
	else
		code_retour := code_retour || 'votre cible se sent soudain plus ... débile, ';
	end if;
    -- dégâts augmentés
    perform ajoute_bonus(cible, 'PDC', 3, v_bonus_degats);
	if cible = personnage then
		code_retour := code_retour || ' mais en même temps plus pugnace, vous donnant un bonus en dégâts, ';
	else
		code_retour := code_retour || ' mais en même temps plus pugnace, lui donnant un bonus en dégâts, ';
	end if;
  end if;
  return code_retour;
end;
$_$;


ALTER FUNCTION potions.pot_poulpe_halafish(integer,integer) OWNER TO delain;
