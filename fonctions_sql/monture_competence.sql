
--
-- Name: monture_competence(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION monture_competence(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function monture_competence : fait un jet de compétence sous la compétence équitation, réalise le gain de PX, et le jet d'augmentation
   Cette compétence est utilisé pour: chevaucher, mettre pied à terre, désarçonner, donner un ordre à la monture.
   On passe en paramètres
     $1 = lanceur
     $2 = type action
          1 : chevaucher
          2 : pied_a terre
          3 : ordre de monture
          4 : désarçonner la cible
     $3 = cible (si action 4) ou null
     $4 = difficulté
   Le code sortie est une chaine séparée par ;
    1 = compétence réussi ?
        0 = non
        1 = oui
    2 = chaine html de sortie
*/
/*****************************************************************/
/* Créé le 16/04/2021                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	v_perso_cod alias for $1;		-- perso_cod du lanceur
	v_action alias for $2;		-- perso_cod du lanceur
	v_perso_cible alias for $3;		-- perso_cod de la cible si désarçonnage
	v_difficulte alias for $4;		-- difficulté de la compétence
	code_retour text;		-- chaine html de sortie

begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
	-- sur le lanceur

	-- sur la position du lanceur
	select into v_pos_protegee
		coalesce(lieu_refuge, 'N')
	from perso_position
	left outer join lieu_position ON lpos_pos_cod = ppos_pos_cod
	left outer join lieu ON lieu_cod = lpos_lieu_cod
	where ppos_perso_cod = lanceur;
	if v_pos_protegee = 'O' then
		code_retour := '0;<p>Erreur ! Vous êtes sur un lieu refuge et ne pouvez donc pas lancer de sorts.</p>';
		return code_retour;
	end if;

	-- sur la cible + zone de droit
	select into
		nom_cible, pos_cible, type_cible, v_pos_pvp, v_gmon_cod, v_pos_protegee
		perso_nom, ppos_pos_cod, perso_type_perso, pos_pvp, perso_gmon_cod, coalesce(lieu_refuge, 'N')
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod
	inner join positions on pos_cod = ppos_pos_cod
	left outer join lieu_position ON lpos_pos_cod = pos_cod
	left outer join lieu ON lieu_cod = lpos_lieu_cod
	where perso_cod = cible
		and perso_actif = 'O';	-- Bleda 27/2/11 On ne cible pas les morts !
	if not found then
		code_retour := code_retour||'0;<p>Erreur : cible non trouvée !</p>';
		return code_retour;
	end if;
	if type_attaquant != 2 and type_cible != 2 and v_pos_pvp = 'N' and offensif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort offensif car elle n’est pas une engeance de Malkiar !<br />La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)</p>';
		return code_retour;
	elsif type_attaquant != 2 and type_cible = 2 and v_pos_pvp = 'N' and offensif = 'N' then
		code_retour := '0;<p>Erreur ! Cette cible est en zone de droit, il vous est impossible de lui lancer un sort de soutien car elle est une engeance de Malkiar !<br />La zone de droit couvre tout l’Ouest de l’étage, et est séparée de la zone de non-droit, dans laquelle vous pouvez vous en prendre à n’importe quelle cible, par une frontière physique visible (Fils barbelés ou rivière)</p>';
		return code_retour;
	end if;
	if v_pos_protegee = 'O' and offensif = 'O' then
		code_retour := '0;<p>Erreur ! Cette cible est sur un lieu protégé ! Elle ne peut pas être la cible d’un sort offensif.</p>';
		return code_retour;
	end if;

	-- sur la compétence
	select into
		v_comp, nom_comp
		pcomp_modificateur, comp_libelle
	from perso_competences
	inner join competences on comp_cod = pcomp_pcomp_cod
	where pcomp_perso_cod = lanceur and pcomp_pcomp_cod = v_comp_cod;
	if not found and type_lancer != -1 then
		code_retour := code_retour||'0;<p>Erreur : infos compétence non trouvées !</p>';
		return code_retour;
	end if;

------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite
------------------------------------------------------------



      -- on regarde s il y a concentration
      select into compt concentration_perso_cod from concentrations  where concentration_perso_cod = v_perso_cod;
      if found then
        v_comp_modifie := v_comp_modifie + 20;
        delete from concentrations where concentration_perso_cod = v_perso_cod;
      end if;

      -- Mini à 1%
      if v_comp_modifie < 1 then
        v_comp_modifie := 1;
      end if;

      code_retour := code_retour||'Votre chance de réussir (en tenant compte des modificateurs) est de <b>'||trim(to_char(v_comp_modifie,'9999'))||'</b> ';

        -- on regarde si le sort est lancé
        v_special := floor(v_comp_modifie/5);

        -- etape  on regarde si la cible est bénie ou maudite
        bonmal := valeur_bonus(lanceur, 'BEN') + valeur_bonus(lanceur, 'MAU');
        if bonmal <> 0 then
          des := lancer_des3(1,100,bonmal);
        else
          des := lancer_des(1,100);
        end if;
        code_retour := code_retour||'et votre lancer de dés est de <b>'||trim(to_char(des,'9999'))||'</b>.<br>';

        if des > 96 then
          -- echec critique

          code_retour := '0;'||code_retour;
          return code_retour;
        end if;

        if des > v_comp_modifie then
          -- echec

          code_retour := code_retour||'Vous avez donc <b>échoué</b>.<br><br>';

          -- on regarde si on améliore la comp
          if v_comp <= getparm_n(1) then
            code_retour := code_retour||'Votre compétence est inférieure à '||trim(to_char(getparm_n(1),'9999'))||' %. Vous tentez une amélioration.<br>';
            temp_ameliore_competence := ameliore_competence_px(lanceur,v_comp_cod,v_comp);
            code_retour := code_retour||'Votre lancer de dés est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
            if split_part(temp_ameliore_competence,';',2) = '1' then
              code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b><br><br>.';
            else
              code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
            end if;
          end if;


          code_retour := '0;'||code_retour;
          return code_retour;
        end if;

        if des <= 5 then
          code_retour := code_retour||'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
          px_gagne := px_gagne + 1;
          cout_pa := floor(cout_pa/2);
        else
          if des <= v_special then
            code_retour := code_retour||'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
            cout_pa := cout_pa - 1;
          else
            code_retour := code_retour||'Vous avez donc <b>réussi</b>.<br><br>';
          end if;
        end if;

        facteur_reussite := v_comp_modifie - des;
        facteur_reussite_pur := v_comp_modifie - des;


        -- renomme magique
        update perso set perso_renommee_magie = perso_renommee_magie + temp_renommee where perso_cod = lanceur;

        -- px
          px_gagne := px_gagne + ((niveau_sort - 1)/3.0::numeric);


        -- on tente l amélioration
        temp_ameliore_competence := ameliore_competence_px(lanceur,v_comp_cod,v_comp);
        code_retour := code_retour||'Votre jet d’amélioration est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
        if split_part(temp_ameliore_competence,';',2) = '1' then
          code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b>.<br><br>';
        else
          code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
        end if;

        -- on attribue les PX
        update perso set perso_px = perso_px + px_gagne where perso_cod = lanceur;


  -- ---------------------------
	code_retour := code_retour||';'||trim(to_char(px_gagne,'999999990.99'))||';'||trim(to_char(facteur_reussite,'99999999999'));
	return code_retour;
end;$_$;


ALTER FUNCTION public.monture_competence(integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION monture_competence(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION monture_competence(integer, integer, integer, integer) IS 'Faire une jé de réussite sous la compétence Equitation';
