--
-- Name: f_enchantement(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_enchantement(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/******************************************/
/* fonction f_enchantement                */
/* paramètres :                           */
/*  $1 = perso_cod                        */
/*  $2 = obj_cod                          */
/*  $3 = enc_cod                          */
/*  $4 = type enchantement                */
/*    0 = depuis un lieu                  */
/*    1 = depuis un perso                 */
/*    2 = depuis un PNJ		          */
/*    3 = depuis un script admin          */
/* Enchante l’objet obj_cod avec enc_cod  */
/******************************************/
/* Créé le 04/05/2006 par Merrick         */
/* Modif le 14/12/2009 par Blade          */
/* Modif le 16/05/2012 par Reivax         */
/******************************************/
declare
	code_retour text;
	personnage alias for $1;
	v_obj_cod alias for $2;
	v_enc_cod alias for $3;
	v_type_enc alias for $4;
	v_obj_enchantable integer;
	v_chance_enchant integer;
	v_pa integer;
	v_nb_objet integer;
	ligne record;
	temp_del integer;
	sortie integer;
	v_cpt integer;
	texte_evt text;
	v_po integer;
	v_cout integer;
	v_cout_pa integer;
	v_comp integer;
	temp integer;
	nom_comp text;
	v_des integer;
	px_gagne numeric;
	v_reussite integer;
	temp_ameliore_competence text;
	nombre integer;
	v_comp_cod integer;
	v_energie integer;
	v_magie integer;
	v_pos_cod integer;
	gain_renommee numeric;		-- gain (ou perte) de renommée artisanale
	--
	-- variables relatives à l’enchantement
	--
	v_enc_nom text;
	v_enc_degat integer;
	v_enc_armure integer;
	v_enc_portee integer;
	v_enc_chute numeric;
	v_enc_usure numeric;
	v_enc_vampirisme numeric;
	v_enc_regen integer;
	v_enc_aura_feu numeric;
	v_enc_critique integer;
	v_enc_seuil_force integer;
	v_enc_seuil_dex integer;
	v_enc_vue integer;
	v_enc_chance_drop numeric;
	v_enc_poids numeric;
begin
	select into v_cout, v_cout_pa enc_cout, enc_cout_pa from enchantements
		where enc_cod = v_enc_cod;

	if v_type_enc <> 3 then	-- Premier cas, les enchantement depuis le jeu
		--
		-- par défaut, on considère qu’on ne va pas y arriver
		--
		v_reussite := 0;
		code_retour := '';
		gain_renommee := 5;
		-------------------------
		-- DEBUT DES CONTROLES --
		-------------------------
		-- on regarde que l’objet soit bien dans l’inventaire
		select into v_obj_enchantable, v_chance_enchant
			obj_enchantable, gobj_chance_enchant
			from objets,perso_objets,objet_generique
			where perobj_perso_cod = personnage
			and perobj_identifie = 'O'
			and gobj_cod = obj_gobj_cod
			and perobj_obj_cod = obj_cod
			and obj_cod = v_obj_cod;
		if not found then
			code_retour := 'Erreur, l’objet demandé n’est pas dans l’inventaire ou n’est pas identifié !';
			return code_retour;
		end if;
		-- on regarde qu’il soit bien enchantable
		if v_obj_enchantable = 0 or v_chance_enchant = 0 then
			code_retour := 'Erreur, l’objet demandé n’est pas enchantable !';
			return code_retour;
		end if;
		-- on vérifie les PA
		select into v_pa,v_energie perso_pa,perso_energie
			from perso
			where perso_cod = personnage;
		if not found then
			code_retour := 'Erreur, personnage non trouvé !';
			return code_retour;
		end if;
		if v_pa < v_cout_pa then
			code_retour := 'Erreur, pas assez de PA pour effectuer cette action !';
			return code_retour;
		end if;
		-- on vérifie que le perso ait bien tous les objets demandés
		v_nb_objet := obj_enchantement(personnage,v_enc_cod,v_obj_cod);
		if v_nb_objet != 1 then
			code_retour := 'Erreur, le personnage ne possède pas tous les objets nécessaires à cet enchantement !';
			return code_retour;
		end if;
		-- on vérifie les brouzoufs lorsqu’on fait appel à un enchanteur ou un batiment

		select into v_po perso_po from perso
			where perso_cod = personnage;
		if v_po < v_cout and v_type_enc != 1 then
			code_retour := 'Erreur, le personnage ne possède pas assez de brouzoufs !';
			return code_retour;
		end if;

		--
		-- on vérifie le type d’enchantement
		--
		if v_type_enc = 0 then
			--
			-- on force la réussite
			--
			v_reussite := 1;
			select into v_comp
				ppos_pos_cod
				from perso_position,lieu_position,lieu
				where ppos_perso_cod = personnage
				and ppos_pos_cod = lpos_pos_cod
				and lpos_lieu_cod = lieu_cod
				and lieu_tlieu_cod = 26;
			if not found then
				code_retour := 'Erreur ! L’enchantement n’est pas effectué depuis une forgeamage !';
				return code_retour;
			end if;
		elsif v_type_enc = 1 then -- Cas d’un perso qui tente un enchantement
			select into v_comp,nom_comp,v_comp_cod
				pcomp_modificateur,comp_libelle,pcomp_pcomp_cod
				from perso_competences,competences
				where pcomp_perso_cod = personnage
				and pcomp_pcomp_cod in (88,102,103)
				and pcomp_pcomp_cod = comp_cod;
			if not found then
				code_retour := 'Erreur ! Le personnage ne connait pas la compétence forgeamage !';
				return code_retour;
			end if;
			select into v_magie,v_pos_cod pos_magie,pos_cod from positions,perso_position where ppos_perso_cod = personnage and ppos_pos_cod = pos_cod; --On vérifie que la position a suffisamment de puissance magique
			if v_magie < 1000 then
				code_retour := 'Cet endroit n’est vraiment pas propice pour s’adonner au forgeamage ! Trouvez un autre endroit qui bénéficiera de meilleur vents magiques';
				return code_retour;
			end if;
			if v_energie < ((v_cout_pa * 20) / 3) then
				code_retour := 'Vous ne possédez pas suffisamment d’énergie pour procéder à l’enchantement de cet objet avec ces composants.';
				return code_retour;
			end if;

		elsif v_type_enc = 2 then /*On vérifie qu’un enchanteur PNJ se trouve sur la même case*/
			v_reussite := 1; /*On force la réussite car un enchanteur PNJ ne rate jamais */
			select into nombre count(perso_cod) from perso,perso_position
						where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = personnage)
						and perso_quete = 'enchanteur.php'
						and perso_cod = ppos_perso_cod;
			if not found then
				code_retour := 'Erreur ! Aucun PNJ enchanteur ne se trouve à proximité !';
				return code_retour;
			end if;

		end if;
		-------------------------
		--  FIN  DES CONTROLES --
		-------------------------
		--
		-- controles de réussite de lancer en cas de lancer manuel
		--
		if v_type_enc = 1 then
			-- concentrations
			select into temp concentration_perso_cod from concentrations
				where concentration_perso_cod = personnage;
			if found then
				v_comp := v_comp + 20;
				delete from concentrations where concentration_perso_cod = personnage;
			end if;

			v_comp := v_comp - v_cout_pa;

			-- on prépare un retour
			code_retour := '<p>Vous avez utilisé la compétence <b>'||nom_comp||'</b><br>';
			code_retour := code_retour||'Votre chance de réussite en tenant compte des modificateurs est de : <b>'||trim(to_char(v_comp,'99999'))||' %</b><br>';
			v_des := lancer_des(1,100);
			code_retour := code_retour||'Le lancer de dés est de <b>'||trim(to_char(v_des,'9999'))||'</b>, ';
			--
			if v_des > 95 then -- échec critique
				px_gagne := 0;
				gain_renommee := gain_renommee * (-0.4);
				code_retour := code_retour||'il s’agit donc d’un échec critique.<br>';
				update objets set obj_etat = obj_etat * 0.9, obj_etat_max = obj_etat_max * 0.9 where obj_cod = v_obj_cod;
				code_retour := code_retour||'<br>Malheureusement, cet objet est définitivement abimé. C’est le risque lorsque vous ratez un forgeamage !<br>';
			elsif v_des <= 5 then -- réussite critique
				v_reussite := 1;
				px_gagne := 2;
				code_retour := code_retour||'il s’agit donc d’une réussite critique. Vous gagnez 2 PX pour cette action.<br>';
				temp_ameliore_competence := ameliore_competence(personnage,v_comp_cod,v_comp);
				code_retour := code_retour||'Votre jet d’amélioration est de '||split_part(temp_ameliore_competence,';',1)||', ';
				if split_part(temp_ameliore_competence,';',2) = '1' then
					code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
					code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
					px_gagne := px_gagne + 4;
				else
					code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
				end if;
			elsif v_des > 5 and v_des <= v_comp then -- réussite
				v_reussite := 1;
				px_gagne := 1;
				code_retour := code_retour||'vous avez donc réussi. Vous gagnez 1 PX pour cette action.<br>';

				temp_ameliore_competence := ameliore_competence(personnage,v_comp_cod,v_comp);
				code_retour := code_retour||'Votre jet d’amélioration est de '||split_part(temp_ameliore_competence,';',1)||', ';
				if split_part(temp_ameliore_competence,';',2) = '1' then
					code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
					code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
					px_gagne := px_gagne + 4;
				else
					code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
				end if;
			elsif v_des > v_comp then -- échec
				px_gagne := 0;
				gain_renommee := gain_renommee * (-0.2);
				code_retour := code_retour||'vous avez donc <b>échoué</b>.<br>';
				-- réinitialisation de la comp pour amélioration automatique
				if v_comp <= getparm_n(1) then -- amélioration auto
					temp_ameliore_competence := ameliore_competence(personnage,v_comp_cod,v_comp);
					code_retour := code_retour||'Votre jet d’amélioration est de '||split_part(temp_ameliore_competence,';',1)||', ';
					if split_part(temp_ameliore_competence,';',2) = '1' then
						code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
						code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
						px_gagne := px_gagne + 1;
					else
						code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
					end if;
				end if; -- fin amélioration auto
				update objets set obj_etat = obj_etat * 0.9, obj_etat_max = obj_etat_max * 0.9 where obj_cod = v_obj_cod;
				code_retour := code_retour||'<br>Malheureusement, cet objet est définitivement abimé. C’est le risque lorsque vous ratez un forgeamage !<br>';
			end if; -- fin échec

			update perso
			set perso_px = perso_px + px_gagne,
				perso_energie = perso_energie - ((v_cout_pa * 20) / 3),
				perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
			where perso_cod = personnage;

		end if;
		-- on enlève les PA
		update perso
			set perso_pa = perso_pa - v_cout_pa
			where perso_cod = personnage;
		--
		-- en fonction de la réussite, on passe à la suite
		--

		-- on retire tous les objets nécessaires à l’enchantement réussite ou pas !
		for ligne in select * from enc_objets where oenc_enc_cod = v_enc_cod loop
			sortie := 0;
			v_cpt := 0;
			while sortie = 0 loop
				select into temp_del obj_cod
					from objets,perso_objets
					where perobj_perso_cod = personnage
					and perobj_identifie = 'O'
					and perobj_obj_cod = obj_cod
					and obj_cod != v_obj_cod
					and obj_gobj_cod = ligne.oenc_gobj_cod
					limit 1;
				temp_del := f_del_objet(temp_del);
				v_cpt := v_cpt + 1;
				if v_cpt >= ligne.oenc_nombre then
					sortie := 1;
				end if;
			end loop;
		end loop;
		update positions set pos_magie = pos_magie - v_magie where pos_cod = v_pos_cod
			and pos_cod <> 152794;	-- == -6 / -7 dans la Halle Merveilleuse. C’est temporaire, pour le marché de Léno 2013
		-- on procède à l’enchantement proprement dit
		if v_reussite = 0 then
			return code_retour;
		else
			select into v_enc_nom,v_enc_degat,v_enc_armure,v_enc_portee,
				v_enc_chute,v_enc_usure,v_enc_vampirisme,v_enc_regen,v_enc_aura_feu,
				v_enc_critique,v_enc_seuil_force,v_enc_seuil_dex,v_enc_vue,v_enc_chance_drop,v_enc_poids
				enc_nom,enc_degat,enc_armure,enc_portee,
				enc_chute,enc_usure,enc_vampirisme,enc_regen,enc_aura_feu,
				enc_critique,enc_seuil_force,enc_seuil_dex,enc_vue,enc_chance_drop,enc_poids
				from enchantements
				where enc_cod = v_enc_cod;
			update objets
				set obj_nom = obj_nom||' ('||v_enc_nom||')',
				obj_bonus_degats = COALESCE(obj_bonus_degats, 0) + v_enc_degat,
				obj_armure = COALESCE(obj_armure, 0) + v_enc_armure,
				obj_portee = COALESCE(obj_portee, 0) + v_enc_portee,
				obj_chute = COALESCE(obj_chute, 0) + v_enc_chute,
				obj_usure = COALESCE(obj_usure, 0) * v_enc_usure,
				obj_vampire  = COALESCE(obj_vampire, 0) + v_enc_vampirisme,
				obj_regen = COALESCE(obj_regen, 0) + v_enc_regen,
				obj_aura_feu = COALESCE(obj_aura_feu, 0) + v_enc_aura_feu,
				obj_critique = COALESCE(obj_critique, 0) + v_enc_critique,
				obj_seuil_force = COALESCE(obj_seuil_force, 0) + v_enc_seuil_force,
				obj_seuil_dex = COALESCE(obj_seuil_dex, 0) + v_enc_seuil_dex,
				obj_bonus_vue = COALESCE(obj_bonus_vue, 0) + v_enc_vue,
				obj_chance_drop = COALESCE(obj_chance_drop, 0) * v_enc_chance_drop,
				obj_poids = COALESCE(obj_poids, 0) * v_enc_poids,
				obj_enchantable = 2,
				obj_valeur = COALESCE(obj_valeur, 0) + 5 * v_cout
				where obj_cod = v_obj_cod;
			-- a priori on n’a rien oublié, on lance le code retour
			texte_evt := '[perso_cod1] a fait enchanter l’objet n°'||trim(to_char(v_obj_cod,'99999999999999'))||' avec l’enchantement n°'||trim(to_char(v_enc_cod,'99999999999999'));
			insert into ligne_evt (levt_tevt_cod,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
				values (68,personnage,texte_evt,'O','N');

			if v_type_enc = 2 then
				code_retour := code_retour||'<br>L’enchanteur vous regarde d’un air amusé, prend les objets que vous avez amené, et procède à quelques savants rituels en fermant les yeux. <br>Au bout d’un temps ... long ... il rouvre les yeux et sans un mot vous tend l’objet. ';
			else
				code_retour := code_retour||'<br>Le forgeamage s’est bien déroulé.';
			end if;

			return code_retour;
		end if;
	else	-- Cas d’un script d’admin. Aucun contrôle, on exécute directement sur l’objet.
		select into v_enc_nom,v_enc_degat,v_enc_armure,v_enc_portee,
			v_enc_chute,v_enc_usure,v_enc_vampirisme,v_enc_regen,v_enc_aura_feu,
			v_enc_critique,v_enc_seuil_force,v_enc_seuil_dex,v_enc_vue,v_enc_chance_drop,v_enc_poids
			enc_nom,enc_degat,enc_armure,enc_portee,
			enc_chute,enc_usure,enc_vampirisme,enc_regen,enc_aura_feu,
			enc_critique,enc_seuil_force,enc_seuil_dex,enc_vue,enc_chance_drop,enc_poids
			from enchantements
			where enc_cod = v_enc_cod;
		update objets
			set obj_nom = obj_nom||' ('||v_enc_nom||')',
			obj_bonus_degats = COALESCE(obj_bonus_degats, 0) + v_enc_degat,
			obj_armure = COALESCE(obj_armure, 0) + v_enc_armure,
			obj_portee = COALESCE(obj_portee, 0) + v_enc_portee,
			obj_chute = COALESCE(obj_chute, 0) + v_enc_chute,
			obj_usure = COALESCE(obj_usure, 0) * v_enc_usure,
			obj_vampire  = COALESCE(obj_vampire, 0) + v_enc_vampirisme,
			obj_regen = COALESCE(obj_regen, 0) + v_enc_regen,
			obj_aura_feu = COALESCE(obj_aura_feu, 0) + v_enc_aura_feu,
			obj_critique = COALESCE(obj_critique, 0) + v_enc_critique,
			obj_seuil_force = COALESCE(obj_seuil_force, 0) + v_enc_seuil_force,
			obj_seuil_dex = COALESCE(obj_seuil_dex, 0) + v_enc_seuil_dex,
			obj_bonus_vue = COALESCE(obj_bonus_vue, 0) + v_enc_vue,
			obj_chance_drop = COALESCE(obj_chance_drop, 0) * v_enc_chance_drop,
			obj_poids = COALESCE(obj_poids, 0) * v_enc_poids,
			obj_enchantable = 2,
			obj_valeur = COALESCE(obj_valeur, 0) + 5 * v_cout
			where obj_cod = v_obj_cod;
		code_retour := 'Enchantement appliqué à l’objet';
		return code_retour;
	end if;
end;
$_$;


ALTER FUNCTION public.f_enchantement(integer, integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION f_enchantement(integer, integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION f_enchantement(integer, integer, integer, integer) IS 'Applique un enchantement à un objet, soit par la compétence, soit dans un lieu, soit par un PNJ, soit encore par un script admin, suivant le 4e paramètre';
