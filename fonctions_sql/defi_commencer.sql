--
-- Name: defi_commencer(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION defi_commencer(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction defi_commencer                                   */
/*   Commence un défi entre deux protagonistes               */
/*   on passe en paramètres :                                */
/*   $1 = v_defi : le code du defi à commencer               */
/* Sortie de la forme n#message                              */
/*   n == statut : -1 (erreur) ou 0 (avertissement)          */
/*                 ou 1 (ok)                                 */
/*   message == message d’erreur si nécessaire               */
/*************************************************************/
/* Créé le 13/01/2014                                        */
/*************************************************************/
declare
	v_defi alias for $1; -- Le code du défi
	ligne_defi record;   -- Les données du défi
	v_actif char;        -- perso_actif de chaque protagoniste.
	v_etage integer;     -- L’étage où ils se retrouvent.

	v_resultat text;
begin
	-- Par défaut, tout est OK.
	v_resultat := '1#';

	-- Récupération des données du défi
	select into ligne_defi *
	from defi
	inner join defi_zone on zone_cod = defi_zone_cod
	where defi_cod = v_defi and defi_statut = 0;
	if not found then
		return '-1#Défi introuvable, ou déjà commencé.';
	end if;

	-- Vérification des ripostes (légitimes défenses)
	if exists(select 1 from riposte where ligne_defi.defi_cible_cod IN (riposte_attaquant, riposte_cible) AND riposte_nb_tours < 2) then
		return '-1#Vous êtes engagé en combat, et ne pouvez pas vous en soustraire pour participer à un défi.';
	end if;

	-- Vérification du statut Actif de chacun
	-- En cas de problème, l’inactif est considéré comme ayant abandonné le défi.
	select into v_actif perso_actif from perso where perso_cod = ligne_defi.defi_lanceur_cod;
	if v_actif <> 'O' then
		perform defi_abandonner(v_defi, 'L');
		return '-1#Le lanceur du défi n’étant plus actif, vous gagnez par forfait !';
	end if;
	select into v_actif perso_actif from perso where perso_cod = ligne_defi.defi_cible_cod;
	if v_actif <> 'O' then
		perform defi_abandonner(v_defi, 'C');
		return '-1#Vous n’êtes plus actif, ou vous apprêtez à partir en hibernation : vous perdez par forfait !';
	end if;

	-- Sauvegarde des Bonus / Malus.
	delete from defi_bonus where defi_bonus_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);
	insert into defi_bonus (defi_bonus_perso_cod, defi_bonus_nb_tours, defi_bonus_tbonus_libc, defi_bonus_valeur, defi_bonus_croissance)
	select bonus_perso_cod, bonus_nb_tours, bonus_tbonus_libc, bonus_valeur, bonus_croissance
	from bonus where bonus_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Suppression des Bonus / Malus.
	delete from bonus where bonus_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Sauvegarde des Réceptacles
	delete from defi_receptacles where drec_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);
	insert into defi_receptacles (drec_perso_cod, drec_sort_cod, drec_reussite)
	select recsort_perso_cod, recsort_sort_cod, recsort_reussite
	from recsort where recsort_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Suppression des Réceptacles.
	delete from recsort where recsort_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Sauvegarde des caractéristiques.
	delete from defi_caracs where defi_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);
	insert into defi_caracs (defi_perso_cod, defi_perso_pv, defi_perso_dlt, defi_perso_pa, defi_perso_nb_esquive, defi_perso_tangible, defi_perso_nb_tour_intangible)
	select perso_cod, perso_pv, perso_dlt, perso_pa, perso_nb_esquive, perso_tangible, perso_nb_tour_intangible
	from perso where perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Remise à jour des données du perso
	update perso
	set perso_pv = perso_pv_max, perso_dlt = now() + '3 hours'::interval, perso_pa = 0,
		perso_nb_esquive = 0, perso_tangible = 'O', perso_nb_tour_intangible = 0
	where perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Sauvegarde des BM de potions (on stocke la valeur du bonus et le temps restant)
	delete from defi_bmcaracs where dbmc_perso_cod IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);
	insert into defi_bmcaracs (dbmc_perso_cod, dbmc_type_carac, dbmc_duree, dbmc_nb_tour, dbmc_valeur, dbmc_mode)
	select corig_perso_cod, corig_type_carac, corig_dfin - now(), corig_nb_tours,
	case corig_type_carac when 'FOR' then perso_for
	                      when 'CON' then perso_con
	                      when 'INT' then perso_int
	                      when 'DEX' then perso_dex end - corig_carac_valeur_orig, corig_mode
	from carac_orig inner join perso on perso_cod = corig_perso_cod
	where corig_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
	  and corig_mode != 'E';  -- sauf bonus equipement

	-- Suppression des BM de potions (sauf bonus/malus d'équipement)
	update carac_orig set corig_dfin = now(), corig_nb_tours = 0
	where corig_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod) and corig_mode != 'E';
	perform f_remise_caracs (ligne_defi.defi_lanceur_cod);
	perform f_remise_caracs (ligne_defi.defi_cible_cod);

	-- Données de position
	select into v_etage pos_etage from positions where pos_cod = ligne_defi.zone_pos_cod_1;

	-- Si le perso est déjà en arène, on n’écrase pas ses données !
	-- Attaquant
	if not exists (select * from perso_arene where parene_perso_cod = ligne_defi.defi_lanceur_cod) then
		insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
		select ppos_perso_cod, v_etage, ppos_pos_cod, now()
		from perso_position
		where ppos_perso_cod = ligne_defi.defi_lanceur_cod;
	else
		update perso_arene set parene_etage_numero = v_etage
		where parene_perso_cod = ligne_defi.defi_lanceur_cod;
	end if;
	-- Cible
	if not exists (select * from perso_arene where parene_perso_cod = ligne_defi.defi_cible_cod) then
		insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
		select ppos_perso_cod, v_etage, ppos_pos_cod, now()
		from perso_position
		where ppos_perso_cod = ligne_defi.defi_cible_cod;
	else
		update perso_arene set parene_etage_numero = v_etage
		where parene_perso_cod = ligne_defi.defi_cible_cod;
	end if;

	-- On supprime les transactions en cours
	delete from transaction
	where tran_vendeur in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
		or tran_acheteur in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- déplacement des protagonistes
	update perso_position set ppos_pos_cod = ligne_defi.zone_pos_cod_1 where ppos_perso_cod = ligne_defi.defi_lanceur_cod;
	update perso_position set ppos_pos_cod = ligne_defi.zone_pos_cod_2 where ppos_perso_cod = ligne_defi.defi_cible_cod;

	-- suppression des locks de combat
	delete from lock_combat
	where lock_cible in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
		OR lock_attaquant IN (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod);

	-- Les familiers. On les met implalpable.
	update perso set perso_tangible = 'N', perso_nb_tour_intangible = 9999
	where perso_cod in (
		select pfam_familier_cod from perso_familier
		inner join perso fam on fam.perso_cod = pfam_familier_cod
		where pfam_perso_cod in (ligne_defi.defi_lanceur_cod, ligne_defi.defi_cible_cod)
			and fam.perso_actif = 'O' and fam.perso_cod <> getparm_n(111)	-- On exclut Kirga
	);

	-- Et on termine;
	update defi set defi_statut = 1 where defi_cod = v_defi;
	return v_resultat;
end;$_$;


ALTER FUNCTION public.defi_commencer(integer) OWNER TO delain;

--
-- Name: FUNCTION defi_commencer(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION defi_commencer(integer) IS 'Débute le défi. $1 = defi_cod';