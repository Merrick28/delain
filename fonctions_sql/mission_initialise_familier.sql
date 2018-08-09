CREATE OR REPLACE FUNCTION public.mission_initialise_familier(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_initialise_familier                      */
/*   Crée une mission de type « garde du corps » dans        */
/*   l’étage donné et pour la faction donnée                 */
/*   on passe en paramètres :                                */
/*   $1 = fac_cod : l’identifiant de la faction              */
/*   $2 = etage_numero : l’identifiant de l’étage            */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 28/10/2013                                        */
/*************************************************************/
declare
	v_fac_cod alias for $1;    -- Le code de la faction concernée
	v_etage_numero alias for $2; -- Le code de l’étage

	code_mission integer;      -- Le code du type de mission à créer
	v_pos_cod integer;         -- Le code de la position où amener l’objet
	v_delai integer;           -- Le délai autorisé, en jours, pour réaliser la mission
	v_recompense integer;      -- La récompense, en brouzoufs
	v_difficulte integer;      -- Un indice de difficulté de la mission
	v_etage_choisi integer;    -- L’étage choisi pour la mission
	v_etage_reference integer; -- La référence de l’étage de la mission
	v_etage_reference_choisi integer; -- La référence de l’étage où se situe la case à découvrir

begin
	code_mission := 16;         -- Mission de type garde du corps

	-- On détermine le lieu de destination : n’importe quel lieu dans lequel la faction est présente, dans l’étage courant et les étages adjacents.
	select INTO v_pos_cod, v_etage_choisi, v_etage_reference_choisi pos_cod, pos_etage, etage_reference
	from v_factions_lieux
	where fac_cod = v_fac_cod
		and pos_etage in (select etages_adjacents(v_etage_numero))
	ORDER BY random()
	LIMIT 1;
	if not found then
		return false;
	end if;
	
	-- Récupération de données sur l’étage en cours
	select into v_etage_reference etage_reference
	from etage
	where etage_numero = v_etage_numero;
	if not found then
		return false;
	end if;
	
	-- On détermine la difficulté
	v_difficulte := 2;
	if v_etage_choisi <> v_etage_numero then 
		v_difficulte := v_difficulte + 1;
	end if;
	if v_etage_reference_choisi <> v_etage_reference then 
		v_difficulte := v_difficulte + 1;
	end if;

	v_recompense := v_difficulte * 300 + lancer_des(1, 300);

	-- On détermine le délai de réalisation (de 9 à 15 jours)
	v_delai := 8 + lancer_des(1, 7);
	
	-- Et on insère les données dans la table des missions
	insert into mission_perso_faction_lieu (mpf_miss_cod, mpf_perso_cod, mpf_fac_cod, mpf_date_debut, mpf_date_fin, mpf_etage_numero, mpf_obj_cod,
		mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod, mpf_statut, mpf_texte, mpf_delai, mpf_recompense)
	values (code_mission, NULL, v_fac_cod, now(), NULL, v_etage_numero, NULL,
		v_pos_cod, NULL, NULL, NULL, NULL, 0, NULL, v_delai, v_recompense);
	
	return true;
end;$function$

