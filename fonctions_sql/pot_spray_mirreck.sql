CREATE OR REPLACE FUNCTION potions.pot_spray_mirreck(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function pot_vie_de_sang                          */
/* parametres :                                          */
/*  $1 = personnage qui boit la potion                   */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
declare
  personnage alias for $1;	-- perso_cod
  code_retour text;				-- code retour
  --
  v_gobj_cod integer;			-- code de l'objet générique
  v_obj_cod integer;			-- obj_cod de la potion
  v_nom_potion text;			-- nom de la potion
  v_pa integer;					-- PA de l'utilisateur
  v_stabilite integer;			-- stabilite de la potion
  v_des_stabilite integer;	-- lancer de dés sur la stabilité
  v_texte_stabilite text;		-- texte lié à l'instabilité de la potion
  titre text;

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  v_gobj_cod := 411;
  code_retour := '';
  /*Partie générique pour toutes les potions*/
  select into code_retour potions.potion_generique(personnage,v_gobj_cod);
  if not found then
    code_retour := code_retour||'Erreur ! Fonction générique non trouvée ';
  elsif substring(code_retour,1,1) != '0' then
    return split_part(code_retour,';',2);
  /*Tous les controles sont OK, on passe alors aux effets de la potion uniquement*/
  else
    code_retour := split_part(code_retour,';',2);
    -- sinon, on a les effets normaux de la potion maintenant.
    -- le non décalage de dlt en cas de blessure
    select into titre ptitre_titre from perso_titre
    where substring(ptitre_titre,1,4) = 'Pue '
          and ptitre_perso_cod = personnage;
    if not found then
      code_retour := code_retour||'<br>Vous sentez parfaitement bon ! Pourquoi avez vous utilisé un spray contre les mauvaises odeurs ?';
    else
      delete from perso_titre
      where substring(ptitre_titre,1,4) = 'Pue '
            and ptitre_perso_cod = personnage;
      code_retour := code_retour||'<br>Quel bonheur de retrouver une odeur à peu près correcte dans ces souterrains ! La prochaine est sans doute un bon bain, mais déjà, cette mauvaise odeur est loin maintenant. ';
    end if;
  end if;
  return code_retour;
end;$function$

