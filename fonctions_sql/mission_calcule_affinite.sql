CREATE OR REPLACE FUNCTION public.mission_calcule_affinite(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction mission_calcule_affinite                         */
/*   Calcule la non-affinité d’un perso à une faction        */
/*   Paramètres :                                            */
/*     $1 = Perso_cod                                        */
/*     $2 = Fac_cod                                          */
/*   Sortie : i;fac_nom                                      */
/*     i       = score                                       */
/*     fac_nom = nom de la faction antagoniste principale    */
/*************************************************************/
/* Créé le 19/02/2013                                        */
/*************************************************************/
declare
	v_perso alias for $1;       -- Perso_cod
	v_faction alias for $2;     -- Faction_cod
	texte_retour text;

	ligne record;               -- Infos sur les factions avec lesquelles le perso a des relations
	score int;                  -- Le score de rejet des fréquentations du perso pour la faction
	score_max int;              -- Le score de la faction qui justifie le plus ce rejet
	nb_factions int;            -- Nombre de factions antagonistes pour lesquelles le perso travaille
	faction_score_max text;     -- Le nom de la faction qui justifie le plus ce rejet

begin
	texte_retour := '';
	score := 0;
	score_max := 0;
	nb_factions := 0;
	faction_score_max := '';

	for ligne in
		select (6 - f2f_note_estime) * pfac_rang_numero as score, f2f_objet_cod, fac_nom
		from faction_relation_faction
		inner join faction_perso on pfac_fac_cod = f2f_objet_cod
		inner join factions on fac_cod = f2f_objet_cod
		where pfac_perso_cod = v_perso
			and f2f_sujet_cod = v_faction
			and f2f_note_estime <= 5
			and pfac_statut = 0
	loop
		score := score + ligne.score;
		nb_factions := nb_factions + 1;
		if ligne.score > score_max then
			score_max := ligne.score;
			faction_score_max := ligne.fac_nom;
		end if;
	end loop;
	texte_retour := score::text || ';' || faction_score_max;
	if nb_factions > 1 then
		texte_retour := texte_retour || ' et d’autres';
	end if;

	return texte_retour;
end;$function$

