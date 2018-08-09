CREATE OR REPLACE FUNCTION public.f_repare_armure(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************************/
/* fonction repare_armure : répare une armure                      */
/*   on passe en paramètres :                                      */
/*   $1 = perso_cod qui répare                                     */
/*   $2 = objet réparé                                             */
/* on a en retour une chaine html                                  */
/*******************************************************************/
/* Modifications                                                   */
/* 18/04/2006 : rajout de la condition sur l’état max de réparation*/
/*             Correction sur amélioration auto (réinit de variable*/
/* 29/05/2012 : ajout renommée artisanale                          */
/*******************************************************************/

declare
	---------------------------------------------------------------------
	-- variable de retour 
	---------------------------------------------------------------------
	code_retour text;
	texte_evt text;
	---------------------------------------------------------------------
	-- variable concernant le perso
	---------------------------------------------------------------------	
	personnage alias for $1;
	v_int integer;			-- intelligence
	v_dex integer;			-- dextérité
	v_etat_max integer;		-- réparation maximum
	v_pa integer;			-- pa du perso
	v_capa_repar integer;		-- capacité de réparation
	num_comp integer;		-- comp_cod utilisée
	nom_comp text;			-- nom de la compétence
	v_comp_init integer;		-- valeur de base de la comp
	v_comp integer;			-- valuer utilisée comme reférence
	v_des integer;			-- lancer de dés
	px_gagne integer;		-- nb de PX gagnés
	temp_ameliore_competence text; 	-- texte temporaire pour l’amel de la compétence
	is_equipe text;			-- armure équipée ?
	bonmal integer;
	malus integer;
	gain_renommee numeric;		-- gain (ou perte) de renommée artisanale
	---------------------------------------------------------------------
	-- variable concernant l’objet
	---------------------------------------------------------------------	
	num_objet alias for $2;
	v_etat numeric;			-- etat de l’objet	
	is_identifie varchar;		-- identifie ?
	---------------------------------------------------------------------
	-- Fourre tout
	---------------------------------------------------------------------		
	temp integer;
begin
	num_comp := 60;
	gain_renommee := 0.2;
	---------------------------------------------------------------------
	-- Etape 1  : contrôles
	---------------------------------------------------------------------
	-- pour le perso
	select into
		v_capa_repar,
		v_pa
		perso_capa_repar,
		perso_pa
	from perso
	where perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Personnage non trouvé !</p>';
		return code_retour;
	end if;

	-- pour l’objet
	select into v_etat , v_etat_max obj_etat , obj_etat_max
	from objets
	where obj_cod = num_objet;
	if not found then
		code_retour := '<p>Erreur ! Objet non trouvé !</p>';
		return code_retour;
	end if;
	if v_etat = v_etat_max then
		code_retour := '<p>Il ne sert à rien de réparer cet objet, vous ne pourrez pas le rajeunir (Quoique, essayez de demander dans une échoppe)</p>';
		return code_retour;
	end if;

	-- les combats
	select into temp lock_cod
	from lock_combat
	where lock_attaquant = personnage;
	if found then
		code_retour := '<p>Erreur ! Vous ne pouvez pas réparer les objets pendant un combat !</p>';
		return code_retour;
	end if;

	select into temp lock_cod
	from lock_combat
	where lock_cible = personnage;
	if found then
		code_retour := '<p>Erreur ! Vous ne pouvez pas réparer les objets pendant un combat !</p>';
		return code_retour;
	end if;

	-- on vérifie que l’objet soit bien dans l’inventaire et identifie
	select into temp,is_identifie,is_equipe
		perobj_obj_cod,perobj_identifie,perobj_equipe
	from perso_objets
	where perobj_obj_cod = num_objet
		and perobj_perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! L’objet n’est pas dans votre inventaire !</p>';
		return code_retour;
	end if;
	if is_identifie != 'O' then
		code_retour := '<p>Erreur ! L’objet n’est pas identifié !</p>';
		return code_retour;
	end if;
	if is_equipe = 'O' then
		code_retour := '<p>Erreur ! Vous ne pouvez pas réparer une armure équipée !</p>';
		return code_retour;
	end if;

	-- le nombre de PA
	if v_pa < getparm_n(40) then
		code_retour := '<p>Erreur ! Vous n’avez pas assez de PA pour réparer cet objet !</p>';
		return code_retour;
	end if;
	-- on calcule le coefficient maximum de réparation
	if v_etat >= min(v_capa_repar , 100) * v_etat_max / 100 then
		code_retour := '<p>Votre capacité de réparation ne vous permet pas d’améliorer l’état de cette armure.</p>';
		return code_retour;
	end if;

	-- la compétence
	select into nom_comp
		comp_libelle
	from competences
	where comp_cod = num_comp;
	if not found then
		code_retour := '<p>Erreur ! Compétence non trouvée !</p>';
		return code_retour;
	end if;	

	select into v_comp_init
		pcomp_modificateur
	from perso_competences
	where pcomp_perso_cod = personnage
		and pcomp_pcomp_cod = num_comp;
	if not found then
		code_retour := '<p>Erreur ! Valeur compétence non trouvée !</p>';
		return code_retour;
	end if;
	------------------------------------------
	-- Tout semble OK, on passe à la suite
	------------------------------------------
	v_comp := v_comp_init;
	-- On enlève les PA 
	update perso
	set perso_pa = perso_pa - getparm_n(40)
	where perso_cod = personnage;

	-- on regarde s’il y a concentration
	select into temp concentration_perso_cod from concentrations
	where concentration_perso_cod = personnage;
	if found then
		v_comp := v_comp + 20;
		delete from concentrations where concentration_perso_cod = personnage;
	end if;

	-- on prépare un retour
	code_retour := '<p>Vous avez utilisé la compétence <b>'||nom_comp||'</b><br>';
	code_retour := code_retour||'Votre chance de réussite en tenant compte des modificateurs est de : <b>'||trim(to_char(v_comp,'99999'))||' %</b><br>';
	-- on lance les dés
	-- on regarde si la cible est bénie ou maudite
	bonmal := valeur_bonus(personnage, 'BEN') + valeur_bonus(personnage, 'MAU');
	if bonmal <> 0 then
		v_des := lancer_des3(1,100,bonmal);
	else
		v_des := lancer_des(1,100);
	end if;
	code_retour := code_retour||'Le lancer de dés est de <b>'||trim(to_char(v_des,'9999'))||'</b>, ';
	--
	if v_des > 95 then -- échec critique
		px_gagne := 0;
		gain_renommee := gain_renommee * (-2);
		code_retour := code_retour||'il s’agit donc d’un échec critique.<br><br>';

		update objets
		set obj_etat = obj_etat - 15
		where obj_cod = num_objet;

		code_retour := code_retour||'Vous avez <b>détérioré</b> l’objet';
		texte_evt := '[perso_cod1] a tenté de réparer un objet, et l’a détérioré.';

		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),25,now(),1,personnage,texte_evt,'O','O');

		select into v_etat
			obj_etat
		from objets
		where obj_cod = num_objet;

		if v_etat <= 0 then -- destruction
			code_retour := code_retour||', il se détruit pendant votre tentative.<br>';
			gain_renommee := gain_renommee * 10;
			-- on doit déleter tout ce qui concerne cet objet
			temp := f_del_objet(num_objet);
			-- on rajoute les évènements
			texte_evt := '[perso_cod1] a détruit un objet en essayant de le réparer.';

			insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
			values(nextval('seq_levt_cod'),26,now(),1,personnage,texte_evt,'O','N');
		else
			code_retour := code_retour||'. Il est maintenant dans un état '||get_etat_objet(v_etat)||'.<br><br>';
			if (v_pa >= 2*getparm_n(40)) then
				code_retour := code_retour||'<a href="action.php?methode=repare&type=2&objet='||cast(num_objet as varchar(20))||'">Réessayer ('||cast(getparm_n(40) as varchar(2))||' PA)</a><br>';
			end if;
		end if; -- fin destruction
		return code_retour;
	end if; -- fin échec
	--
	if v_des <= 5 then -- réussite critique
		px_gagne := 2;
		gain_renommee := gain_renommee * 5;
		code_retour := code_retour || 'il s’agit donc d’une réussite critique.<br>Vous gagnez 2 PX pour cette action.<br><br>';
		temp_ameliore_competence := ameliore_competence(personnage,num_comp,v_comp_init);
		code_retour := code_retour || 'Votre jet d’amélioration est de ' || split_part(temp_ameliore_competence, ';', 1) || ', ';
		if split_part(temp_ameliore_competence,';',2) = '1' then
			code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
			code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
			px_gagne := px_gagne + 1;
		else
			code_retour := code_retour || 'vous n’avez pas amélioré cette compétence.<br><br>';
		end if;

		update objets
		set obj_etat = obj_etat + 2*v_capa_repar
		where obj_cod = num_objet;

		code_retour := code_retour || 'Vous avez <b>amélioré</b> l’état de l’objet. Il est maintenant dans un état '||get_etat_objet(MIN(cast(v_etat + (v_capa_repar*2) as integer), v_etat_max))||'.<br><br>';
		if (v_pa >= 2*getparm_n(40)) then
			code_retour := code_retour||'<a href="action.php?methode=repare&type=2&objet='||cast(num_objet as varchar(20))||'">Recommencer ('||cast(getparm_n(40) as varchar(2))||' PA)</a><br>';
		end if;
		texte_evt := '[perso_cod1] a réparé un objet.';

		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),25,now(),1,personnage,texte_evt,'O','O');
	end if; -- fin réussite critique	
	--
	if v_des > 5 and v_des <= v_comp then -- réussite
		px_gagne := 1;
		code_retour := code_retour||'vous avez donc réussi.<br>Vous gagnez 1 PX pour cette action.<br><br>';

		update objets
		set obj_etat = obj_etat + v_capa_repar,
		obj_etat_max =  obj_etat_max  - getparm_n(106)
		where obj_cod = num_objet;

		temp_ameliore_competence := ameliore_competence(personnage,num_comp,v_comp_init);
		code_retour := code_retour || 'Votre jet d’amélioration est de ' || split_part(temp_ameliore_competence, ';', 1) || ', ';
		if split_part(temp_ameliore_competence,';',2) = '1' then
			code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
			code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
			px_gagne := px_gagne + 1;
		else
			code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
		end if;
		code_retour := code_retour||'Vous avez <b>amélioré</b> l’état de l’objet. Il est maintenant dans un état '||get_etat_objet(MIN(cast(v_etat + v_capa_repar as integer), v_etat_max))||'.<br><br>';
		if (v_pa >= 2*getparm_n(40)) then
			code_retour := code_retour||'<a href="action.php?methode=repare&type=2&objet='||cast(num_objet as varchar(20))||'">Recommencer ('||cast(getparm_n(40) as varchar(2))||' PA)</a><br>';
		end if;
		texte_evt := '[perso_cod1] a réparé un objet.';

		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),25,now(),1,personnage,texte_evt,'O','O');
	end if; -- fin échec
	-- 
	if v_des > v_comp then -- échec
		gain_renommee := gain_renommee * (-1);
		px_gagne := 0;
		code_retour := code_retour||'vous avez donc <b>échoué</b>.<br><br>';
		texte_evt := '[perso_cod1] n’a pas réussi à réparer un objet.';

		insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
		values(nextval('seq_levt_cod'),25,now(),1,personnage,texte_evt,'O','O');

		-- réinitialisation de la comp pour amélioration automatique
		v_comp := v_comp_init;
		if v_comp <= getparm_n(1) then -- amélioration auto
			temp_ameliore_competence := ameliore_competence(personnage,num_comp,v_comp_init);
			code_retour := code_retour||'Votre jet d’amélioration est de '||split_part(temp_ameliore_competence,';',1)||', ';
			if split_part(temp_ameliore_competence,';',2) = '1' then
				code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
				code_retour := code_retour||'Sa nouvelle valeur est '||split_part(temp_ameliore_competence,';',3)||'<br><br>';
				px_gagne := px_gagne + 1;
			else
				code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br>';
			end if; 
		end if; -- fin amélioration auto
		if (v_pa >= 2*getparm_n(40)) then
			code_retour := code_retour||'<a href="action.php?methode=repare&type=2&objet='||cast(num_objet as varchar(20))||'">Réessayer ('||cast(getparm_n(40) as varchar(2))||' PA)</a><br>';
		end if;
	end if; -- fin échec

	update perso set perso_px = perso_px + px_gagne, perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee where perso_cod = personnage;

	select into v_etat
	obj_etat
	from objets
	where obj_cod = num_objet;

	if v_etat > v_etat_max then
		update objets
		set obj_etat = v_etat_max
		where obj_cod = num_objet;
	end if;
	return code_retour;
end;$function$

