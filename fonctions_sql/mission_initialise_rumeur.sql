CREATE OR REPLACE FUNCTION public.mission_initialise_rumeur(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_initialise_rumeur                        */
/*   Crée une mission de type « rumeur » dans l’étage donné  */
/*    et pour la faction donnée                              */
/*   on passe en paramètres :                                */
/*   $1 = fac_cod : l’identifiant de la faction              */
/*   $2 = etage_numero : l’identifiant de l’étage            */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 30/01/2013                                        */
/*************************************************************/
declare
	v_fac_cod alias for $1;    -- Le code de la faction concernée
	v_etage_numero alias for $2; -- Le code de l’étage

	code_mission integer;       -- Le code du type de mission à créer
	v_rumeur text;             -- Le texte de la rumeur à publier
	v_fac_nom text;            -- Le nom de la faction émettrice
	v_ennemie_fac_nom text;    -- Le nom d’une faction ennemie
	v_amie_fac_nom text;       -- Le nom d’une faction amie
	v_temp_amitie integer;     -- Valeur de l’estime entre deux factions
	v_temp_inimitie integer;   -- Valeur de l’estime entre deux factions
	v_delai integer;           -- Le délai autorisé, en jours, pour réaliser la mission
	v_poids integer;           -- Le poids minimal demandé pour la rumeur
	v_recompense integer;      -- La récompense, en brouzoufs
	v_difficulte integer;      -- Un indice de difficulté de la mission

begin
	code_mission := 3;         -- Mission de type rumeur

	-- On détermine le poids, entre 50 et 150
	v_poids := 40 + lancer_des(1, 11) * 10;

	-- Sélection d’un texte de rumeur
	select into v_rumeur dmiss_donnee from missions_donnees
	where dmiss_miss_cod = code_mission
		and dmiss_etiquette = 'rumeur'
	order by random()
	limit 1;
	if not found then
		return false;
	end if;

	-- Détermination des seuils de factions 
	select into v_temp_amitie, v_temp_inimitie max(f2f_note_estime) - 2, min(f2f_note_estime) + 2
	from faction_relation_faction
	where f2f_sujet_cod = v_fac_cod;

	-- Détermination d’une faction ennemie
	select into v_ennemie_fac_nom fac_nom
	from factions
	inner join faction_relation_faction on f2f_objet_cod = fac_cod
	where f2f_sujet_cod = v_fac_cod and f2f_note_estime <= v_temp_inimitie
	order by random() limit 1;

	-- Détermination d’une faction amie
	select into v_amie_fac_nom fac_nom
	from factions
	inner join faction_relation_faction on f2f_objet_cod = fac_cod
	where f2f_sujet_cod = v_fac_cod and f2f_note_estime >= v_temp_amitie
	order by random() limit 1;

	-- Détermination du nom de la faction courante
	select into v_fac_nom fac_nom
	from factions
	where fac_cod = v_fac_cod;

	-- On règle l’indice de difficulté, et la récompense.
	if v_rumeur LIKE '%[faction_ennemie]%' then
		v_difficulte := 3;
	else
		v_difficulte := 2;
	end if;
	v_recompense := v_difficulte * 100 + lancer_des(1, 100);

	-- On remplace tout ça dans le texte de la rumeur
	v_rumeur := replace(v_rumeur, '[faction_amie]', v_amie_fac_nom);
	v_rumeur := replace(v_rumeur, '[faction_ennemie]', v_ennemie_fac_nom);
	v_rumeur := replace(v_rumeur, '[faction]', v_fac_nom);

	-- On détermine le délai de réalisation (de 8 à 15 jours)
	v_delai := 7 + lancer_des(1, 8);
	
	-- Et on insère les données dans la table des missions
	insert into mission_perso_faction_lieu (mpf_miss_cod, mpf_perso_cod, mpf_fac_cod, mpf_date_debut, mpf_date_fin, mpf_etage_numero, mpf_obj_cod,
		mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod, mpf_statut, mpf_texte, mpf_delai, mpf_recompense)
	values (code_mission, NULL, v_fac_cod, now(), NULL, v_etage_numero, NULL,
		NULL, NULL, NULL, v_poids, NULL, 0, v_rumeur, v_delai, v_recompense);
	
	return true;
end;$function$

