--
-- Name: execute_effet_auto_bmc(integer, text, numeric, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION execute_effet_auto_bmc(integer, text, numeric, numeric) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction execute_effet_auto_bmc                                */
/*   Exécute les fonctions spécifiques liées à un monstre    */
/*    et/ou un personnage                                    */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de la source              */
/*   $2 = type_bonus : le bonus qui a changé                 */
/*   $3 = bonus_vaant : le bonus avant changement            */
/*   $4 = bonus_apres : le bonus apres changement            */
/* on a en sortie les sorties concaténées des fonctions.     */
/*************************************************************/
/* Créé le 19/05/2014                                        */
/*************************************************************/
declare
  v_perso_cod    alias for $1;  -- Le code du perso en question
  v_bonus        alias for $2;  -- Le type de bonus
	v_valeur_avant alias for $3;  -- BM avant l'ajout
	v_valeur_apres alias for $4;  -- BM après l'ajout

	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	ligne_fonction record;     -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique
	v_gmon_nom text;           -- Le nom du monstre générique
	-- v_abattement text;         -- L'abatement à réaliser.

begin
  -- code de retour
	code_retour := '';
  -- v_abattement := '':

  -- eventuellement les fonction du monstre générique
	select into v_gmon_cod, v_gmon_nom perso_gmon_cod, gmon_nom from perso inner join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = v_perso_cod;
	if not found then
      v_gmon_cod:= null ;
      v_gmon_nom:= null ;
	end if;

  -- boucle sur toutes les fonctions specifiques de l'évenement pour le perso et le bonus qui vérifie le passage du seuil
	for ligne_fonction in (
      select fonc_cod, fonc_nom, trim(fonc_trigger_param->>'trig_raz'::text) as fonc_trig_abattement, fonc_trigger_param->>'fonc_trig_nom'::text as fonc_trig_nom
      from fonction_specifique
      where (fonc_gmon_cod = coalesce(v_gmon_cod, -1) OR (fonc_perso_cod = v_perso_cod))
            and (fonc_type='BMC')
            and (fonc_trigger_param->>'fonc_trig_compteur'::text = v_bonus)
            and (
                (fonc_trigger_param->>'fonc_trig_sens'::text = '1' and v_valeur_avant<(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric and v_valeur_apres>=(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric)
              or
                (fonc_trigger_param->>'fonc_trig_sens'::text = '-1' and v_valeur_avant>(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric and v_valeur_apres<=(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric)
                )
            and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
      order by (fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric, fonc_cod desc
		)
	loop
		code_fonction := ligne_fonction.fonc_nom;
		retour_fonction := execute_fonction_specifique(v_perso_cod, v_perso_cod, ligne_fonction.fonc_cod) ;

		-- changement de nom du perso (si monstre generique)
		if (coalesce(ligne_fonction.fonc_trig_nom, '') != '') and (v_gmon_nom is not null) then
		    update perso set perso_nom = replace(ligne_fonction.fonc_trig_nom,'[nom]',v_gmon_nom) ||' (n° '||trim(to_char(perso_cod,'99999999'))||')' where perso_cod=v_perso_cod;
		end if;

    -- if ligne_fonction.trig_raz != '' and ligne_fonction.fonc_trig_abattement != '0' then
    --     v_abattement := ligne_fonction.fonc_trig_abattement ;
    -- end if;

		if coalesce(retour_fonction, '') != '' then
			-- code_retour := code_retour || code_fonction || ' : ' || coalesce(retour_fonction, '') || '<br />';
			code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
		end if;
	end loop;

  -- -- traitement de l'abatement (s'il y en a un seulement pour les compteur)
  -- if  v_abattement != '' then
  --     -- seulement pour les bonus du type compteur (
  --     select tbonus_compteur from bonus_type where tbonus_libc = v_bonus and tbonus_compteur='O'
  --     if found then
  --
  --         if substr(v_abattement, char_length(v_abattement)-1, 1)= '%' then
  --             -- abattement par pourcentage
  --             v_abattement := substr(v_abattement, 1, char_length(v_abattement)-1) ;
  --             update
  --         else
  --
  --         end if;
  --     end if;
  -- end if;


	if code_retour != '' then
		code_retour := replace('<br /><b>Effets automatiques :</b><br />' || code_retour, '<br /><br />', '<br />') || '<br />';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.execute_effet_auto_bmc(integer, text, numeric, numeric) OWNER TO delain;

--
-- Name: FUNCTION execute_effet_auto_bmc(integer, text, numeric, numeric); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION execute_effet_auto_bmc(integer, text, numeric, numeric) IS 'Exécute les fonctions liées au changement de BM du perso_cod donné.';
