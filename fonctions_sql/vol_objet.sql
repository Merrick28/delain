CREATE OR REPLACE FUNCTION public.vol_objet(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function vol : un voleur tente de d&#233;trousser sa victime       */
/* On passe en param&#232;tres                                        */
/*    $1 = perso_cod voleur                                      */
/*    $2 = perso_cod cible                                       */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Cr&#233;&#233; le 01/04/2003                                            */
/*****************************************************************/
declare
--------------------------------------------------------------------------------
-- variables fourre tout
--------------------------------------------------------------------------------
	texte_evt text;	                -- Texte de l'&#233;vennement
	texte_evt_bis text;	            -- Texte de l'&#233;vennement pour la cible
	code_retour text;				-- chaine qui contiendra le html de retour
	compt integer;					-- compteur mutli usage
	des integer;					-- lancer de des
--------------------------------------------------------------------------------
-- renseignements de l attaquant
--------------------------------------------------------------------------------
	v_attaquant alias for $1;	-- perso_cod attaquant
	pa integer; 					-- pa de l'attaquant
	v_cout_pa integer;			-- cout en PA de l'attaque
	pos_per1 integer;				-- position de l attaquant
	num_comp integer;				-- comp_cod utilis&#233;e
	nom_competence competences.comp_libelle%type;
										-- nom de la comp utilis&#233;e
	comp_vol integer;		-- valeur de la comp d'attaque
	comp_vol_init integer;	-- valeur initiale de la comp
	comp_vol_modifie integer; -- valeur de la comp d'attaque modifi&#233;e
	comp_vol_detection integer; -- valeur de la comp modifi&#233;e pour le jet de d&#233;tection

	malus_type_cible integer;
	malus_lock_a integer;
	malus_lock_d integer;
	bonus_malus_magie integer;
	malus_combat integer;
	malus_monstre integer;

	nb_lock_o integer;			-- nombre de locks offensifs de l'attaquant
	nb_lock_d integer;			-- nombre de locks d&#233;fensifs de l'attaquant

	nb_lock_d_cible integer;	-- nombre de locks d&#233;fensifs de la cible
	nb_lock_o_cible integer;	-- nombre de locks offensifs de la cible
   									-- bonus aux d&#233;gats de l'attaquant
    px_gagne numeric;				-- px gagn&#233;s par l'attaquant

   	num_obj_vole integer;
   	res_succes integer;
   	res_amelioration integer;
   	res_vu integer;

   limite_comp_maitre integer; -- limite pour le coup sp&#233;cial
   v_bonus_toucher integer;	-- bonus au toucher de l'attaquant (magie)
   v_bonus_degats integer;		-- bonus au toucher de l'attaquant (caracs)
  	nom_arme text;					-- nom de l'arme utilis&#233;e
	v_type_arme integer; 		-- type d'arme : 0 pas d arme, 1 contact, 2 distance
	distance_perso integer;		-- distance maxi d'attaque
	degats_portes integer;		-- degats th&#233;oriques de l'attaque
	degats_effectues integer;	-- degats r&#233;&#233;llement effectu&#233;s (armure d&#233;duite)
	temp_ameliore_competence text;
	temp_comp_vol numeric;
										-- texte temporaire pour l'amel de la comp&#233;tence
	type_attaquant integer;
	v_num_objet integer;
	v_nom_objet text;

	v_type_lock integer;
	combat_groupe integer;
--------------------------------------------------------------------------------
-- renseignements de la cible
--------------------------------------------------------------------------------
	v_cible alias for $2;		-- perso_cod cible
	type_cible integer;			-- type de la cible (1 pour joueur, 2 pour monstre)
	pos_per2 integer;				-- position de la cible
	nom_per2 perso.perso_nom%type;
										-- nom de la cible
	armure integer;				-- armure de la cible
	po_cible integer;				-- points de vie de la cible
	v_cible_tangible text;		-- tangibilit&#233; de la cible
	v_compte_fam integer;
	v_compte integer;
	temp_texte_titre text;
	v_niveau_cible integer;
	v_dex_cible integer;
    v_int_cible integer;

	v_dex_voleur integer;
    v_int_voleur integer;
    v_comp_vol_cible integer;
    
    v_poids_objet numeric;

--------------------------------------------------------------------------------
-- variables &#233;v&#232;nements
--------------------------------------------------------------------------------
begin
	-- competence vol
	num_comp := 86;
	px_gagne := 0;
	malus_monstre := getparm_n(96);
	malus_combat := getparm_n(97);
	v_cout_pa := getparm_n(95);
	code_retour := '<p>'; -- par d&#233;faut, tout est OK

/***********************************************/
/* Etape 1 : on v&#233;rifie que le voleur existe */
/***********************************************/
-- on r&#233;cup&#232;re ici tous les &#233;l&#233;ments concernant l attaquant
	select into 		pa,
						pos_per1,
						v_cible_tangible,
						v_dex_voleur,
						v_int_voleur
				perso_pa,
				ppos_pos_cod,
				perso_tangible,
				perso_dex,
				perso_int
		from perso,perso_position
		where perso_cod = v_attaquant
		and ppos_perso_cod = perso_cod;
	if not found then
		code_retour := '<p>Erreur ! Voleur non trouv&#233; !';
		return code_retour;
	end if;
	if v_cible_tangible = 'N' then
		code_retour := '<p>Erreur ! Vous &#234;tes intangible, il vous est impossible de voler !';
		return code_retour;
	end if;
/*********************************************/
/* Etape 2 : on v&#233;rifie que la cible existe  */
/*********************************************/
-- on r&#233;cup&#232;re ici tous les &#233;l&#233;ments concernant la cible
	select into 	type_cible,
						nom_per2,
						pos_per2,
						po_cible,
						v_cible_tangible,
						v_niveau_cible,
						v_dex_cible,
						v_int_cible
					perso_type_perso,
					perso_nom,
					ppos_pos_cod,
					perso_po,
					perso_tangible,
					perso_niveau,
					perso_dex,
					perso_int
		from perso,perso_position
		where perso_cod = v_cible
		and ppos_perso_cod = perso_cod
		and perso_actif = 'O' for update;
	if not found then
		code_retour := '<p>Erreur ! Cible non trouv&#233;e !';
		return code_retour;
	end if;
	if v_cible_tangible = 'N' then
		code_retour := '<p>Erreur ! Cette cible est intangible, il vous est impossible de la voler !';
		return code_retour;
	end if;
	if type_cible = 3 then
		if type_attaquant = 1 then
			select into v_compte_fam
				pcompt_compt_cod
				from perso_familier,perso_compte
				where pfam_familier_cod = v_cible
				and pfam_perso_cod = pcompt_perso_cod;
			select into v_compte pcompt_compt_cod from perso_compte
				where pcompt_perso_cod = v_attaquant;
			if v_compte = v_compte_fam then
				code_retour := '<p>Erreur ! Vous ne pouvez pas voler les familiers d''un des persos de votre compte !';
				return code_retour;
			end if;
		end if;
	end if;
	select into compt lieu_cod
		from lieu,lieu_position
		where lpos_pos_cod = pos_per2
		and lpos_lieu_cod = lieu_cod
		and lieu_refuge = 'O';
	if found then
		code_retour := '<p>Erreur ! La cible est sur un lieu prot&#233;g&#233; !';
		return code_retour;
	end if;
/****************************************************/
/* debut des controles li&#233;s aux combats de groupe   */
/****************************************************/

-- Les locks du voleurs donneront lieu &#224; des malus ou une impossiblit&#233; de voler.
	select into nb_lock_d count(lock_cod)
			from lock_combat
			where lock_cible = v_attaquant;
	select into nb_lock_o count(lock_cod)
			from lock_combat
			where lock_attaquant = v_attaquant;
-- les locks de la cible donneront lieu &#224; des malus ou impossibilit&#233; de voler.
	select into nb_lock_d_cible count(lock_cod)
			from lock_combat
			where lock_cible = v_cible;
	select into nb_lock_o_cible count(lock_cod)
			from lock_combat
			where lock_attaquant = v_cible;

/*******************************************************/
/* fin des contr&#244;les li&#233;s aux combats de groupe        */
/*******************************************************/
/*************************************************/
/* Etape 3 : on v&#233;rifie qu il y ait assez de pa  */
/*************************************************/
	if pa < v_cout_pa then
		code_retour := '<p>Erreur ! Pas assez de PA pour effectuer cette action';
		return code_retour;
	end if;
/*************************************************/
/* Etape 4 : on v&#233;rifie la distance d attaque    */
/*************************************************/
	if pos_per1 != pos_per2 then
		code_retour := '<p>Erreur ! La cible est trop &#233;loign&#233;e du voleur';
		return code_retour;
	end if;

/*************************************/
/* Etape 6 : les controles sont bons */
/*     On passe &#224; l attaque          */
/*************************************/

--  enleve les pa
	update perso
		set perso_pa = pa - v_cout_pa
		where perso_cod = v_attaquant;

-- valeur de la competence de base
		select into comp_vol,num_comp
			pcomp_modificateur,pcomp_pcomp_cod
			from perso_competences
			where pcomp_perso_cod = v_attaquant
			and pcomp_pcomp_cod = 86;
		if not found then
			code_retour := '<p>Erreur ! Vous n''avez la comp&#233;tence requise pour voler !<br>';
			return code_retour;
		end if;
	comp_vol_init := comp_vol;
-- Bonus-Malus - 1 - Concentration
	select into compt concentration_perso_cod from concentrations
		where concentration_perso_cod = v_attaquant;
	if found then
		comp_vol_modifie := comp_vol + 20;
		delete from concentrations where concentration_perso_cod = v_attaquant;
		code_retour := code_retour||'Votre concentration augmente vos chances de r&#233;ussite: '||malus_type_cible::text||'%<br>';
	else
		comp_vol_modifie := comp_vol;
	end if;
-- Bonus-Malus - 2 - Type de cible (malus en cas de monstre ou familier)
	if type_cible != 1 then
		malus_type_cible :=  -3 * v_niveau_cible;
	    if malus_type_cible > - malus_monstre then
			malus_type_cible := - malus_monstre;
		end if;
		comp_vol_modifie := comp_vol_modifie + malus_type_cible;
		code_retour := code_retour||'Vos chances sont r&#233;duites en attaquant un monstre: '||malus_type_cible::text||'%<br>';
	end if;
-- Bonus-Malus - 3 - Locks de combat.
	malus_lock_a := (nb_lock_d + nb_lock_o) * -1 * malus_combat;
	if malus_lock_a != 0 then
		comp_vol_modifie := comp_vol_modifie + malus_lock_a;
		code_retour := code_retour||'Vous &#234;tes en combat vos chances de r&#233;ussite sont diminu&#233;es: '||malus_lock_a::text||'%<br>';
	end if;
	malus_lock_d := (nb_lock_d_cible + nb_lock_o_cible) * -1 * malus_combat;
	if malus_lock_a != 0 then
		comp_vol_modifie := comp_vol_modifie + malus_lock_d;
		code_retour := code_retour||'Votre cible est en plein combat vos chances de r&#233;ussite sont diminu&#233;es: '||malus_lock_d::text||'%<br>';
	end if;
-- Bonus-Malus - 4 - Sorts.
	comp_vol_modifie := comp_vol_modifie + valeur_bonus(v_attaquant, 'VOL');

-- Bonus-Malus - 5 - Minimum de 10%
	if comp_vol_modifie < 10 then
		comp_vol_modifie := 10;
	end if;
-- limite pour un coup sp&#233;cial
	limite_comp_maitre := round(comp_vol_modifie*0.25);
-- comp pour la detection
	comp_vol_detection := round(comp_vol_modifie*0.30);
	if num_comp = 85 then
	-- niveau 2
		comp_vol_detection := round(comp_vol_modifie*0.60);
	end if;
	if num_comp = 86 then
	-- niveau 3
		comp_vol_detection := round(comp_vol_modifie*0.80);
	end if;
	comp_vol_detection := comp_vol_detection - v_dex_cible - v_int_cible + v_dex_voleur + v_int_voleur;
	-- 
	select into v_comp_vol_cible
			pcomp_modificateur,pcomp_pcomp_cod
			from perso_competences
			where pcomp_perso_cod = v_cible
			and pcomp_pcomp_cod IN(84,85,86);
	comp_vol_detection := comp_vol_detection - (v_comp_vol_cible / 5);

---------------------------------------------------------------------------
-- etape 5.2 : on commence &#224; g&#233;n&#233;rer un code retour
   	res_succes := 0;
   	res_amelioration := 0;
   	res_vu := 1;

	code_retour := code_retour||'<br>Vous tentez de voler le ';
	if type_cible = 1 then
		code_retour := code_retour||'joueur ';
	else
		code_retour := code_retour||'monstre ';
	end if;
	code_retour := code_retour||' <b>'||nom_per2||'</b><br><br>'; -- pos 2
	select into nom_competence comp_libelle from competences
		where comp_cod = num_comp;
	code_retour := code_retour||'Vous avez utilis&#233; la comp&#233;tence <b>'||nom_competence;
	code_retour := code_retour||'</b> (chance de r&#233;ussite en tenant compte des modificateurs :<b>'||trim(to_char(comp_vol_modifie,'999'))||'</b> %)<br>';

-- etape 5.3 : on regarde si l attaque a r&#233;ussi
	des := lancer_des(1,100);
	code_retour := code_retour||'Votre lancer de d&#233;s est de <b>'||trim(to_char(des,'999'))||'</b>, '; -- pos 5 lancer des
	if des > 96 then -- echec critique
		code_retour := code_retour||'il s''agit donc d''un &#233;chec automatique.<br><br>';
		-- INSERTION DU TITRE
		temp_texte_titre :='Voleur pas tr&#232;s dou&#233;';
		select into compt ptitre_perso_cod from  perso_titre
		where ptitre_perso_cod = v_attaquant and ptitre_titre = temp_texte_titre;
		if not found then
			insert into perso_titre(ptitre_perso_cod,ptitre_titre,ptitre_date)
			values(v_attaquant,temp_texte_titre,now());
		end if;
	elsif ((des > 5) and (des > limite_comp_maitre) and (des > comp_vol_modifie)) then
		code_retour := code_retour||'il s''agit donc d''un &#233;chec.<br><br>';
		if comp_vol_init <= getparm_n(1) then
			res_amelioration := 1;
		end if;
	elsif ((des > 5) and (des > limite_comp_maitre) and (des <= comp_vol_modifie)) then
		code_retour := code_retour||'il s''agit donc d''une r&#233;ussite.<br><br>';
		res_succes := 1;
   		res_amelioration := 1;
	elsif ((des > 5) and (des <= limite_comp_maitre)) then
		code_retour := code_retour||'il s''agit donc d''une r&#233;ussite sp&#233;ciale.<br><br>';
		res_succes := 1;
   		res_amelioration := 1;
	elsif (des <= 5) then
		code_retour := code_retour||'il s''agit donc d''une r&#233;ussite critique.<br><br>';
		res_succes := 1;
   		res_amelioration := 1;
	end if;
	-- TRANSFERT DE L'OBJET
	v_poids_objet := 0;
	if res_succes > 0 then
		-- Selection de l'objet
		select into compt count(obj_cod)as num from objets, perso_objets
		  where perobj_obj_cod = obj_cod
		  and perobj_perso_cod = v_cible
		  and perobj_equipe <> 'O'
		  and obj_poids <= 2
		  and not exists (select 1 from perso_glyphes -- Bleda 30/01/11 : Glyphes
		  		  where pglyphe_perso_cod = v_attaquant
		  		  and pglyphe_obj_cod = obj_cod);
	        if compt < 1 then
		  		code_retour := code_retour||'Dommage, la cible n''a aucun petit objet dans son sac !<br><br> ';
		  		v_nom_objet := 'rien du tout';
            else		 
			  des := lancer_des(1,compt)-1;
			  select into v_num_objet,v_nom_objet,v_poids_objet obj_cod,obj_nom,obj_poids from objets, perso_objets
				where perobj_obj_cod = obj_cod
		  		and perobj_perso_cod = v_cible
		 		and perobj_equipe <> 'O'
		  		and obj_poids <= 2
				offset des limit 1;
              update perso_objets set perobj_perso_cod = v_attaquant
                where perobj_obj_cod = v_num_objet;
              px_gagne := px_gagne + 0.33;
			  code_retour := code_retour||'Vous d&#233;robez l''objet <b>'||v_nom_objet||'</b><br><br> ';
            end if;
	end if;
	--code_retour := code_retour||'Poids='||v_poids_objet||'<br>';
	comp_vol_detection := round(comp_vol_detection - (v_poids_objet *10));
	
	-- VU OU PAS ?
	if res_succes > 0 then
		code_retour := code_retour||'Chances de ne pas &#234;tre vu en tenant compte des modificateurs :<b>'||trim(to_char(comp_vol_detection,'999'))||'</b> %)<br>';
		des := lancer_des(1,100);
		code_retour := code_retour||'Votre lancer de d&#233;s est de <b>'||trim(to_char(des,'999'))||'</b>, ';
		if des > comp_vol_detection then
			code_retour := code_retour||'La cible a vu qui &#233;tait le voleur.<br><br>';
			res_vu := 1;
		else
			code_retour := code_retour||'La cible n''a pas pu voir qui &#233;tait le voleur.<br><br>';
			res_vu := 0;
		end if;
	end if;

	-- AMELIORATION
	if res_amelioration > 0 then
		temp_ameliore_competence := ameliore_competence_px(v_attaquant,num_comp,comp_vol_init);
		code_retour := code_retour||'Votre jet d''am&#233;lioration est de '||split_part(temp_ameliore_competence,';',1)||', '; -- pos 7 8 9 10
		if split_part(temp_ameliore_competence,';',2) = '1' then
			code_retour := code_retour||'vous avez donc <b>am&#233;lior&#233;</b> cette comp&#233;tence.<br> ';
			code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
			px_gagne := px_gagne + 1;
		else
			code_retour := code_retour||'vous n''avez pas am&#233;lior&#233; cette comp&#233;tence.<br><br> ';
		end if;
	end if;

	-- EVENEMENTS
	if res_succes > 0 then
		if res_vu > 0 then
			-- VU
			texte_evt := '[attaquant] a vol&#233; '||v_nom_objet||' &#224; [cible] qui s''en est aper&#231;u';
			texte_evt_bis := '[attaquant] a vol&#233; '||v_nom_objet||' &#224; [cible] qui s''en est aper&#231;u';
			-- INSERTION DU LOCK
			if type_attaquant != 3 then
				if type_cible != 3 then
					delete from lock_combat where lock_attaquant = v_attaquant and lock_cible = v_cible;
					insert into lock_combat (lock_cod,lock_attaquant,lock_cible,lock_nb_tours)
						values (nextval('seq_lock_cod'),v_attaquant,v_cible,getparm_n(17));
					end if;
			end if;
		else
			texte_evt := '[attaquant] a vol&#233; '||v_nom_objet||' &#224; [cible] en toute discr&#233;tion';
			texte_evt_bis := 'un inconnu a vol&#233; '||v_nom_objet||' &#224; [cible] en toute discr&#233;tion';
		end if;
	else
		if res_vu > 0 then
		  texte_evt := '[attaquant] a rat&#233; un vol sur [cible]';
		  texte_evt_bis := '[attaquant] a rat&#233; un vol sur [cible]';
    else
      texte_evt := '[attaquant] a rat&#233; un vol sur [cible]';
		  texte_evt_bis := 'un inconnu a rat&#233; un vol sur [cible]';
    end if;
	end if;
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),55,now(),1,v_attaquant,texte_evt,'O','N',v_attaquant,v_cible);
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),55,now(),1,v_cible,texte_evt_bis,'N','N',v_attaquant,v_cible);
	-- XP
	if px_gagne is null then
		px_gagne := 0.0;
	end if;
	update perso
	set perso_px = perso_px + px_gagne, perso_kharma = perso_kharma - 0.1
	where perso_cod = v_attaquant;
	code_retour := code_retour||'Vous gagnez '||to_char(px_gagne,'999999990D99')||' PX pour cette action.';

	return code_retour;
end;$function$

