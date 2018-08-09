CREATE OR REPLACE FUNCTION public.mission_valide_familier(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_familier                          */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 13/12/2013                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	ligne record;              -- Contient les données des missions en cours
	v_pos_cod_cible integer;   -- La position de l’émissaire à escorter
	v_vivant boolean;          -- L’émissaire est-il toujours vivant ?
begin
	code_retour := '';         -- Par défaut, aucun retour

	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	select into v_vivant
		(EXISTS(select 1 from perso
			where perso_cod = ligne.mpf_cible_perso_cod
				and perso_actif = 'O'
			limit 1));

	select into v_pos_cod_cible
		ppos_pos_cod
	from perso_position
	where ppos_perso_cod = ligne.mpf_cible_perso_cod;
	if not found then
		v_pos_cod_cible := 0;
	end if;

	-- On vérifie si les critères sont respectés.
	if v_vivant and v_pos_cod_cible = ligne.mpf_pos_cod then
		-- Paramètres : mission, nouveau statut (20 == terminée), avancement
		code_retour := mission_modifie_statut(code_mission, 20, 0);

		-- On désactive le pnj
		update perso set perso_actif = 'N' where perso_cod = ligne.mpf_cible_perso_cod;
		
		-- On crée un événement
		perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
			'[perso_cod1] a escorté un monstre dans le cadre d’une mission', 'O', '[mpf_cod]=' || code_mission::text);
	end if;

	return code_retour;
end;$function$

