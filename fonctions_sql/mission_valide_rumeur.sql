CREATE OR REPLACE FUNCTION public.mission_valide_rumeur(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_rumeur                            */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 28/01/2013                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	ligne record;              -- Contient les données des missions en cours
	v_rumeur integer;          -- Le code de la rumeur répondant à la mission
	v_rumeur_poids integer;    -- Le poids de la rumeur
	v_poids_demande integer;   -- Le poids minimal demandé
	v_avancement integer;      -- L’avancement de la mission (de 0 à 9)
begin
	code_retour := '';         -- Par défaut, aucun retour
	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	-- On vérifie si la rumeur a bien été propagée par le personnage.
	select into v_rumeur rum_cod from rumeurs where rum_perso_cod = ligne.mpf_perso_cod AND trim(lower(rum_texte)) = trim(lower(ligne.mpf_texte));
	if found then
		-- On vérifie que le perso a payé suffisamment (on peut cumuler les poids de plusieurs rumeurs identiques...)
		select into v_rumeur_poids sum(rum_poids) from rumeurs where rum_perso_cod = ligne.mpf_perso_cod AND trim(lower(rum_texte)) = trim(lower(ligne.mpf_texte));
		
		-- Si le poids est insuffisant, on ne valide pas...
		if v_rumeur_poids < ligne.mpf_nombre then
			v_avancement := (v_rumeur_poids * 10) / ligne.mpf_nombre;

			-- Paramètres : mission, nouveau statut (10 == en cours), avancement
			code_retour := mission_modifie_statut(code_mission, 10, v_avancement);

			code_retour := code_retour || '<br />La rumeur que vous avez lancé n’a pas été assez écoutée !<br />
				Il faut y mettre au moins ' || ligne.mpf_nombre::text || ' brouzoufs pour que le résultat soit convainquant...';
		else
			-- Paramètres : mission, nouveau statut (20 == terminée), avancement
			code_retour := mission_modifie_statut(code_mission, 20, 0);
		
			-- On crée un événement
			perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
				'[perso_cod1] a répandu la rumeur demandée pour sa mission.', 'O', '[mpf_cod]=' || code_mission::text);
		end if;
	end if;
	
	return code_retour;
end;$function$

