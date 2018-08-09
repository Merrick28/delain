CREATE OR REPLACE FUNCTION public.necromancie(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* Nécromancie                                  */
/*  $1 = tueur                                  */
/*  $2 = cible                                  */
/************************************************/
declare
	code_retour text;
	v_attaquant alias for $1;
	v_cible alias for $2;
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
begin
	select into v_pos
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = v_cible;
	v_des := lancer_des(1,100);
	if v_des < 20 then
		v_monstre := cree_monstre_pos(19,v_pos);
	elsif v_des < 50 then
		v_monstre := cree_monstre_pos(207,v_pos);
	elsif v_des < 60 then
		v_monstre := cree_monstre_pos(210,v_pos);
	elsif v_des < 70 then
		v_monstre := cree_monstre_pos(28,v_pos);
	elsif v_des < 73 then
		v_monstre := cree_monstre_pos(170,v_pos);
	elsif v_des < 76 then
		v_monstre := cree_monstre_pos(175,v_pos);
	elsif v_des < 80 then
		v_monstre := cree_monstre_pos(181,v_pos);	
	elsif v_des < 85 then
		v_monstre := cree_monstre_pos(253,v_pos);	
	else
		-- on créé un clone !
		v_num_perso := nextval('seq_perso');
		-- les objets
		for ligne in select * from perso_objets
			where perobj_perso_cod = v_cible
			and perobj_equipe = 'O' loop
			select into chance_chute,v_objet_deposable,v_type_objet tobj_chute,gobj_deposable,gobj_tobj_cod
				from type_objet,objet_generique,objets
				where ligne.perobj_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod;
			if v_objet_deposable = 'O' then	
				if lancer_des(1,100) <= chance_chute then
					select into v_objet_identifie perobj_identifie from perso_objets
						where perobj_cod = ligne.perobj_cod;
					if v_objet_identifie = 'O' then
						insert into perso_identifie_objet (pio_cod,pio_perso_cod,pio_obj_cod,pio_nb_tours)
							values (nextval('seq_pio_cod'),v_cible,ligne.perobj_obj_cod,getparm_n(22));		
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
			end if;
		end loop;
		-- les caracs de base
		insert into perso (	perso_cod,
									perso_nom,
									perso_for,
									perso_dex,
									perso_int,
									perso_con,
									perso_race_cod,
									perso_pv,
									perso_pv_max,
									perso_dcreat,
									perso_actif,
									perso_des_regen,
									perso_valeur_regen,
									perso_vue,
									perso_niveau,
									perso_type_perso,
									perso_amelioration_vue,
 									perso_amelioration_regen,
  									perso_amelioration_degats,
  									perso_amelioration_armure,
  									perso_nb_des_degats,
  									perso_val_des_degats,
  									perso_enc_max,
  									perso_amel_deg_dex,
  									perso_tangible,
  									perso_capa_repar,
  									perso_nb_amel_repar,
  									perso_amelioration_nb_sort,
  									perso_px,
  									perso_dlt,
  									perso_temps_tour)
			select 	v_num_perso,
						'Squelette de '||perso_nom,
						perso_for,
						perso_dex,
						perso_int,
						perso_con,
						17,
						round(perso_pv_max*0.5),
						perso_pv_max,
						now(),
						'O',
						perso_des_regen,
						perso_valeur_regen,
						perso_vue,
						perso_niveau,
						2,
						perso_amelioration_vue,
 						perso_amelioration_regen,
  						perso_amelioration_degats,
  						perso_amelioration_armure,
  						perso_nb_des_degats,
  						perso_val_des_degats,
  						perso_enc_max,
  						perso_amel_deg_dex,
  						'O',
  						perso_capa_repar,
  						perso_nb_amel_repar,
  						perso_amelioration_nb_sort,
  						perso_px,
  						perso_dlt,
  						perso_temps_tour
  			from perso where perso_cod = v_cible;
		-- competences
		insert into perso_competences
			(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur)
			select
			v_num_perso,pcomp_pcomp_cod,pcomp_modificateur			
			from perso_competences
			where pcomp_perso_cod = v_cible;
		-- sorts
		insert into perso_sorts
			(psort_perso_cod,psort_sort_cod)
			select 
			v_num_perso,psort_sort_cod
			from perso_sorts
			where psort_perso_cod = v_cible;
		code_retour := trim(to_char(v_num_perso,'999999999'));				
	--end if;
	end if;
	texte_evt := '[perso_cod1] a invoqué un mort vivant.';
	
	insert into ligne_evt (levt_tevt_cod,levt_texte,levt_perso_cod1,levt_lu,levt_visible)
		values
		(53,texte_evt,v_attaquant,'O','O');
	
	return code_retour;
		
end;$function$

