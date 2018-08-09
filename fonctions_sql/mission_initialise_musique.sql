CREATE OR REPLACE FUNCTION public.mission_initialise_musique(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_initialise_musique                       */
/*   Crée une mission de type « musique » dans l’étage donné */
/*    et pour la faction donnée                              */
/*   on passe en paramètres :                                */
/*   $1 = fac_cod : l’identifiant de la faction              */
/*   $2 = etage_numero : l’identifiant de l’étage            */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 18/10/2013                                        */
/*************************************************************/
declare
	v_fac_cod alias for $1;    -- Le code de la faction concernée
	v_etage_numero alias for $2; -- Le code de l’étage

	code_mission integer;      -- Le code du type de mission à créer
	v_temple integer;          -- Le temple où aller chanter
	
	v_delai integer;           -- Le délai autorisé, en jours, pour réaliser la mission
	v_recompense integer;      -- La récompense, en brouzoufs
	v_difficulte integer;      -- Un indice de difficulté de la mission
	v_etage_ref integer;       -- L’étage de référence (pour jauger de la difficulté)

begin
	code_mission := 13;         -- Mission de type rumeur

	-- Choix du lieu où jouer de la musique, parmi les lieux de la faction
	select into v_temple, v_etage_ref pos_cod, etage_reference
	from v_factions_lieux
	where fac_cod = v_fac_cod
		and pos_etage in (select etages_adjacents(v_etage_numero))
	order by random()
	limit 1;

	if not found then
		return false;	-- Si aucun temple, bah c’est foutu...
	end if;

	-- On règle l’indice de difficulté, et la récompense.
	v_difficulte := v_etage_ref * -1 + 1;
	v_recompense := v_difficulte * 100 + lancer_des(1, v_difficulte * 100);

	-- On détermine le délai de réalisation (de 15 à 30 jours)
	v_delai := 14 + lancer_des(1, 16);
	
	-- Et on insère les données dans la table des missions
	insert into mission_perso_faction_lieu (mpf_miss_cod, mpf_perso_cod, mpf_fac_cod, mpf_date_debut, mpf_date_fin, mpf_etage_numero, mpf_obj_cod,
		mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod, mpf_statut, mpf_texte, mpf_delai, mpf_recompense)
	values (code_mission, NULL, v_fac_cod, now(), NULL, v_etage_numero, NULL,
		v_temple, NULL, NULL, NULL, NULL, 0, NULL, v_delai, v_recompense);
	
	return true;
end;$function$

