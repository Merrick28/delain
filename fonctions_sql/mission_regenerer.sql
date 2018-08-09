CREATE OR REPLACE FUNCTION public.mission_regenerer()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_regenerer                                */
/*   Régénère les missions de chaque faction à chaque étage  */
/*   Sortie : missions recréées                              */
/*************************************************************/
/* Créé le 19/02/2013                                        */
/* 23/10/2013 - Utilisation de la vue v_factions_lieux       */
/*************************************************************/
declare
	ligne record;             -- Ligne de boucle sur les étages et factions
	nombre_missions integer;  -- Nombre de missions à recréer
	texte_retour text;
	itemp integer;
	v_fonction_init text;
	creation_ok boolean;
	v_nom_mission text;
	texte_creation text;
	au_moins_une_mission boolean;

begin
	texte_retour := e'\
Suppression des missions non relevées... ';

	-- On purge les vieilles missions non prises
	delete from mission_perso_faction_lieu
	where mpf_date_debut < now() - '7 days'::interval
		and mpf_statut = 0;

	get diagnostics itemp = row_count;
	texte_retour := texte_retour || itemp::text || ' suppression(s).';
	texte_retour := texte_retour || e'\
Création des nouvelles missions...';

	for ligne in
		select distinct fac_cod, fac_nom, pos_etage, etage_libelle
		from v_factions_lieux
		order by fac_nom, pos_etage
	loop
		au_moins_une_mission := false;
		nombre_missions := lancer_des(1, 3) - 1;
		texte_creation := e'\
\	Faction ' || ligne.fac_nom || ', dans ' || ligne.etage_libelle || ' : '
			|| nombre_missions::text || ' nouvelle(s) mission(s) (';
		itemp := 0;
		while itemp < nombre_missions and nombre_missions < 10	-- Le nombre de missions peut monter en cas d’échec, mais on limite...
		loop
			-- À améliorer !
			-- La partie aléatoire n’est pas intuitive.
			-- Avec order by fmiss_proba - random(), si on a une mission de proba m1 et une de proba m2
			-- la proba réelle de tomber sur m1 est Pm1 = 1/2 x (m1 - m2 + 1)² 
			-- avec m1 = 0.1 et m2 = 0.9, Pm1 = 2% contre les 10% attendus !
			-- plus fort : m1 = 0 et m2 = 0.5 : m1 a quand même 12.5% chances d’être choisie ! ...

			-- 2ème version : avec where random() <= fmiss_proba et order by random()
			-- Avec ça, si on a une mission de proba m1 et une de proba m2
			-- la proba réelle de tomber sur m1 est Pm1 = m1 - 1/2 x (m1 x m2) 
			-- chaque enregistrement passe un test ; puis on en choisi une parmi celles qui ont réussi.
			-- inconvénient : il est possible qu’aucune mission ne soit choisie.
			-- mais si une est choisie, les probas intuitives sont mieux respectées.
			select into v_fonction_init, v_nom_mission
				miss_fonction_init, miss_nom
			from missions
			inner join faction_missions on fmiss_miss_cod = miss_cod
			where fmiss_fac_cod = ligne.fac_cod
				and miss_fonction_init IS NOT NULL
				and miss_fonction_init <> ''
				and miss_fonction_valide IS NOT NULL
				and miss_fonction_valide <> ''
				and random() <= fmiss_proba
			order by random()
			limit 1;
			if found then
				v_fonction_init := 'select ' || v_fonction_init || '(' || ligne.fac_cod::text || ', ' || ligne.pos_etage::text || ') as initialise';
				execute v_fonction_init into creation_ok;
				
				if not creation_ok then	-- Si on n’arrive pas à créer la mission, on tente de créer une mission de plus
					nombre_missions := nombre_missions + 1;
				else
					if au_moins_une_mission then
						texte_creation := texte_creation || ', ';
					end if;
					texte_creation := texte_creation || v_nom_mission;
					au_moins_une_mission := true;
				end if;
			end if;
			itemp := itemp + 1;
		end loop;
		if au_moins_une_mission then
			texte_retour := texte_retour || texte_creation || e')';
		end if;
	end loop;
	return texte_retour;
end;$function$

