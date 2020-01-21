--
-- Name: pot_remede(integer,integer); Type: FUNCTION; Schema: potions; Owner: postgres
--

CREATE or replace FUNCTION potions.pot_remede(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_remede						                           */
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

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 1266;
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

    -- Supprimer les bonus/malus de poison
	delete from bonus where
		bonus_perso_cod = cible and
	  bonus_mode != 'E' and   -- sauf bonus d'équipement
		bonus_tbonus_libc = 'MDS';

	if cible = personnage then
		code_retour := code_retour || 'Vous avez supprimé tous vos bonus/malus liés à la maladie du sang';
	else
		code_retour := code_retour || 'Vous avez supprimé tous les bonus/malus du buveur liés à la maladie du sang';
	end if;
  end if;
  return code_retour;
end;
$_$;


ALTER FUNCTION potions.pot_remede(integer,integer) OWNER TO delain;