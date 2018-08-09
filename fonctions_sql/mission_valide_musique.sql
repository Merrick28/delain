CREATE OR REPLACE FUNCTION public.mission_valide_musique(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_musique                           */
/*   Vérifie les conditions de validation des missions du    */
/*          perso                                            */
/*   on passe en paramètres :                                */
/*   $1 = mpf_cod : l’identifiant de la mission              */
/* on a en sortie une chaine à afficher                      */
/*************************************************************/
/* Créé le 18/10/2013                                        */
/*************************************************************/
declare
	code_retour text;          -- Le résultat, affichable, de la fonction
	code_mission alias for $1; -- Le code de la mission à valider
	ligne record;              -- Contient les données des missions en cours
	v_avancement integer;      -- L’avancement de la mission (de 0 à 9)
	v_autorise_psaumes char(1);-- Indique si un chant religieux est accepté.
	v_autorise_paillard char(1);-- Indique si une chanson paillarde est autorisée.
	v_autorise_marche char(1); -- Indique si une marche guerrière est autorisée.

begin
	code_retour := '';         -- Par défaut, aucun retour
	v_autorise_psaumes = 'N';
	v_autorise_paillard = 'N';
	v_autorise_marche = 'N';

	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	-- On détermine le type de chants autorisés
	select into v_autorise_psaumes, v_autorise_paillard, v_autorise_marche
		case when dieu_nom IS NULL OR dieu_cod < 0 then 'N' else 'O' end,
		case when dieu_nom = 'Tonto' OR dieu_cod < 0 then 'O' else 'N' end,
		case when dieu_nom = 'Balgur' OR dieu_cod < 0 then 'O' else 'N' end
	from factions
	left outer join (
		select dieu_cod, dieu_nom from dieu
		union select -1, 'Malkiar'
		union select -2, 'Morbelin'
	) d on fac_nom LIKE '%' || dieu_nom || '%'
	where fac_cod = ligne.mpf_fac_cod;

	-- On vérifie si un événement correspondant totalement a bien été effectué par le personnage.
	select into v_avancement 
		case when v_autorise_psaumes = 'O' AND levt_tevt_cod = 90 then 10
			when v_autorise_paillard = 'O' AND levt_tevt_cod = 74 then 10
			when v_autorise_marche = 'O' AND levt_tevt_cod = 72 then 10
			else 9 end
	from ligne_evt
	where levt_tevt_cod IN (70, 71, 72, 73, 74, 90)
		AND levt_perso_cod1 = ligne.mpf_perso_cod
		AND levt_date >= ligne.mpf_date_debut
		AND levt_parametres = '[pos_cod]=' || ligne.mpf_pos_cod::text
	order by case when v_autorise_psaumes = 'O' AND levt_tevt_cod = 90 then 10
			when v_autorise_paillard = 'O' AND levt_tevt_cod = 74 then 10
			when v_autorise_marche = 'O' AND levt_tevt_cod = 72 then 10
			else 9 end desc
	limit 1;

	if found then
		-- On vérifie que le chant correspond à ce qui était demandé
		if v_avancement < 10 then  -- Le chant n’était pas assez RP (une berceuse sur un portail démoniaque, par exemple...)
			-- Paramètres : mission, nouveau statut (10 == en cours), avancement
			code_retour := mission_modifie_statut(code_mission, 10, v_avancement);

			code_retour := code_retour || '<br />Votre performance a été entendue, mais comment dire... Elle était un peu hors de propos !<br />
				Vous pourriez peut-être faire mieux ?';
		else
			-- Paramètres : mission, nouveau statut (20 == terminée), avancement
			code_retour := mission_modifie_statut(code_mission, 20, 0);
		
			-- On crée un événement
			perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
				'[perso_cod1] a régalé son auditoire, comme demandé.', 'O', '[mpf_cod]=' || code_mission::text);
		end if;
	end if;
	
	return code_retour;
end;$function$

