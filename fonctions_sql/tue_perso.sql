
--
-- Name: tue_perso(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION tue_perso(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction tue_perso                                        */
/*   accomplit les actions consécutives à la mort d un perso */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod attaquant                                */
/*   $2 = perso_cod cible                                    */
/* on a en sortie une chaine séparée par ;                   */
/*   1 = px gagnés par l attaquant                           */
/*************************************************************/
/* Créé le 22/05/2003                                        */
/* Modif Blade 26/10/2006 : intégration des distinction      */
/*    Dispensaires bon et mauvais au 0                       */
/* Modif Blade 06/11/2009 : suppression des empoisonnement   */
/*************************************************************/
declare
	code_retour text;
	v_attaquant alias for $1;
	v_cible alias for $2;
-- variables de la cible
	pos_cible integer;
	type_cible integer;
	v_or integer;
	chance_chute integer;
	cible_pv_max integer;
	nouveau_pv numeric;
	niveau_cible integer;
	v_race integer;
	v_reputation integer;
	nom_cible perso.perso_nom%type;
	v_objet_identifie perso_objets.perobj_identifie%type;
	pos_temple_cible integer;
	des_temple integer;
	reputation_cible integer;
	kharma_cible integer;
	kharma_attaquant integer;
	v_familier integer;
-- variables attaquant
	niveau_attaquant integer;
	nom_attaquant perso.perso_nom%type;
	px_attaquant numeric;
	niveau_r_attaquant integer;
-- variables de calcul
	ligne_objet record;
	texte_evt text;
	nouveau_x integer;
	nouveau_y integer;
	nouveau_etage integer;
	nouvelle_position integer;
	px_gagnes integer;
	px_perdus integer;
	px_cible numeric;
	niveau_r_cible integer;
	nouveau_px integer;
	nouvelle_reputation integer;
	texte_admin text;
	cible_x integer;
	cible_y integer;
	cible_etage integer;
	nouveau_e integer;
	l_position record;
	variation_kharma integer;
	modif_renommee real;
	temp_renommee numeric;
	renommee_attaquant numeric;
	v_gmon_cod integer;
	v_cree_monstre integer;
	nb_temple_cible integer;
	chance_temple integer;
	etage_ref integer;
	nv_etage integer;
	nb_temple integer;
	v_objet_deposable text;
	v_perso_vampire integer;
	nb_trans integer;	--Variable de calcul pour les transactions
	temp integer;		--Variable de calcul pour les transactions
	texte text;		--Variable pour le texte de l'évènement des transaction
begin
/**************************************************/
/* Etape 1 : on récupère les infos de la cible    */
/**************************************************/
nouvelle_position = -1;
select into pos_cible,type_cible,v_or,cible_pv_max,niveau_cible,px_cible,v_race,v_reputation,nom_cible,reputation_cible,kharma_cible,v_gmon_cod,etage_ref
	ppos_pos_cod,perso_type_perso,perso_po,perso_pv_max,perso_niveau,perso_px,perso_race_cod,perso_kharma,perso_nom,perso_kharma,perso_kharma,perso_gmon_cod,etage_reference
		from perso_position,perso,positions,etage
		where ppos_perso_cod = v_cible
		and perso_cod = v_cible
		and ppos_pos_cod = pos_cod
		and pos_etage = etage_numero;
	select into kharma_attaquant,v_perso_vampire perso_kharma,perso_niveau_vampire from perso
		where perso_cod = v_attaquant;
	select into cible_x, cible_y,cible_etage pos_x,pos_y,pos_etage
		from positions
		where pos_cod = pos_cible;
	select into niveau_attaquant,nom_attaquant,px_attaquant perso_niveau,perso_nom,perso_px
		from perso
		where perso_cod = v_attaquant;
--
-- compteurs
--
	if type_cible = 1 then
		update parametres set parm_valeur = parm_valeur + 1
		where parm_cod = 64;
	end if;
	if type_cible = 2 then
		update parametres set parm_valeur = parm_valeur + 1
		where parm_cod = 65;
	end if;
	if type_cible = 3 then
		update parametres set parm_valeur = parm_valeur + 1
		where parm_cod = 66;
	end if;

/***************************************************/
/* Etape 2 : on fait tomber les objets de la cible */
/***************************************************/
-- 2.1 : la cible est un monstre :
	if type_cible = 2 then
		for ligne_objet in select * from perso_objets where perobj_perso_cod = v_cible loop
			select into v_objet_deposable gobj_deposable
				from objet_generique,objets
				where obj_cod = ligne_objet.perobj_obj_cod
				and obj_gobj_cod = gobj_cod;
			if v_objet_deposable = 'N' then
				delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
				delete from perso_identifie_objet where pio_obj_cod = ligne_objet.perobj_obj_cod;
				delete from objets where obj_cod = ligne_objet.perobj_obj_cod;
			else
				insert into objet_position (pobj_cod,pobj_obj_cod,pobj_pos_cod)
					values (nextval('seq_pobj_cod'),ligne_objet.perobj_obj_cod,pos_cible);
				delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
			end if;
		end loop;
		if v_or != 0 then
			insert into or_position (por_cod,por_pos_cod,por_qte)
				values (nextval('seq_por_cod'),pos_cible,v_or);
		end if; -- fin or
-- 2.1.2 : on rajoute pour les gelées
		if v_gmon_cod = 46 then
			v_cree_monstre := cree_monstre_pos(31,pos_cible);
			v_cree_monstre := cree_monstre_pos(31,pos_cible);
			v_cree_monstre := cree_monstre_pos(31,pos_cible);
			v_cree_monstre := cree_monstre_pos(31,pos_cible);
			v_cree_monstre := cree_monstre_pos(31,pos_cible);
			v_cree_monstre := cree_monstre_pos(31,pos_cible);
		end if;
		if v_gmon_cod = 31 then
			v_cree_monstre := cree_monstre_pos(32,pos_cible);
			v_cree_monstre := cree_monstre_pos(32,pos_cible);
		end if;
		if v_gmon_cod = 32 then
			v_cree_monstre := cree_monstre_pos(33,pos_cible);
			v_cree_monstre := cree_monstre_pos(33,pos_cible);
		end if;
		if v_gmon_cod = 33 then
			v_cree_monstre := cree_monstre_pos(34,pos_cible);
			v_cree_monstre := cree_monstre_pos(34,pos_cible);
		end if;
	end if;
	if type_cible = 3 then
		for ligne_objet in select * from perso_objets where perobj_perso_cod = v_cible loop
			select into v_objet_deposable gobj_deposable
				from objet_generique,objets
				where obj_cod = ligne_objet.perobj_obj_cod
				and obj_gobj_cod = gobj_cod;
			if v_objet_deposable = 'N' then
				delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
				delete from perso_identifie_objet where pio_obj_cod = ligne_objet.perobj_obj_cod;
				delete from objets where obj_cod = ligne_objet.perobj_obj_cod;
			else
				insert into objet_position (pobj_cod,pobj_obj_cod,pobj_pos_cod)
					values (nextval('seq_pobj_cod'),ligne_objet.perobj_obj_cod,pos_cible);
				delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
			end if;
		end loop;
		if v_or != 0 then
			insert into or_position (por_cod,por_pos_cod,por_qte)
				values (nextval('seq_por_cod'),pos_cible,v_or);
		end if; -- fin or
	end if;
-- 2.2 : la cible est un joueur
	if type_cible = 1 then -- end monstre
		for ligne_objet in select * from perso_objets where perobj_perso_cod = v_cible loop
			select into chance_chute,v_objet_deposable tobj_chute,gobj_deposable
				from type_objet,objet_generique,objets
				where ligne_objet.perobj_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod;
			if v_objet_deposable = 'N' then
				delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
				delete from perso_identifie_objet where pio_obj_cod = ligne_objet.perobj_obj_cod;
				delete from objets where obj_cod = ligne_objet.perobj_obj_cod;
			else
				if lancer_des(1,100) <= chance_chute then
					select into v_objet_identifie perobj_identifie from perso_objets
						where perobj_cod = ligne_objet.perobj_cod;
					if v_objet_identifie = 'O' then
						insert into perso_identifie_objet (pio_cod,pio_perso_cod,pio_obj_cod,pio_nb_tours)
							values (nextval('seq_pio_cod'),v_cible,ligne_objet.perobj_obj_cod,getparm_n(22));
					end if;
					insert into objet_position (pobj_cod,pobj_obj_cod,pobj_pos_cod)
						values (nextval('seq_pobj_cod'),ligne_objet.perobj_obj_cod,pos_cible);
					delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
				end if;
			end if;
		end loop;
		if v_or != 0 then
			v_or := lancer_des(1,v_or);
			insert into or_position (por_cod,por_pos_cod,por_qte)
				values (nextval('seq_por_cod'),pos_cible,v_or);
			update perso set perso_po = perso_po - v_or where perso_cod = v_cible;
		end if; -- fin or
	end if; -- end type perso
-- etape 6.2 : la cible est tuée, on la replace
	if type_cible = 1 then
		select into pos_temple_cible,nb_temple_cible ptemple_pos_cod,ptemple_nombre
			from perso_temple
			where ptemple_perso_cod = v_cible;
		if found then
			chance_temple := 100 - (getparm_n(32) * nb_temple_cible);
			if chance_temple <= 0 then
				delete from perso_temple where ptemple_perso_cod = v_cible;
			end if;
			des_temple := lancer_des(1,99);
			if des_temple < chance_temple then
				nouvelle_position := pos_temple_cible;
				update perso_temple set ptemple_nombre = ptemple_nombre + 1
					where ptemple_perso_cod = v_cible;
			else
				nouvelle_position := -1;
			end if;
		end if;
		if nouvelle_position = -1 then
			-- on regarde sur quel etage on replace
			des_temple := lancer_des(1,100);
			if des_temple <= 80 then
				nv_etage = etage_ref + 1;
			else
				nv_etage = etage_ref + 2;
			end if;
			if nv_etage > 0 then
				nv_etage := 0;
			end if;
/*Modif Blade pour distinguer dispensaire bon et mauvais au 0 afin de pouvoir délimiter et gérer les zones de droits*/
			if nv_etage = 0 and v_reputation < 0 then
					select into nb_temple count(lieu_cod)
						from lieu,lieu_position,positions
						where pos_etage = nv_etage
						and lpos_pos_cod = pos_cod
						and lpos_lieu_cod = lieu_cod
						and lieu_tlieu_cod = 2
						and lieu_alignement < 0;
					des_temple := lancer_des(1,nb_temple);
					des_temple := des_temple - 1;
					select into nouvelle_position lpos_pos_cod
						from lieu,lieu_position,positions
						where pos_etage = nv_etage
						and lpos_pos_cod = pos_cod
						and lpos_lieu_cod = lieu_cod
						and lieu_tlieu_cod = 2
						offset des_temple
						limit 1;
			else
			select into nb_temple count(lieu_cod)
				from lieu,lieu_position,positions
				where pos_etage = nv_etage
				and lpos_pos_cod = pos_cod
				and lpos_lieu_cod = lieu_cod
				and lieu_tlieu_cod = 2;
			des_temple := lancer_des(1,nb_temple);
			des_temple := des_temple - 1;
			select into nouvelle_position lpos_pos_cod
				from lieu,lieu_position,positions
				where pos_etage = nv_etage
				and lpos_pos_cod = pos_cod
				and lpos_lieu_cod = lieu_cod
				and lieu_tlieu_cod = 2
				offset des_temple
				limit 1;
			end if;
		end if;	-- pos_cible not null

		update perso_position
			set ppos_pos_cod = nouvelle_position
			where ppos_perso_cod = v_cible;
		select into v_familier
			pfam_familier_cod
			from perso_familier
			where pfam_perso_cod = v_cible;
		if found then
			update perso set perso_type_perso = 2 where perso_cod = v_familier;
			delete from perso_familier where pfam_perso_cod = v_cible;
		end if;

	else
		update perso
			set perso_actif = 'N'
			where perso_cod = v_cible;
	end if;
-- etape 6.3 : on le remet au tiers de ses PV
	nouveau_pv = cible_pv_max / 3;
	update perso
		set perso_pv = round(nouveau_pv),perso_tangible = 'N', perso_nb_tour_intangible = 4
		where perso_cod = v_cible;
-- etape 6.3 bis : on enlève les locks
	delete from lock_combat where lock_attaquant = v_cible;
	delete from lock_combat where lock_cible = v_cible;
-- etape 6.4 : on balance le code retour

	/* calcul des px gagnés */
	niveau_r_attaquant := niveau_relatif(px_attaquant);
	niveau_r_cible := niveau_relatif(px_cible);
	if niveau_r_attaquant > niveau_attaquant then
		niveau_attaquant := niveau_r_attaquant;
	end if;
	if niveau_r_cible > niveau_cible then
		niveau_cible := niveau_r_cible;
	end if;

	if v_attaquant = v_cible then
		niveau_cible := niveau_attaquant + lancer_des(1,5) - 10;
	end if;
	px_gagnes := 10 + 2*(niveau_cible - niveau_attaquant) + niveau_cible;
	if px_gagnes <= 0 then
		px_gagnes := 0;
	end if;
	/* evts pour mort */
	if v_attaquant = v_cible then
		texte_evt := '[attaquant] est mort en position x='||trim(to_char(cible_x,'999'))||', y='||trim(to_char(cible_y,'999'))||', etage='||trim(to_char(cible_etage,'999'))||', gagnant '||trim(to_char(px_gagnes,'999999'))||' PX.';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(nextval('seq_levt_cod'),10,now(),1,v_attaquant,texte_evt,'O','O',v_attaquant,v_cible);
	else
		texte_evt := '[attaquant] a tué [cible] en position x='||trim(to_char(cible_x,'999'))||', y='||trim(to_char(cible_y,'999'))||', etage='||trim(to_char(cible_etage,'999'))||', gagnant '||trim(to_char(px_gagnes,'999999'))||' PX.';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(nextval('seq_levt_cod'),10,now(),1,v_attaquant,texte_evt,'O','O',v_attaquant,v_cible);
		texte_evt := '[attaquant] a tué [cible] en position x='||trim(to_char(cible_x,'999'))||', y='||trim(to_char(cible_y,'999'))||', etage='||trim(to_char(cible_etage,'999'))||'.';
		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
			values(nextval('seq_levt_cod'),10,now(),1,v_cible,texte_evt,'N','O',v_attaquant,v_cible);
	end if;

	/* evt pour nouvelle position */
	select into nouveau_x,nouveau_y,nouveau_etage pos_x,pos_y,pos_etage
		from positions
		where pos_cod = nouvelle_position;
	texte_evt := '[cible] réapparait au niveau '||trim(to_char(nouveau_etage,'999999'))||', en position '||trim(to_char(nouveau_x,'9999'))||','||trim(to_char(nouveau_y,'9999'))||'.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),10,now(),1,v_cible,texte_evt,'N','N',v_attaquant,v_cible);
	if v_attaquant != v_cible then
		update perso
			set perso_px = perso_px + px_gagnes
			where perso_cod = v_attaquant;
		-- calcul pour la renommée
		select into renommee_attaquant perso_renommee from perso
			where perso_cod = v_attaquant;
		modif_renommee := niveau_cible;
		modif_renommee := modif_renommee/niveau_attaquant;
		if modif_renommee > 2 then
			modif_renommee := 2;
		end if;
		temp_renommee := renommee_attaquant + modif_renommee;
		update perso set perso_renommee = temp_renommee where perso_cod = v_attaquant;
		-- calcul pour le kharma
		variation_kharma := 0;
		if type_cible = 1 then
			if is_riposte(v_cible,v_attaquant) != 0 then
				variation_kharma := -3;
				if kharma_cible < 0 then
					if kharma_cible < kharma_attaquant then
						variation_kharma := variation_kharma + 4;
					end if;
				end if;
			end if;
		end if;
		if type_cible = 2 then
			variation_kharma := variation_kharma +1;
		end if;
		update perso set perso_kharma = perso_kharma + variation_kharma
			where perso_cod = v_attaquant;
	end if;
	px_perdus := round(px_gagnes/3);
	nouveau_px := px_cible - px_perdus;
	if nouveau_px < 0 then
		nouveau_px := 0;
	end if;
	update perso
		set perso_px = nouveau_px
		where perso_cod = v_cible;
	texte_evt := '[cible] a perdu '||trim(to_char(px_perdus,'9999999'))||' px.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),10,now(),1,v_cible,texte_evt,'N','N',v_attaquant,v_cible);
	update perso set perso_nb_mort = perso_nb_mort + 1 where perso_cod = v_cible;
	if v_cible != v_attaquant then
		if type_cible = 1 then
			update perso set perso_nb_joueur_tue = perso_nb_joueur_tue + 1 where perso_cod = v_attaquant;
		else
			update perso set perso_nb_monstre_tue = perso_nb_monstre_tue + 1 where perso_cod = v_attaquant;
		end if;
	end if;
	code_retour := trim(to_char(px_gagnes,'99999'))||';'; -- pos 1
	/*On supprime les empoisonnements si il y en a */
	delete from bonus where bonus_perso_cod = v_cible and bonus_mode != 'E' and bonus_tbonus_libc = 'POI';
	/* reputation */
	select into nouveau_x, nouveau_y,nouveau_e pos_x,pos_y,pos_etage from positions
		where pos_cod = nouvelle_position;
	delete from riposte where riposte_attaquant = v_cible;
	delete from concentrations where concentration_perso_cod = v_cible;
	delete from riposte where riposte_cible = v_cible;
	if v_perso_vampire != 0 then
		select into kharma_attaquant perso_kharma from perso
			where perso_cod = v_attaquant;
		if kharma_attaquant > 0 then
			update perso set perso_kharma = 0 where perso_cod = v_attaquant;
		end if;
	end if;
/* Suppression des transaction en cours, pour le perso ou son familier*/
	delete from transaction
		where tran_vendeur = v_familier;
	get diagnostics temp = row_count;
	delete from transaction
		where tran_vendeur = v_cible;
	get diagnostics nb_trans = row_count;
	if (nb_trans+temp) != 0 then
		texte := 'Les transactions en cours en tant que vendeur ont été annulées y compris pour votre familier !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,v_cible,texte,'O','N');
	end if;
	delete from transaction
		where tran_acheteur = v_familier;
	get diagnostics temp = row_count;
	delete from transaction
		where tran_acheteur = v_cible;
	get diagnostics nb_trans = row_count;
	if (nb_trans+temp) != 0 then
		texte := 'Les transactions en cours en tant qu''acheteur ont été annulées, y compris pour votre familier !';
		insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values (nextval('seq_levt_cod'),17,'now()',1,v_cible,texte,'O','N');
	end if;


	/* actions */
	/*delete from action where act_tact_cod in (1,2)
		and act_perso2 = v_cible;*/
	return code_retour;
end;$_$;


ALTER FUNCTION public.tue_perso(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION tue_perso(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION tue_perso(integer, integer) IS 'ATTENTION : vieille fonction plus utilisée NORMALEMENT !';
