--
-- Name: pot_langueur_duurstaf(integer,integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or REPLACE FUNCTION potions.pot_langueur_duurstaf(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function pot_langueur_durrstaf                           */
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
  v_pv integer;
  v_pv_max integer;
  v_diff integer;

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 547;
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
    -- les pv
    select into v_pv,v_pv_max
      perso_pv,perso_pv_max
    from perso
    where perso_cod = cible;
    v_diff := lancer_des(5,5);
    v_pv := v_pv + v_diff;
    if v_pv > v_pv_max then
      v_pv := v_pv_max;
    end if;
    update perso set perso_pv = v_pv where perso_cod = cible;
	if cible = personnage then
		code_retour := code_retour || 'Vous gagnez '||trim(to_char(v_diff,'999999999'))||' points de vie, ';
	else
		code_retour := code_retour || 'Votre cible gagne '||trim(to_char(v_diff,'999999999'))||' points de vie, ';
	end if;
    -- cout d'une attaque
    perform ajoute_bonus(cible, 'PPA', 6, 1);
	if cible = personnage then
		code_retour := code_retour || 'vous avez un malus de +1 PA par attaque, ';
	else
		code_retour := code_retour || 'elle a un malus de +1 PA par attaque, ';
	end if;
    -- competences de combat
    perform ajoute_bonus(cible, 'PCC', 6, -10);
	if cible = personnage then
		code_retour := code_retour || 'vous avez un malus de 10% de chances de toucher au corps à corps.';
	else
		code_retour := code_retour || 'elle a un malus de 10% de chances de toucher au corps à corps.';
	end if;

  end if;
  return code_retour;
end;
$_$;


ALTER FUNCTION potions.pot_langueur_duurstaf(integer,integer) OWNER TO delain;
