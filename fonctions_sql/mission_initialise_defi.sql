CREATE OR REPLACE FUNCTION public.mission_initialise_defi(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_initialise_defi                          */
/*   Crée une mission de type « défi » dans l’étage donné    */
/*    et pour la faction donnée                              */
/*   on passe en paramètres :                                */
/*   $1 = fac_cod : l’identifiant de la faction              */
/*   $2 = etage_numero : l’identifiant de l’étage            */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 05/02/2014                                        */
/*************************************************************/
declare
	v_fac_cod alias for $1;    -- Le code de la faction concernée
	v_etage_numero alias for $2; -- Le code de l’étage

	code_mission integer;      -- Le code du type de mission à créer
	v_etage_reference integer; -- Le code de l’étage de référence (pour limiter le niveau de l’adversaire)
	v_cible integer;           -- Le code de la cible à défier
	v_ennemie_fac_nom text;    -- Le nom d’une faction ennemie
	v_temp_inimitie integer;   -- Valeur de l’estime entre deux factions
	v_delai integer;           -- Le délai autorisé, en jours, pour réaliser la mission
	v_recompense integer;      -- La récompense, en brouzoufs
	v_difficulte integer;      -- Un indice de difficulté de la mission

begin
	code_mission := 12;         -- Mission de type défi

	-- Étage de référence
	select into v_etage_reference etage_reference from etage where etage_numero = v_etage_numero;

	-- Détermination des seuils de factions 
	select into v_temp_inimitie min(f2f_note_estime) + 2
	from faction_relation_faction
	where f2f_sujet_cod = v_fac_cod;

	-- Détermination d’une cible ennemie
	select into v_ennemie_fac_nom, v_cible fac_nom, perso_cod
	from factions
	inner join faction_relation_faction on f2f_objet_cod = fac_cod
	inner join faction_perso on pfac_fac_cod = fac_cod
	inner join perso on perso_cod = pfac_perso_cod
	where f2f_sujet_cod = v_fac_cod and f2f_note_estime <= v_temp_inimitie
		and pfac_rang_numero > 0 -- personnages impliqués dans leur faction
		and pfac_statut = 0      -- personnages non démissionnaires
		and perso_actif = 'O'    -- personnages actifs
		and perso_type_perso = 1 -- personnages (hors familiers et monstres)
		and (perso_niveau < abs(v_etage_reference - 1) * 10 or v_etage_reference < -8) -- Niveau limité au dessus du -9
		and (perso_niveau > abs(v_etage_reference) * 4) -- Niveau min limité
	order by random() limit 1;

	if not found then
		return false;
	end if;

	-- On règle l’indice de difficulté, et la récompense.
	v_difficulte := abs(v_etage_reference);
	v_recompense := 300 + 6 * lancer_des(v_difficulte + 1, 100);

	-- On détermine le délai de réalisation (15 à 25 jours)
	v_delai := 14 + lancer_des(1, 11);

	-- Et on insère les données dans la table des missions
	insert into mission_perso_faction_lieu (mpf_miss_cod, mpf_perso_cod, mpf_fac_cod, mpf_date_debut, mpf_date_fin, mpf_etage_numero, mpf_obj_cod,
		mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod, mpf_statut, mpf_texte, mpf_delai, mpf_recompense)
	values (code_mission, NULL, v_fac_cod, now(), NULL, v_etage_numero, NULL,
		NULL, NULL, v_cible, NULL, NULL, 0, v_ennemie_fac_nom, v_delai, v_recompense);

	return true;
end;$function$

