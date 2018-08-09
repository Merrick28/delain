CREATE OR REPLACE FUNCTION public.mission_initialise_carte(integer, integer)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_initialise_carte                         */
/*   Crée une mission de type « carte » dans l’étage donné   */
/*    et pour la faction donnée                              */
/*   on passe en paramètres :                                */
/*   $1 = fac_cod : l’identifiant de la faction              */
/*   $2 = etage_numero : l’identifiant de l’étage            */
/* on a en sortie true ou false suivant que ça se soit bien  */
/* passé ou non.                                             */
/*************************************************************/
/* Créé le 31/01/2013                                        */
/*************************************************************/
declare
	v_fac_cod alias for $1;    -- Le code de la faction concernée
	v_etage_numero alias for $2; -- Le code de l’étage

	code_mission integer;      -- Le code du type de mission à créer
	v_pos_cod integer;         -- Le code de la position à explorer
	v_delai integer;           -- Le délai autorisé, en jours, pour réaliser la mission
	v_rayon integer;           -- Le rayon demandé autour de la case cible
	v_recompense integer;      -- La récompense, en brouzoufs
	v_difficulte integer;      -- Un indice de difficulté de la mission
	v_etage_choisi integer;    -- L’étage choisi pour la mission
	v_etage_reference integer; -- La référence de l’étage de la mission
	v_etage_reference_choisi integer; -- La référence de l’étage où se situe la case à découvrir

begin
	code_mission := 10;         -- Mission de type carte

	-- On détermine le poids, entre 50 et 150
	v_rayon := 2 + lancer_des(1, 4);

	-- Sélection de l’étage à explorer
	select into v_etage_choisi, v_etage_reference_choisi
		etage_numero, etage_reference
	from etage
	where etage_numero in (select etages_adjacents(v_etage_numero))
	order by random()
	limit 1;
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

	-- On choisit une case au hasard dans cet étage
	v_pos_cod := pos_aleatoire(v_etage_choisi);
	if v_pos_cod is null then
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
	v_difficulte := v_difficulte + (v_rayon - 2) / 2;

	v_recompense := v_difficulte * 100 + lancer_des(1, 100);

	-- On détermine le délai de réalisation (de 8 à 20 jours)
	v_delai := 7 + lancer_des(1, 13);
	
	-- Et on insère les données dans la table des missions
	insert into mission_perso_faction_lieu (mpf_miss_cod, mpf_perso_cod, mpf_fac_cod, mpf_date_debut, mpf_date_fin, mpf_etage_numero, mpf_obj_cod,
		mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod, mpf_statut, mpf_texte, mpf_delai, mpf_recompense)
	values (code_mission, NULL, v_fac_cod, now(), NULL, v_etage_numero, NULL,
		v_pos_cod, NULL, NULL, v_rayon, NULL, 0, NULL, v_delai, v_recompense);
	
	return true;
end;$function$

