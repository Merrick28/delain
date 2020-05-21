--
-- Name: execute_fonctions_ext(integer, integer, character, json); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION execute_fonctions_ext(integer, integer, character, json) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction execute_fonctions_ext                            */
/*   comme  execute_fonctions mais avec injection de params. */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de la source              */
/*   $2 = cible_cod : si nécessaire, le numéro de la cible   */
/*   $3 = événement : D pour début de tour, M pour mort,     */
/*                    T pour Tueur, A pour Attaque, etc...   */
/*   $4 = params : divers paramètre (en fonction des besoins)*/
/* on a en sortie les sorties concaténées des fonctions.     */
/*************************************************************/
declare
	v_perso_cod alias for $1;  -- Le code de la source
	v_cible_cod alias for $2;  -- Le numéro de la cible
	v_evenement alias for $3;  -- L’événement déclencheur
	v_param alias for $4;      -- Les données à injecter pour l'éffet de l'EA

	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	ligne_fonction record;     -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique

begin

  -- préparation des paramètres commun
  -- if v_cible_cod is null and v_evenement != 'D' then
	-- 	v_cible_cod := v_perso_cod;     --Marlyza - 2019-03-04 ? deb_tour_invocation n'est jamais déclenché à cause du manque de cible, BUG ?, Je rajoute le cas !
  -- elsif v_cible_cod is null then
  --   select into v_cible_cod COALESCE(perso_cible, perso_cod) from perso where perso_cod = v_perso_cod;
	-- end if;

  -- le protagoniste est la cible en cours ou le perso lui même s'il n'y en a pas. --Marlyza - 2020-05-20
  select into v_cible_cod COALESCE(perso_cible, perso_cod) from perso where perso_cod = v_perso_cod;

  -- eventuellement les fonction du monstre générique
	select into v_gmon_cod perso_gmon_cod from perso where perso_cod = v_perso_cod;

  -- code de retour
	code_retour := '';

  -- boucle sur toutes les fonctions specifiques sur l'évenement
	for ligne_fonction in (
		select * from fonction_specifique
		where (fonc_gmon_cod = coalesce(v_gmon_cod, -1) OR (fonc_perso_cod = v_perso_cod))
			and fonc_type = v_evenement
			and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
		)
	loop
		code_fonction := ligne_fonction.fonc_nom;

		-- --------------- dealing with data injection (seulement si pas déjà en cours) ! -- c'est la différence avec le "execute_fonctions" de base !
    retour_fonction := execute_fonction_specifique(v_perso_cod, v_cible_cod, ligne_fonction.fonc_cod, v_param) ;

		if coalesce(retour_fonction, '') != '' then
			-- code_retour := code_retour || code_fonction || ' : ' || coalesce(retour_fonction, '') || '<br />';
			code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
		end if;
	end loop;

	if code_retour != '' then
		code_retour := replace('<br /><b>Effets automatiques :</b><br />' || code_retour, '<br /><br />', '<br />') || '<br />';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.execute_fonctions_ext(integer, integer, character, json) OWNER TO delain;

--
-- Name: FUNCTION execute_fonctions_ext(integer, integer, character, json); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION execute_fonctions_ext(integer, integer, character, json) IS 'Exécute les fonctions liées au perso_cod donné, pour le type d’événement donné : ''D'' pour Début de tour, ''M'' pour Mort, ''T'' pour Tueur, ''A'' pour Attaque, ''AC'' pour attaque subie, ''AE'' pour attaque esquivée, ''ACE'' pour Attaque subie Esquivée, ''AT'' pour attaque qui touche, ''ACT'' pour attaque subie qui touche.';
