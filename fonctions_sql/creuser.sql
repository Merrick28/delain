CREATE OR REPLACE FUNCTION public.creuser(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function creuser : un perso creuse dans un mur                */
/* On passe en paramètres                                   */
/*    $1 = perso_cod mineur                                      */
/*    $2 = pos_cod position du mur à creuser                     */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 11/02/2006                                  */
/* Modifié le 12/05/2015 par kahlann                   */
/* Modifiction en profondeur des gains du minage :     */
/* Nouvelle répartition des gains, nouveaux gains      */
/*****************************************************************/
declare
--------------------------------------------------------------------------------
-- variables fourre tout
--------------------------------------------------------------------------------
	texte_evt text;	                -- Texte de l'évennement
	code_retour text;				-- chaine qui contiendra le html de retour
	compt integer;					-- compteur mutli usage
	des integer;					-- lancer de des
	nom_competence competences.comp_libelle%type;
	temp_ameliore_competence text;
	tmp_txt text;
	poison integer;			-- Si on a du poison
	ligne record;					-- enregistrements
--------------------------------------------------------------------------------
-- renseignements de l attaquant
--------------------------------------------------------------------------------
	v_mineur alias for $1;	-- perso_cod mineur
	v_pos_mur alias for $2;		-- perso_cod cible
	pa integer; 					-- pa du mineur
	v_force integer; 					-- force du mineur
	pos_mineur integer; 					-- position du mineur
	race_mineur integer; 					-- race du mineur
	v_cout_pa integer;			-- cout en PA de l'action
	comp_creuser  integer;
	comp_creuser_modifie  integer;
	limite_comp_maitre integer; -- limite pour le coup spécial
	v_mur_creusable text;
	v_mur_usure integer;
	v_mur_richesse integer;
	v_mur_texte text;
	num_comp integer;				-- comp_cod utilisée
	res_succes integer;
	res_echec_critique integer;
	res_amelioration integer;
	px_gagne numeric;
	g_obj_cod integer; -- objet obtenu par minage
	obj_nom text;
	v_degats integer;
	v_etage integer;
	v_bonus_creusage integer; --Bonus de creusage de 30% et 7 en dégâts mur
	gain_renommee numeric;		-- gain (ou perte) de renommée artisanale
        nb_objets integer;              -- nombre d'objets utiles gagnés
        v_obj_cod integer;
        v_usure numeric;
begin
	num_comp := 83;
	px_gagne := 0;
	v_cout_pa := 4;
	gain_renommee := 0.1;

	code_retour := '<p>'; -- par défaut, tout est OK
	/***********************************************/
	/* Etape 1 : on vérifie que le mineur existe */
	/***********************************************/
	select into
		pa,
		v_force,
		race_mineur,
		pos_mineur
		perso_pa,
		perso_for,
		perso_race_cod,
		ppos_pos_cod
	from perso,perso_position
	where perso_cod = v_mineur
		and ppos_perso_cod = perso_cod;
	if not found then
		code_retour := '<p>Erreur ! Mineur non trouvé !';
		return code_retour;
	end if;
	/***********************************************/
	/* Etape 2 : on vérifie que le mur existe      */
	/***********************************************/
	select into
		v_mur_creusable,
		v_mur_usure,
		v_mur_richesse
		mur_creusable,
		mur_usure,
		mur_richesse
	from murs
	where mur_pos_cod = v_pos_mur;
	if not found then
		code_retour := '<p>Erreur ! Pas de mur trouvé à cet endroit !';
		return code_retour;
	end if;
	if v_mur_creusable <> 'O' then
		code_retour := '<p>Erreur ! Ce mur ne peut pas être creusé !';
		return code_retour;
	end if;

	/***********************************************/
	/* Etape 3 : on vérifie que le mineur a bien équipé une pioche      */
	/***********************************************/


	/***********************************************/
	/* Etape 4 : on vérifie que le mineur a suffisament de PAs */
	/***********************************************/
	if pa < v_cout_pa then
		code_retour := '<p>Erreur ! Pas assez de PA pour effectuer cette action';
		return code_retour;
	end if;

	-- A partir d'ici on peut creuser.

	/***********************************************/
	/* Etape 5 : on vérifie que le mineur a la competence */
	/***********************************************/
	-- valeur de la competence de base
	select into comp_creuser
		pcomp_modificateur
	from perso_competences
	where pcomp_perso_cod = v_mineur
		and pcomp_pcomp_cod = num_comp;
	if not found then
		code_retour := code_retour|| '<p>Vous n’avez pas encore la compétence requise pour creuser.<br>Celle-ci est donc ajoutée à votre fiche.<br>';
		insert into perso_competences(pcomp_perso_cod,pcomp_pcomp_cod,pcomp_modificateur) values (v_mineur,num_comp,20);
		comp_creuser := 20;
	end if;

	-- Bonus-Malus - 1 - Concentration
	select into compt concentration_perso_cod from concentrations
	where concentration_perso_cod = v_mineur;
	if found then
		comp_creuser_modifie := comp_creuser + 20;
		delete from concentrations where concentration_perso_cod = v_mineur;
		code_retour := code_retour||'Votre concentration augmente vos chances de réussite: 20%<br>';
	else
		comp_creuser_modifie := comp_creuser;
	end if;
	-- Bonus-Malus - 1 - Nains: +20
	if race_mineur = 2 then
		code_retour := code_retour||'Vous êtes un(e) nain(e), les gestes vous viennent naturellement, vos chances de réussite sont augmentées de 20%.<br>';
		comp_creuser_modifie := comp_creuser_modifie + 20;
	end if;

	-- Bonus de creusage
	comp_creuser_modifie := comp_creuser_modifie + valeur_bonus(v_mineur, 'CRE');
	v_force := v_force + round(valeur_bonus(v_mineur, 'CRE')/4);
	-- limite pour un coup spécial
	limite_comp_maitre := round(comp_creuser_modifie*0.25);
	/***********************************************/
	/* Etape 6 : on creuse */
	/***********************************************/
	--  enleve les pa
	update perso
	set perso_pa = pa - v_cout_pa
	where perso_cod = v_mineur;

        -- *******************************************************************************
        -- ajout de l'usure de la pioche
        -- *******************************************************************************
        SELECT into v_obj_cod perobj_obj_cod from perso_objets, objets 
        where perobj_obj_cod = obj_cod and perobj_equipe = 'O' and perobj_perso_cod = v_mineur and obj_gobj_cod in ( 332, 858, 1091, 1101, 1105, 1106, 1107);

        SELECT into v_usure obj_usure FROM objets WHERE obj_cod = v_obj_cod;

        UPDATE objets SET obj_etat = obj_etat - v_usure WHERE obj_cod = v_obj_cod;

        -- *******************************************************************************

	select into nom_competence comp_libelle from competences
	where comp_cod = num_comp;
	code_retour := code_retour||'Vous avez utilisé la compétence <b>'||nom_competence;
	code_retour := code_retour||'</b> (chance de réussite en tenant compte des modificateurs :<b>'||trim(to_char(comp_creuser_modifie,'999'))||'</b> %)<br>';

	-- lancer de dés
	des := lancer_des3(1,100, cast (valeur_bonus(v_mineur, 'BEN') + valeur_bonus(v_mineur, 'MAU') as integer));
	res_succes := 0;
	res_echec_critique := 0;
	code_retour := code_retour||'Votre lancer de dés est de <b>'||trim(to_char(des,'999'))||'</b>, '; -- pos 5 lancer des
	if des > 96 then -- echec critique
		res_echec_critique := 1;
		code_retour := code_retour||'il s’agit donc d’un échec automatique.<br><br>';
	elsif ((des > 5) and (des > limite_comp_maitre) and (des > comp_creuser_modifie)) then
		code_retour := code_retour||'il s’agit donc d’un échec.<br><br>';
		if comp_creuser <= getparm_n(1) then
			res_amelioration := 1;
		end if;
	elsif ((des > 5) and (des > limite_comp_maitre) and (des <= comp_creuser_modifie)) then
		code_retour := code_retour||'il s’agit donc d’une réussite.<br><br>';
		res_amelioration := 1;
		res_succes := 1;
	elsif ((des > 5) and (des <= limite_comp_maitre)) then
		code_retour := code_retour||'il s’agit donc d’une réussite spéciale.<br><br>';
		res_amelioration := 1;
		res_succes := 2;
	elsif (des <= 5) then
		code_retour := code_retour||'il s’agit donc d’une réussite critique.<br><br>';
		res_amelioration := 1;
		res_succes := 3;
	end if;
	-- AMELIORATION
	if res_amelioration > 0 then
		temp_ameliore_competence := ameliore_competence_px(v_mineur,num_comp,comp_creuser);
		code_retour := code_retour||'Votre jet d’amélioration est de '||split_part(temp_ameliore_competence,';',1)||', ';
		if split_part(temp_ameliore_competence,';',2) = '1' then
			code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence.<br> ';
			code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
			px_gagne := px_gagne + 1;
		else
			code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
		end if;
	end if;
	if res_succes > 0 then
		-- USURE DU MUR
		des := lancer_des(1,8 + v_force);
		v_mur_usure := v_mur_usure - des;
		if v_mur_usure > 0 then
			update murs set mur_usure = v_mur_usure where mur_pos_cod = v_pos_mur;
			if v_mur_usure > 999 then
				v_mur_texte := 'intact';
			elseif v_mur_usure > 799 then
				v_mur_texte := 'presque intact';
			elseif v_mur_usure > 599 then
				v_mur_texte := 'légèrement usé';
			elseif v_mur_usure > 349 then
				v_mur_texte := 'usé';
			elseif v_mur_usure > 99 then
				v_mur_texte := 'très usé';
			else
				v_mur_texte := 'presque épuisé';
			end if;
			code_retour := code_retour||'Le filon s’use un peu plus il est maintenant <b>'||v_mur_texte||'</b>.<br><br> ';
		else
			delete from murs where mur_pos_cod = v_pos_mur;
			tmp_txt := init_automap_pos (v_pos_mur);
			code_retour := code_retour||'Le filon est épuisé, le mur s’écroule devant vos yeux !.<br><br> ';
			gain_renommee := gain_renommee * 5;
		end if;

		-- RESULTAT DU SUCCES
		des := lancer_des(1,100);
		-- Azaghal le 5/09/08, amélioration des gains en cas de spéciaux et critiques
		des := des + ( 10 * ( res_succes - 1 ) );
		if des < 15 then
			px_gagne := px_gagne + 0.3;
			code_retour := code_retour||'Pas de bol, vous n’avez rien trouvé !.<br><br> ';
		elseif   des < 25 then
			-- Objet inutile
			-- Choix de l'objet trouvé
			px_gagne := px_gagne + 0.5;
			des := lancer_des(1,100);
			if des < 20 then
				-- GRAVATS
				g_obj_cod = 333;
			elseif   des <40 then
				-- STATUE
				g_obj_cod = 164;
			elseif   des <60 then
				-- FOSSILE 1
				g_obj_cod = 370;
			elseif   des <80 then
				-- FOSSILE 2
				g_obj_cod = 371;
			else
				-- FOSSILE 3
				g_obj_cod = 372;
			end if;
			tmp_txt := cree_objet_perso_nombre (g_obj_cod, v_mineur, 1);
			select into
				obj_nom
				gobj_nom
			from objet_generique
			where 	gobj_cod = g_obj_cod;
			code_retour := code_retour||'Vous réussissez à extraire du filon:  <b>'||obj_nom||'</b>.<br><br> ';
		elseif des < 55 then
			-- quelques Br
			px_gagne := px_gagne + 0.5;

			-- Azaghal le 5/09/08, amélioration des gains en cas de spéciaux et critiques
			des := des * 5 + ( 50 * ( res_succes - 1 ) );
			update perso
			set perso_po = perso_po + des
			where perso_cod = v_mineur;
			code_retour := code_retour||'Vous réussissez à extraire du filon:  <b>'||trim(to_char(des,'999999999'))||' Br</b>.<br><br> ';
			obj_nom := trim(to_char(des,'999999999'))||' Brouzoufs';
		elseif des < 75 then
			g_obj_cod := 26 + lancer_des(1, 20);
	                px_gagne := px_gagne + 1;
			gain_renommee := gain_renommee * 3;
			tmp_txt := cree_objet_perso_nombre (g_obj_cod, v_mineur, 1);
			select into
				obj_nom
				gobj_nom
			from objet_generique
			where 	gobj_cod = g_obj_cod;
			code_retour := code_retour||'Vous réussissez à extraire du filon:  <b>'||obj_nom||'</b>.<br><br> ';
		else
                        nb_objets := lancer_des(1, 2) +  ( res_succes - 1 );
                        WHILE nb_objets > 0 LOOP  
			     -- AJOUT DE L'OBJET
			     -- Choix de l'objet trouvé
			     px_gagne := px_gagne + 0.1;
			     des := lancer_des(1,v_mur_richesse);
			     -- Azaghal le 5/09/08, amélioration des gains en cas de spéciaux et critiques
			     des := des + ( 50 * ( res_succes - 1 ) );
			     if des < 81 then
				-- CHARBON
				g_obj_cod = 336;
			     elseif   des < 201 then
				-- FER
				g_obj_cod = 335;
			     elseif   des < 231 then
				-- GEMME E
				g_obj_cod = 356;
                             elseif   des < 261 then
				-- Citrinoline
				g_obj_cod = 1056;
			     elseif   des <291 then
				-- GEMME D
				g_obj_cod = 355;
                             elseif   des < 321 then
				-- Orpimentien
				g_obj_cod = 1061;
			     elseif   des <351 then
				-- MITHRIL
				g_obj_cod = 342;
			     elseif   des <381 then
				-- Schorlactide
				g_obj_cod = 1066;
                             elseif   des <411 then
				-- JADE
				g_obj_cod = 360;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <441 then
				-- Pyrrholitoline
				g_obj_cod = 1059;
			     elseif   des <471 then
				-- TOPAZE
				g_obj_cod = 359;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <501 then
				-- Boraximère
				g_obj_cod = 1064;
			     elseif   des <531 then
				-- GEMME A
				g_obj_cod = 352;
			     elseif   des <561 then
				-- GEMME B
				g_obj_cod = 353;
			     elseif   des <591 then
				-- OBSIDIENNE
				g_obj_cod = 357;
			     elseif   des <621 then
				-- GEMME C
				g_obj_cod = 354;
			     elseif   des <651 then
				--  Chalcopyritiminou
				g_obj_cod = 1060;
			     elseif   des <681 then
				-- AMETHYSTE
				g_obj_cod = 358;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <711 then
				--  Torbénitiste
				g_obj_cod = 1058;
			     elseif   des <741 then
				-- AMBRE
				g_obj_cod = 361;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <771 then
				--  Autunizolinette
				g_obj_cod = 1057;
			     elseif   des <801 then
				-- PEPITE D'OR
				g_obj_cod = 341;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <831 then
				--  Crocoït sublime
				g_obj_cod = 1062;
			     elseif   des <861 then
				-- SAPHIR
				g_obj_cod = 340;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <891 then
				--  Tibnitrion
				g_obj_cod = 1065;
			     elseif   des <921 then
				-- RUBIS
				g_obj_cod = 339;
				px_gagne := px_gagne + 0.5;
				gain_renommee := gain_renommee * 2;
			     elseif   des <951 then
				-- EMERAUDE
				g_obj_cod = 338;
				px_gagne := px_gagne + 1;
				gain_renommee := gain_renommee * 2;
			     else
				-- DIAMANT
				g_obj_cod = 337;
				px_gagne := px_gagne + 1;
				gain_renommee := gain_renommee * 2;
  			     end if;
                             nb_objets := nb_objets - 1;
                             tmp_txt := cree_objet_perso_nombre (g_obj_cod, v_mineur, 1);
			     select into
				obj_nom
				gobj_nom
			     from objet_generique
			     where gobj_cod= g_obj_cod;
			     code_retour := code_retour||'Vous réussissez à extraire du filon:  <b>'||obj_nom||'</b>.<br><br> ';
                        END LOOP;
		end if;

		-- augmentation des gains pour les réussites critiques et les spéciaux
		-- gain de 0.5 et 1 px supplémentaire
		-- Azaghal le 05/09/2008
		if res_succes = 2 then
			px_gagne := px_gagne + 0.25;
			gain_renommee := gain_renommee * 1.5;
		elseif   res_succes = 3 then
			px_gagne := px_gagne + 0.5;
			gain_renommee := gain_renommee * 2;
		end if;
	else
		-- ECHEC DE L'ACTION
		if res_echec_critique > 0 then
			-- ECHEC CRITIQUE: EVT SPECIAL
			des := lancer_des(1,100);
			gain_renommee := gain_renommee * (-5);
			if des < 33 then
				-- COUP DE GRISOU
				for ligne in select perso_cod,perso_nom,perso_pv,perso_pv_max
					from perso,perso_position,positions
					where perso_actif = 'O'
						and perso_tangible = 'O'
						and ppos_perso_cod = perso_cod
						and ppos_pos_cod = pos_cod
						and pos_cod = pos_mineur
				loop
					v_degats := lancer_des(1,10);
					texte_evt := '[perso_cod1] a été victime d’un coup de grisou.';
					insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
					values(66,now(),ligne.perso_cod,texte_evt,'O','O');
					update perso set perso_pv = max(1,perso_pv - v_degats) where perso_cod = ligne.perso_cod;
					perform ajoute_bonus(ligne.perso_cod, 'POI', 1, 3);
				end loop;
				code_retour := code_retour||'Votre erreur provoque un <b>Coup de grisou</b> !.<br><br> ';
			/*elseif   des < 66 then
				-- EFFONDREMENT
				for ligne in select perso_cod,perso_nom,perso_pv,perso_pv_max
					from perso,perso_position,positions
					where perso_actif = 'O'
						and perso_tangible = 'O'
						and ppos_perso_cod = perso_cod
						and ppos_pos_cod = pos_cod
						and pos_cod = pos_mineur
				loop
					v_degats := lancer_des(1,15);
					texte_evt := '[perso_cod1] a été victime d’un effondrement.';
					insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
					values(66,now(),ligne.perso_cod,texte_evt,'O','O');
					update perso set perso_pv = max(1,perso_pv - v_degats) where perso_cod = ligne.perso_cod;
					perform ajoute_bonus(ligne.perso_cod, 'POI', 1, 3);
				end loop;
				insert into murs (mur_pos_cod,mur_creusable,mur_usure,mur_richesse) values (pos_mineur,'O',100,50);
				code_retour := code_retour||'Votre erreur provoque un <b>Effondrement</b>, vous êtes esseveli sous les gravats !.<br><br> ';
				*/
			else
				-- ajout du 05/01/2010 par Merrick : test sur l'étage pour éviter les elems au 0
				select into v_etage pos_etage 
				from positions
				where pos_cod = v_pos_mur;
				if (v_etage in (0, 32)) then
					tmp_txt := cree_monstre_pos (1, pos_mineur);
					tmp_txt := cree_monstre_pos (1, pos_mineur);
					tmp_txt := cree_monstre_pos (2, pos_mineur);
					tmp_txt := cree_monstre_pos (2, pos_mineur);
					code_retour := code_retour||'Votre erreur dérange une nichée de rats !.<br><br> ';
				else
					-- REVEIL DES ESPRITS
					tmp_txt := cree_monstre_pos (187, pos_mineur);
					code_retour := code_retour||'Votre erreur provoque le <b>réveil d’un esprit de la Terre</b> !.<br><br> ';
				end if;
			end if;

		else
			-- ECHEC SIMPLE 50% CHANCES DE BLESSURE LEGERE
			des := lancer_des(1,100);
			if des > 50 then
				des := lancer_des(1,5);
				gain_renommee := gain_renommee * (-1);
				update perso set perso_pv = max(1,perso_pv - des) where perso_cod = v_mineur;
				texte_evt := '[perso_cod1] a été victime d’une chute de pierre.';
				insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
				values(66,now(),v_mineur,texte_evt,'O','O');
				code_retour := code_retour||'Une chute de pierre vous occasionne <b>'||trim(to_char(des,'999999'))||'</b> dégâts.<br><br> ';
			end if;
		end if;
	end if;
	-- EVENEMENTS
	if res_succes > 0 then
		texte_evt := '[perso_cod1] a trouvé '||obj_nom||' en creusant.';
	else
		texte_evt := '[perso_cod1] n’a pas réussi à creuser.';
	end if;
	insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
	values (nextval('seq_levt_cod'),65,now(),1,v_mineur,texte_evt,'O','O');
	-- XP
	update perso
	set perso_px = perso_px + px_gagne, 
		perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
	where perso_cod = v_mineur;
	code_retour := code_retour||'Vous gagnez <b>'||to_char(px_gagne,'999999990D99')||'</b> PX pour cette action.';
	return code_retour;
end;$function$

