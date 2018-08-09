CREATE OR REPLACE FUNCTION public.cree_ombre_familier(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function cree_ombre_familier                          */
/* Création d’une ombre du familier démon                */
/* parametres :                                          */
/*  $1 = perso_cod du familier démon modèle              */
/* Sortie :                                              */
/*  v_perso_cod = perso_cod du clone                     */
/*********************************************************/
/**************************************************************/
/*																														*/
/**************************************************************/
declare
	kirga alias for $1;		-- perso_cod du familier démon
	v_num_perso integer;	-- résultat : perso_cod du familier dupliqué
	
begin
	-- on créé un clone !
	v_num_perso := nextval('seq_perso');
	insert into perso (
		perso_cod,
		perso_for,
		perso_dex,
		perso_int,
		perso_con,
		perso_for_init,
		perso_dex_init,
		perso_int_init,
		perso_con_init,
		perso_sex,
		perso_race_cod,
		perso_pv,
		perso_pv_max,
		perso_dlt,
		perso_temps_tour,
		perso_email,
		perso_dcreat,
		perso_validation,
		perso_actif,
		perso_pa,
		perso_der_connex,
		perso_des_regen,
		perso_valeur_regen,
		perso_vue,
		perso_po,
		perso_nb_esquive,
		perso_niveau,
		perso_type_perso,
		perso_amelioration_vue,
		perso_amelioration_regen,
		perso_amelioration_degats,
		perso_amelioration_armure,
		perso_nb_des_degats,
		perso_val_des_degats,
		perso_cible,
		perso_enc_max,
		perso_description,
		perso_nb_mort,
		perso_nb_monstre_tue,
		perso_nb_joueur_tue,
		perso_reputation,
		perso_avatar,
		perso_kharma,
		perso_amel_deg_dex,
		perso_nom,
		perso_gmon_cod,
		perso_renommee,
		perso_dirige_admin,
		perso_lower_perso_nom,
		perso_sta_combat,
		perso_sta_hors_combat,
		perso_utl_pa_rest,
		perso_tangible,
		perso_nb_tour_intangible,
		perso_capa_repar,
		perso_nb_amel_repar,
		perso_amelioration_nb_sort,
		perso_renommee_magie,
		perso_vampirisme,
		perso_niveau_vampire,
		perso_admin_echoppe,
		perso_nb_amel_comp,
		perso_nb_receptacle,
		perso_nb_amel_chance_memo,
		perso_priere,
		perso_dfin,
		perso_px,
		perso_taille,
		perso_admin_echoppe_noir,
		perso_use_repart_auto,
		perso_pnj,
		perso_redispatch,
		perso_nb_redist,
		perso_mcom_cod,
		perso_nb_ch_mcom,
		perso_piq_rap_env,
		perso_ancien_avatar,
		perso_nb_crap,
		perso_nb_embr,
		perso_crapaud,
		perso_dchange_mcom,
		perso_prestige,
		perso_av_mod,
		perso_mail_inactif_envoye,
		perso_test,
		perso_nb_spe,
		perso_compt_pvp,
		perso_dmodif_compt_pvp,
		perso_effets_auto,
		perso_quete,
		perso_tuteur,
		perso_voie_magique,
		perso_energie,
		perso_desc_long,
		perso_nb_mort_arene,
		perso_nb_joueur_tue_arene,
		perso_dfin_tangible
	)
	select
		v_num_perso,
		perso_for,
		perso_dex,
		perso_int,
		perso_con,
		perso_for_init,
		perso_dex_init,
		perso_int_init,
		perso_con_init,
		perso_sex,
		perso_race_cod,
		perso_pv,
		perso_pv_max,
		perso_dlt,
		perso_temps_tour,
		perso_email,
		perso_dcreat,
		perso_validation,
		perso_actif,
		perso_pa,
		perso_der_connex,
		perso_des_regen,
		perso_valeur_regen,
		perso_vue,
		0,
		perso_nb_esquive,
		perso_niveau,
		perso_type_perso,
		perso_amelioration_vue,
		perso_amelioration_regen,
		perso_amelioration_degats,
		perso_amelioration_armure,
		perso_nb_des_degats,
		perso_val_des_degats,
		perso_cible,
		perso_enc_max,
		perso_description,
		perso_nb_mort,
		perso_nb_monstre_tue,
		perso_nb_joueur_tue,
		perso_reputation,
		perso_avatar,
		perso_kharma,
		perso_amel_deg_dex,
		perso_nom,
		perso_gmon_cod,
		perso_renommee,
		perso_dirige_admin,
		perso_lower_perso_nom,
		perso_sta_combat,
		perso_sta_hors_combat,
		perso_utl_pa_rest,
		perso_tangible,
		perso_nb_tour_intangible,
		perso_capa_repar,
		perso_nb_amel_repar,
		perso_amelioration_nb_sort,
		perso_renommee_magie,
		perso_vampirisme,
		perso_niveau_vampire,
		perso_admin_echoppe,
		perso_nb_amel_comp,
		perso_nb_receptacle,
		perso_nb_amel_chance_memo,
		perso_priere,
		perso_dfin,
		perso_px,
		perso_taille,
		perso_admin_echoppe_noir,
		perso_use_repart_auto,
		perso_pnj,
		perso_redispatch,
		perso_nb_redist,
		perso_mcom_cod,
		perso_nb_ch_mcom,
		perso_piq_rap_env,
		perso_ancien_avatar,
		perso_nb_crap,
		perso_nb_embr,
		perso_crapaud,
		perso_dchange_mcom,
		perso_prestige,
		perso_av_mod,
		perso_mail_inactif_envoye,
		perso_test,
		perso_nb_spe,
		perso_compt_pvp,
		perso_dmodif_compt_pvp,
		perso_effets_auto,
		perso_quete,
		perso_tuteur,
		perso_voie_magique,
		perso_energie,
		perso_desc_long,
		perso_nb_mort_arene,
		perso_nb_joueur_tue_arene,
		perso_dfin_tangible
	from perso where perso_cod = kirga;

	-- competences
	insert into perso_competences
		(pcomp_perso_cod, pcomp_pcomp_cod, pcomp_modificateur)
		select
			v_num_perso, pcomp_pcomp_cod, pcomp_modificateur
		from perso_competences
		where pcomp_perso_cod = kirga;

	-- coterie -- on échange les coteries, donc l'image prend la place de Kirga. Kirga, lui, ira dans la coterie du nouveau maitre.
	update groupe_perso set pgroupe_perso_cod = v_num_perso where pgroupe_perso_cod = kirga;

	-- sorts : seul un nombre limité de sorts est autorisé
	insert into perso_sorts
		(psort_perso_cod, psort_sort_cod)
		select 
		v_num_perso, sort_cod
		from (
			(
				select
					sort_cod
				from perso_sorts
				inner join sorts on sort_cod = psort_sort_cod
				where psort_perso_cod = kirga
					and sort_niveau = 2
				order by random()
				limit 3
			)
			UNION ALL
			(
				select
					sort_cod
				from perso_sorts
				inner join sorts on sort_cod = psort_sort_cod
				where psort_perso_cod = kirga
					and sort_niveau = 3
					and random() < 0.3
				order by random()
				limit 2
			)
			UNION ALL
			(
				select
					sort_cod
				from perso_sorts
				inner join sorts on sort_cod = psort_sort_cod
				where psort_perso_cod = kirga
					and sort_niveau = 4
					and random() < 0.2
				order by random()
				limit 1
			)
			UNION ALL
			(
				select
					sort_cod
				from perso_sorts
				inner join sorts on sort_cod = psort_sort_cod
				where psort_perso_cod = kirga
					and sort_niveau = 5
					and random() < 0.1
				order by random()
				limit 1
			)
		) s;

	return v_num_perso;
end;	$function$

