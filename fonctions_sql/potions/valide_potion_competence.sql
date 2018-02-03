--
-- Name: valide_potion_competence(integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.valide_potion_competence(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/*********************************************************/
/* function valide_potion_competence                     */
/* Valide la compétence lors de la finalisation d’une    */
/*potion                                                 */
/* parametres :                                          */
/*  $1 = personnage qui compose la potion                */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/*********************************************************/
/*														 */
/*********************************************************/
declare
  personnage alias for $1;	-- perso_cod
  code_retour text;			-- code retour
  pluriel text;
  ligne record;
  lignesup record;
  des integer;
  compt integer;				-- valeur de la concentration
  alchimie integer;			-- valeur de la compétence en alchimie du perso
  alchimie_modif integer;		-- valeur de la compétence en alchimie du perso après modifications
  comp_alchimie integer;		-- Compétence en alchimie du perso (Niv1, 2 ou 3)
  resultat text;				--texte d'amélioration de la comp

begin
  code_retour := '';

  -- Jet de compétence et amélioration
  select into alchimie,comp_alchimie pcomp_modificateur,pcomp_pcomp_cod
  from perso_competences where pcomp_perso_cod = personnage and pcomp_pcomp_cod in (97,100,101);

  -- on regarde s il y a concentration
  select into compt concentration_perso_cod from concentrations
  where concentration_perso_cod = personnage;
  if found then
    alchimie_modif := alchimie + 20;
    delete from concentrations where concentration_perso_cod = personnage;
  else
    alchimie_modif := alchimie;
  end if;

  des := lancer_des(1,100);
  code_retour := code_retour||'Votre chance de réussir (en tenant compte des modificateurs) est de <b>'||trim(to_char(alchimie_modif,'9999'))||'</b> ';
  code_retour := code_retour||'et votre lancer de dés est de <b>'||trim(to_char(des,'9999'))||'</b>.<br>';

  if des > 96 then
    -- echec critique
    code_retour := '0;'||code_retour||'Il s''agit donc d''un échec automatique.<br><br>';
    return code_retour;
  end if;
  if des > alchimie_modif then
    code_retour := '0;'||code_retour||'Vous avez donc <b>échoué</b>.<br><br>';

    if alchimie <= getparm_n(1) then -- amélioration auto then
      code_retour := code_retour||'Votre compétence est inférieure à '||trim(to_char(getparm_n(1),'9999'))||' %. Vous tentez une amélioration.<br>';
      resultat := ameliore_competence_px(personnage, comp_alchimie, alchimie);
      code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(resultat,';',1)||', '; -- pos 7 8 9 10
      if split_part(resultat,';',2) = '1' then
        code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
        code_retour := code_retour||'Sa nouvelle valeur est '||split_part(resultat,';',3)||' ; vous gagnez 1 PX.<br><br>';
      else
        code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
      end if;
    end if;
    return code_retour;
  end if;

  -- Compétence réussie
  code_retour := '1;'||code_retour||'Vous avez donc <b>réussi</b>.<br><br>';
  resultat := ameliore_competence_px(personnage, comp_alchimie, alchimie);
  code_retour := code_retour||'Votre jet d''amélioration est de '||split_part(resultat,';',1)||', '; -- pos 7 8 9 10
  if split_part(resultat,';',2) = '1' then
    code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
    code_retour := code_retour||'Sa nouvelle valeur est '||split_part(resultat,';',3)||' ; vous gagnez 1 PX.<br><br>';
  else
    code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
  end if;
  --  fin d'amélioration des compétences

  return code_retour;
end;	$_$;


ALTER FUNCTION potions.valide_potion_competence(integer) OWNER TO delain;

--
-- Name: FUNCTION valide_potion_competence(integer); Type: COMMENT; Schema: potions; Owner: delain
--

COMMENT ON FUNCTION potions.valide_potion_competence(integer) IS 'Gère la réussite (ou l’échec) de la compétence (pour ces *** de parties d’alchimie codées en PHP)';
