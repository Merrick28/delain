CREATE OR REPLACE FUNCTION public.mission_texte(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_texte                                    */
/*   Génère le texte descriptif d’une mission                */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 22/05/2013                                        */
/* MàJ le 28/10/2013 prise en compte de textes spécifiques   */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider

	donnees record;            -- Les données de la mission

	v_pos_x integer;            -- La position X
	v_pos_y integer;            -- La position Y
	v_pos_etage text;           -- L’étage
	v_perso_monstre text;        -- Le nom du monstre / personnage qui est ciblé
	v_nom_objet text;            -- Le nom de l’objet
begin
	code_retour := 'Erreur ! Texte non trouvé'; -- Par défaut, aucun retour
	select into donnees *
	from mission_perso_faction_lieu where mpf_cod = code_mission;

	select into code_retour fmiss_libelle from faction_missions
	where fmiss_miss_cod = donnees.mpf_miss_cod AND fmiss_fac_cod = donnees.mpf_fac_cod;

	if not found or coalesce(code_retour, '') = '' then
		select into code_retour miss_libelle from missions where miss_cod = donnees.mpf_miss_cod;
	end if;

	if donnees.mpf_pos_cod IS NOT NULL then
		select into v_pos_x, v_pos_y, v_pos_etage pos_x, pos_y, etage_libelle
		from positions
		inner join etage on etage_numero = pos_etage
		where pos_cod = donnees.mpf_pos_cod;

		code_retour := replace(code_retour, '[position]', '[' || v_pos_x::text || ', ' || v_pos_y::text || ', ' || v_pos_etage || ']');
	end if;

	if donnees.mpf_nombre IS NOT NULL then
		code_retour := replace(code_retour, '[nombre]', donnees.mpf_nombre::text);
	end if;

	if donnees.mpf_recompense IS NOT NULL then
		code_retour := replace(code_retour, '[recompense]', donnees.mpf_recompense::text || ' brouzoufs');
	end if;

	if donnees.mpf_delai IS NOT NULL then
		code_retour := replace(code_retour, '[délai]', donnees.mpf_delai::text || ' jours');
	end if;
	
	if donnees.mpf_cible_perso_cod IS NOT NULL then
		select into v_perso_monstre perso_nom from perso where perso_cod = donnees.mpf_cible_perso_cod;
		if not found then
			v_perso_monstre = '-- Personnage ou monstre disparu --';
		end if;
		code_retour := replace(code_retour, '[personnage]', v_perso_monstre);
	elsif donnees.mpf_gmon_cod is not null then
		select into v_perso_monstre gmon_nom from monstre_generique where gmon_cod = donnees.mpf_gmon_cod;
		code_retour := replace(code_retour, '[personnage]', v_perso_monstre);
	end if;
	
	if donnees.mpf_obj_cod IS NOT NULL then
		select into v_nom_objet obj_nom from objets where obj_cod = donnees.mpf_obj_cod;
		if not found then
			v_nom_objet = '-- Objet disparu --';
		end if;
		code_retour := replace(code_retour, '[objet]', v_nom_objet);
	end if;
	
	if donnees.mpf_texte IS NOT NULL then
		code_retour := replace(code_retour, '[rumeur]', donnees.mpf_texte);
		code_retour := replace(code_retour, '[autre]', donnees.mpf_texte);
	end if;
	
	-- Cas particuliers
	
	-- Cas de [autre]
	
	
	return code_retour;
end;$function$

