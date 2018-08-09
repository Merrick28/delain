CREATE OR REPLACE FUNCTION public.defi_reinitialise_persos(integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction defi_reinitialise_persos                         */
/*   Remise des personnages dans leur état initial           */
/*   on passe en paramètres :                                */
/*   $1 = v_defi : le numéro de défi                         */
/*************************************************************/
/* Créé le 21/01/2014                                        */
/*************************************************************/
declare
	v_defi alias for $1;   -- Le code du défi rejeté
	ligne_defi record;     -- Les données du défi
	ligne_bmcaracs record; -- Les données de bonus / malus sur caractéristiques primaires (potions)

begin
	-- Récupération des données
	select into ligne_defi * from defi where defi_cod = v_defi;

	-----------------
	-- BONUS MALUS --
	-----------------
	-- Suppression des Bonus / Malus dûs au défi.
	delete from bonus where bonus_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Restauration des Bonus / Malus antérieurs
	insert into bonus (bonus_perso_cod, bonus_nb_tours, bonus_tbonus_libc, bonus_valeur, bonus_croissance)
	select defi_bonus_perso_cod, defi_bonus_nb_tours, defi_bonus_tbonus_libc, defi_bonus_valeur, defi_bonus_croissance
	from defi_bonus where defi_bonus_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Nettoyage des données sauvegardées
	delete from defi_bonus where defi_bonus_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-----------------
	-- RÉCEPTACLES --
	-----------------
	-- Suppression réceptacles du défi.
	delete from recsort where recsort_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);
	
	-- Restauration des Réceptacles
	insert into recsort (recsort_perso_cod, recsort_sort_cod, recsort_reussite)
	select drec_perso_cod, drec_sort_cod, drec_reussite
	from defi_receptacles where drec_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Nettoyage des données sauvegardées
	delete from defi_receptacles where drec_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	----------------------
	-- CARACTÉRISTIQUES --
	----------------------
	-- Restauration des caractéristiques.
	update perso set
		perso_pv = defi_perso_pv,
		perso_dlt = now(),
		perso_pa = defi_perso_pa,
		perso_nb_esquive = defi_perso_nb_esquive,
		perso_tangible = 'N',
		perso_nb_tour_intangible = max (coalesce(defi_perso_nb_tour_intangible, 0), 2)
	from defi_caracs
	where defi_perso_cod = perso_cod
		and perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Suppression des caractéristiques sauvegardées
	delete from defi_caracs where defi_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	---------------------------
	-- BONUS MALUS PRIMAIRES --
	---------------------------
	-- Suppression des éventuelles potions bues pendant le défi
	update carac_orig set corig_dfin = now(), corig_nb_tours = 0
	where corig_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);
	perform f_remise_caracs (ligne_defi.defi_lanceur_cod);
	perform f_remise_caracs (ligne_defi.defi_cible_cod);

	-- Restauration des bonus antérieurs
	for ligne_bmcaracs in
		select * from defi_bmcaracs
		where dbmc_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
	loop
		perform f_modif_carac (ligne_bmcaracs.dbmc_perso_cod,
			ligne_bmcaracs.dbmc_type_carac,
			floor(extract(epoch from ligne_bmcaracs.dbmc_duree) / 3600)::integer,  -- Heures dans un interval...
			ligne_bmcaracs.dbmc_valeur);
	end loop;

	-- Suppression des BM de potions sauvegardés
	delete from defi_bmcaracs where dbmc_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-----------------------
	-- SORTIR DE L’ARÈNE --
	-----------------------
	-- On ramène le perso à son emplacement d’origine
	update perso_position set ppos_pos_cod = parene_pos_cod
	from perso_arene where parene_perso_cod = ppos_perso_cod
		and parene_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- On nettoie la table des arènes
	delete from perso_arene where parene_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- On supprime les locks de combat éventuels
	delete from lock_combat
	where lock_cible in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
		OR lock_attaquant IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- On réduit la durée d’impalpabilité des familiers à 2 tours
	update perso set perso_tangible = 'N', perso_nb_tour_intangible = 2
	where perso_cod in (
		select pfam_familier_cod from perso_familier
		inner join perso fam on fam.perso_cod = pfam_familier_cod
		where pfam_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
			and fam.perso_actif = 'O' and fam.perso_cod <> getparm_n(111)	-- On exclut Kirga
	);

	-- On supprime les transactions en cours
	delete from transaction
	where tran_vendeur in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod) 
		or tran_acheteur in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- On supprime les légitimes défenses entre les protagonistes
	delete from riposte
	where riposte_attaquant in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod) 
		and riposte_cible in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- On supprime les actions entre les protagonistes
	delete from action
	where act_perso1 in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod) 
		and act_perso2 in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Et je crois que c’est bon...
end;$function$

