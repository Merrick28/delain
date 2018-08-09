CREATE OR REPLACE FUNCTION public.mission_valide_carte(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_carte                             */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 29/01/2013                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	ligne record;              -- Contient les données des missions en cours
	v_etage integer;           -- L’étage de la position à cartographier
	v_etage_id integer;        -- L’id de l’étage à cartographier
	v_x integer;               -- Le X de la position à cartographier
	v_y integer;               -- Le Y de la position à cartographier
	v_rayon integer;           -- Le rayon à cartographier autour de la case
	v_nombre_connu integer;    -- Le nombre de cases connues autour de la position ciblée
	v_nombre_total integer;    -- Le nombre de cases existantes autour de la position ciblée
	v_requete text;            -- Le texte de la requête
	v_urgence integer;         -- L’urgence de la mission (de 1 à 5)
	v_avancement integer;      -- L’avancement de la mission (de 0 à 9)
begin
	code_retour := '';         -- Par défaut, aucun retour

	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	v_rayon := ligne.mpf_nombre;

	select into v_etage, v_x, v_y, v_etage_id
		pos_etage, pos_x, pos_y, etage_cod
	from positions
	inner join etage on etage_numero = pos_etage
	where pos_cod = ligne.mpf_pos_cod;

	-- on compte le nombre de cases connues autour de la case ciblée :
	v_requete := 'select count(*)
		from perso_vue_pos_' || v_etage_id::text || '
		inner join positions on pos_cod = pvue_pos_cod
		where pos_etage = ' || v_etage::text || ' 
			and pos_x between ' || (v_x - v_rayon)::text || ' and ' || (v_x + v_rayon)::text || '
			and pos_y between ' || (v_y - v_rayon)::text || ' and ' || (v_y + v_rayon)::text || '
			and pvue_perso_cod = ' || ligne.mpf_perso_cod::text;
	execute v_requete into v_nombre_connu;

	-- on compte le nombre de cases totales autour de la case ciblée :
	v_requete := 'select count(*)
		from positions
		where pos_etage = ' || v_etage::text || ' 
			and pos_x between ' || (v_x - v_rayon)::text || ' and ' || (v_x + v_rayon)::text || '
			and pos_y between ' || (v_y - v_rayon)::text || ' and ' || (v_y + v_rayon)::text;
	execute v_requete into v_nombre_total;

	v_avancement := (v_nombre_connu * 10) / v_nombre_total;

	-- On vérifie si la position et toutes les cases alentour sont bien connues.
	if v_nombre_connu >= v_nombre_total then
		-- Paramètres : mission, nouveau statut (20 == terminée), avancement
		code_retour := mission_modifie_statut(code_mission, 20, 0);

		-- On crée un événement
		perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
			'[perso_cod1] a fini la cartographie qui lui était demandée.', 'O', '[mpf_cod]=' || code_mission::text);
	elsif v_nombre_connu > 0 then
		-- Paramètres : mission, nouveau statut (10 == en cours), avancement
		code_retour := mission_modifie_statut(code_mission, 10, v_avancement);
	end if;
	return code_retour;
end;$function$

