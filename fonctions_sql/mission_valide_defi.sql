CREATE OR REPLACE FUNCTION public.mission_valide_defi(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_defi                              */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 05/02/2014                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	ligne record;              -- Contient les données des missions en cours
	v_meilleur_defi integer;   -- Le meilleur résultat de défi obtenu : 0 (rien), 1 (perdu), 2 (nul), 3 (adversaire abandonne), 4 (gagné)
	v_avancement integer;      -- L’avancement de la mission (de 0 à 9)
begin
	code_retour := '';         -- Par défaut, aucun retour
	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	v_meilleur_defi := 0;

	-- On récupère le meilleur des défis de l’aventurier, envoyé à sa cible, initiés après le début de la mission.
	select into v_meilleur_defi 
		case when defi_statut = 3 and defi_vainqueur = 'L' then 4          -- défi gagné
		     when defi_statut in (2, 4) and defi_vainqueur = 'L' then 3    -- défi abandonné par l’adversaire
		     when defi_statut = 5 then 2                                   -- match nul
		     when defi_statut in (3, 4) and defi_vainqueur = 'C' then 1    -- défi perdu ou abandonné
		     else 0                                                        -- défi en cours, ou annulé.
		end
	from defi
	where defi_lanceur_cod = ligne.mpf_perso_cod            -- défis lancés par le perso...
		and defi_cible_cod = ligne.mpf_cible_perso_cod  -- ... contre la bonne cible...
		and defi_date_debut > ligne.mpf_date_debut      -- ... après avoir accepté la mission
	order by case when defi_statut = 3 and defi_vainqueur = 'L' then 4
		     when defi_statut in (2, 4) and defi_vainqueur = 'L' then 3
		     when defi_statut = 5 then 2
		     when defi_statut in (3, 4) and defi_vainqueur = 'C' then 1
		     else 0 end desc
	limit 1;

	if not found then
		v_meilleur_defi := 0;
	end if;

	if v_meilleur_defi in (3, 4) then	-- Défi terminé et gagné !
		-- Paramètres : mission, nouveau statut (20 == terminée), avancement
		code_retour := mission_modifie_statut(code_mission, 20, 0);
	
		-- On crée un événement
		perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
			'[perso_cod1] a réalisé le contrat demandé pour sa mission.', 'O', '[mpf_cod]=' || code_mission::text);
	elsif v_meilleur_defi = 2 then
		v_avancement := 9;

		-- Paramètres : mission, nouveau statut (10 == en cours), avancement
		code_retour := mission_modifie_statut(code_mission, 10, v_avancement);
		code_retour := code_retour || '<br />Ce match nul n’est pas si mal...';
	elsif v_meilleur_defi = 1 then
		v_avancement := 1;

		-- Paramètres : mission, nouveau statut (10 == en cours), avancement
		code_retour := mission_modifie_statut(code_mission, 10, v_avancement);
		code_retour := code_retour || '<br />La perte de votre défi est une humiliation pour nous ! Reprenez-vous !';
	end if;

	return code_retour;
end;$function$

