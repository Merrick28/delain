CREATE OR REPLACE FUNCTION public.mission_valide_coursier(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_valide_coursier                          */
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
	v_pos_cod integer;         -- La position du personnage
	v_possede boolean;         -- Le personnage possède-t-il l’objet ?
begin
	code_retour := '';         -- Par défaut, aucun retour

	select into ligne * from mission_perso_faction_lieu where mpf_cod = code_mission;
	if not found then
		return code_retour;
	end if;

	select into v_possede
		(EXISTS(select 1 from perso_objets
			where perobj_obj_cod = ligne.mpf_obj_cod
				and perobj_perso_cod = ligne.mpf_perso_cod
			limit 1));

	select into v_pos_cod
		ppos_pos_cod
	from perso_position
	where ppos_perso_cod = ligne.mpf_perso_cod;

	-- On vérifie si les critères sont respectés.
	if v_possede and v_pos_cod = ligne.mpf_pos_cod then
		-- Paramètres : mission, nouveau statut (20 == terminée), avancement
		code_retour := mission_modifie_statut(code_mission, 20, 0);

		-- On supprime l’objet
		perform f_del_objet(ligne.mpf_obj_cod);
		
		-- On crée un événement
		perform insere_evenement(ligne.mpf_perso_cod, ligne.mpf_perso_cod, 93,
			'[perso_cod1] a donné un objet dans le cadre d’une mission', 'O', '[mpf_cod]=' || code_mission::text);
	end if;
	return code_retour;
end;$function$

