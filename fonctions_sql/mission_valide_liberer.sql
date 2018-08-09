CREATE OR REPLACE FUNCTION public.mission_valide_liberer(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_liberer                           */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 19/02/2013                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	ligne record;              -- Contient les données des missions en cours
	v_nombre integer;          -- Le nombre de monstres dans la zone à libérer
	v_x integer;               -- La coordonnée X du centre de la zone à libérer
	v_y integer;               -- La coordonnée Y du centre de la zone à libérer
	v_etage integer;           -- L’étage de la zone à libérer
	v_pos_cod integer;         -- La position du perso
begin
	code_retour := '';         -- Par défaut, aucun retour

	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	select into v_etage, v_x, v_y
		pos_etage, pos_x, pos_y
	from positions
	where pos_cod = ligne.mpf_pos_cod;

	select into v_pos_cod
		ppos_pos_cod
	from perso_position
	where ppos_perso_cod = ligne.mpf_perso_cod;

	select into v_nombre
		count(*)
	from positions
	inner join perso_position on ppos_pos_cod = pos_cod
	inner join perso on perso_cod = ppos_perso_cod
	where pos_x between (v_x - ligne.mpf_nombre) and (v_x + ligne.mpf_nombre)
		and pos_y between (v_y - ligne.mpf_nombre) and (v_y + ligne.mpf_nombre)
		and pos_etage = v_etage
		and perso_type_perso = 2
		and perso_actif = 'O'
		and perso_pnj <> 1
		and perso_tangible = 'O'
		and trajectoire_vue(ligne.mpf_pos_cod, pos_cod) = 1;

	-- On vérifie si les critères sont respectés.
	if v_nombre = 0 and v_pos_cod = ligne.mpf_pos_cod then
		-- Paramètres : mission, nouveau statut (20 == terminée), avancement
		code_retour := mission_modifie_statut(code_mission, 20, 0);
		
		-- On crée un événement
		perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
			'[perso_cod1] est parvenu à libérer la zone demandée pour sa mission.', 'O', '[mpf_cod]=' || code_mission::text);
	end if;
	return code_retour;
end;$function$

