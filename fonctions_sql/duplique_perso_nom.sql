CREATE OR REPLACE FUNCTION public.duplique_perso_nom(integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Clonage		                                  */
/*  $1 = perso_cloné                            */
/*  $2 = Nouveau nom du perso_cloné             */
/************************************************/
declare
	code_retour text;
	v_attaquant alias for $1;
	nv_nom_perso alias for $2;
	nv_nom_perso2 text;
	nv_perso_nom_lower text;
	v_des integer;
	v_pos integer;
	v_monstre integer;
	texte_evt text;
	ligne record;
	chance_chute integer;
	v_objet_deposable text;
	v_type_objet integer;
  v_objet_identifie text;
	v_num_perso integer;
	tmp numeric;
	v_compte integer;
	temp integer;
	temp1 integer;
	temp2 integer;
	temp3 integer;
	temp4 integer;
	temp5 integer;
begin
	select into v_pos
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = v_attaquant;
		-- on créé un clone !
		v_num_perso := nextval('seq_perso');
		-- les objets
		for ligne in select * from perso_objets
			where perobj_perso_cod = v_attaquant
			and perobj_equipe = 'O' loop
			select into chance_chute,v_objet_deposable,v_type_objet tobj_chute,gobj_deposable,gobj_tobj_cod
				from type_objet,objet_generique,objets
				where ligne.perobj_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod;
			if v_objet_deposable = 'O' then	
					select into v_objet_identifie perobj_identifie from perso_objets
						where perobj_cod = ligne.perobj_cod;
					if v_objet_identifie = 'O' then
						insert into perso_identifie_objet (pio_cod,pio_perso_cod,pio_obj_cod,pio_nb_tours)
							values (nextval('seq_pio_cod'),v_attaquant,ligne.perobj_obj_cod,getparm_n(22));		
					end if;
					update perso_objets
						set perobj_equipe = 'N'
						where perobj_perso_cod = v_attaquant
						and perobj_obj_cod in
							(select obj_cod
								from objets,objet_generique
								where obj_gobj_cod = gobj_cod
								and gobj_tobj_cod = v_type_objet);
					update perso_objets
						set perobj_perso_cod = v_attaquant
						where perobj_cod = ligne.perobj_cod;
			end if;
		end loop;
		-- les caracs de base
		--On vérifie le nom
			nv_perso_nom_lower := lower(nv_nom_perso);
			nv_nom_perso2 := nv_nom_perso;
		-- les caracs de base
		insert into perso (	perso_cod,
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
												perso_quete
												)
								select 	v_num_perso,
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
												'Copie de '||perso_nom,
												perso_gmon_cod,
												perso_renommee,
												perso_dirige_admin,
												'copie de '||perso_lower_perso_nom,
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
												perso_quete
  			from perso where perso_cod = v_attaquant;
		-- competences
		update perso set perso_nom = nv_nom_perso2,perso_lower_perso_nom = nv_perso_nom_lower where perso_cod = v_num_perso;
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			select
			v_num_perso,pcomp_pcomp_cod,pcomp_modificateur			
			from perso_competences
			where pcomp_perso_cod = v_attaquant;
		-- sorts
		insert into perso_sorts
			(psort_perso_cod,psort_sort_cod)
			select 
			v_num_perso,psort_sort_cod
			from perso_sorts
			where psort_perso_cod = v_attaquant;
			-- compte -- Bleda 5/4/2011 Supprimé. La fonction n''est pas appelée ailleurs que pour les avatars, où le lien avec le compte est supprimé. De plus, cet insert échoue dans le cas d'un familier.
--		select into v_compte pcompt_compt_cod from perso_compte where pcompt_perso_cod = v_attaquant;
--			insert into perso_compte (pcompt_compt_cod,pcompt_perso_cod)
--			values (v_compte,v_num_perso);
			insert into perso_position (ppos_pos_cod,ppos_perso_cod) values (v_pos,v_num_perso);
			--temple
			/*Mise en commentaire, ça ne marche pas pour une obscure raison
			select into temp,temp1,temp2,temp3,temp4,temp5
				ptemple_perso_cod,ptemple_cod,ptemple_pos_cod,ptemple_nombre,ptemple_anc_pos_cod,ptemple_anc_nombre 
				from perso_temple 
				where ptemple_perso_cod = v_attaquant;
			if found then
					insert into perso_temple (ptemple_perso_cod,ptemple_cod,ptemple_pos_cod,ptemple_nombre,ptemple_anc_pos_cod,ptemple_anc_nombre)
					values (v_num_perso,temp1,temp2,temp3,temp4,temp5);
			end if;*/
		code_retour := trim(to_char(v_num_perso,'999999999'));				

	return code_retour;
		
end;$function$

