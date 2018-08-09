CREATE OR REPLACE FUNCTION public.cree_parchemin(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_parchemin : met un sort sur un parchemin        */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = numéro du sort lancé                                   */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html                            */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   01/01/2014 : Alignement gestion PA sur magie                */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	px_gagne numeric;				-- nombre de px gagnés
	v_comp_m integer;				-- valeur compétence magique liée au sort
	v_comp_cod_m integer;		-- numéro compténce magique liée au sort
	v_comp_e integer;				-- valeur compétence enluminure
	v_nom_comp_e text;			-- nom de la compétence enluminure
	lanceur_pa integer;			-- pa du lanceur
	pa_magie integer;				-- bonus eventuel (PA) au lancement du sort
	compt_rune integer;			-- nombre de runes d'un type donné
	nom_rune text;					-- nom de la rune manquenate
	nb_sort integer;				-- nombre de fois ou le sort à été lancé ce tour
	v_pnbs_cod integer;			-- nombre de fois total où a été lancé ce sort
	v_comp_modifie integer;		-- valeur finale de la comp
	nv_parch	integer;				-- code objet_generique du nouveau parchemin
	temp_renommee numeric;
	nb_sort_niveau integer;
	des integer;
	bonus_pa integer;
	gain_renommee numeric;		-- gain (ou perte) de renommée artisanale
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort alias for $2;		-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	nom_sort text;
	cout_pa integer;
	aggressif text;
	niveau_sort integer;
	ligne_rune record;			-- sert pour les boucles de runes
	texte_memo	text;				-- texte de mémorisation du sort
	v_malus_niveau integer;
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	v_code_parch integer;		-- obj_cod du parchemin vierge
	v_comp_cod2 integer; 	--code de la compétence utilisée
	v_comp_cod text;			-- code des compétences possible
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	compt integer;
	v_special integer;
	temp_ameliore_competence text;
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
-- sur le sort
	select into nom_sort, aggressif, niveau_sort sort_nom, sort_bloquable, sort_niveau from sorts where sort_cod = num_sort;
	if not found then
		code_retour := code_retour || '<p>Erreur : sort non trouvé !';
		return code_retour;
	end if;
	gain_renommee := niveau_sort / 2;

	-- appel de la fonction cout_pa_magie pour les calculs de cout de pa avec correlation pour l’affichage dans la page magie_php
	select into cout_pa cout_pa_magie(lanceur, num_sort, type_lancer);
	bonus_pa := valeur_bonus(lanceur, 'PAM');

-- sur le lanceur
	select into v_code_parch
		obj_cod
	from perso_objets
	inner join objets on obj_cod = perobj_obj_cod
	left outer join transaction on tran_obj_cod = obj_cod
	where perobj_perso_cod = lanceur
		and perobj_identifie = 'O'
		and obj_gobj_cod IN (481, 482, 483, 729)
		and (case when obj_gobj_cod = 481 then 2
			when obj_gobj_cod = 482 then 3
			when obj_gobj_cod = 483 then 4
			when obj_gobj_cod = 729 then 5 end) >= niveau_sort
	order by (case when tran_obj_cod is null then 0 else 1 end),
		(case when obj_gobj_cod = 481 then 2
			when obj_gobj_cod = 482 then 3
			when obj_gobj_cod = 483 then 4
			when obj_gobj_cod = 729 then 5 end)
	limit 1;
	if not found then
		code_retour := code_retour || 'Erreur : Vous n’avez pas de parchemin vierge du bon niveau à disposition !';
		return code_retour;
	end if;		
		
-- sur la compétence
	if (niveau_sort = 2 or niveau_sort = 3) then
		v_comp_cod := '(91, 92, 93)';
	elsif niveau_sort = 4 then
		v_comp_cod := '(92, 93)';
	elsif niveau_sort = 5 then
		v_comp_cod := '(93)';
	end if;
	
-- compétence magique
	select into v_comp_m, v_comp_cod_m
		pcomp_modificateur, pcomp_pcomp_cod
	from perso_competences,sorts,competences
	where pcomp_perso_cod = lanceur
		and pcomp_pcomp_cod = sort_comp_cod
		and sort_cod = num_sort
		and pcomp_pcomp_cod = comp_cod;
	if not found then
		code_retour := code_retour || '<p>Erreur : infos compétence non trouvées !';
		return code_retour;
	end if;
	
-- compétence enluminure : on vérifie qu'elle est bien présente
	v_comp_cod := 'select pcomp_pcomp_cod
		from perso_competences
		where pcomp_perso_cod = ' || to_char(lanceur, '9999999999999') || '
		and pcomp_pcomp_cod in ' || v_comp_cod;
	execute v_comp_cod into v_comp_cod2;
	if not found then
		code_retour := code_retour || '<p>Erreur : Vous n’avez pas la compétence nécessaire (enluminure du bon niveau) !';
		return code_retour;
	else	--On la sélectionne
		select into v_comp_e, v_nom_comp_e			
			pcomp_modificateur, comp_libelle	
		from perso_competences,competences
		where pcomp_perso_cod = lanceur
			and pcomp_pcomp_cod = v_comp_cod2
                        and pcomp_pcomp_cod = comp_cod;
	end if;
	
-- on controle les PA
	select into lanceur_pa
		perso_pa
	from perso
	where perso_cod = lanceur;
	
	if lanceur_pa < cout_pa then
		code_retour := 'Erreur : Vous n’avez pas assez de PA pour lancer ce sort !';
		return code_retour;
	end if;	
	
-- on controle les runes si type lancer = 0
	if type_lancer = 0 then
		for ligne_rune in
			select * from sort_rune where srune_sort_cod = num_sort
		loop
			select into compt_rune perobj_obj_cod
			from perso_objets,objets
			where perobj_perso_cod = lanceur
				and perobj_obj_cod = obj_cod
				and obj_gobj_cod = ligne_rune.srune_gobj_cod;
			if not found then
				select into nom_rune gobj_nom
				from objet_generique
				where gobj_cod = ligne_rune.srune_gobj_cod;

				code_retour := 'Erreur : Vous ne possédez pas de rune '||nom_rune||' nécessaire pour ce sort !';
				return code_retour;
			end if;
		end loop;
	end if;
	if type_lancer = 1 then
		select into compt_rune psort_cod from perso_sorts
		where psort_perso_cod = lanceur
			and psort_sort_cod = num_sort;
		if not found then
			code_retour := 'Erreur : Vous n’avez pas mémorisé le sort !';
			return code_retour;
		end if;
	end if;
	if type_lancer > 1 then
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
	if type_lancer < 0 then
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
-- on vérifie que le sort ait pas déjà été lancé plusieurs fois
	if not exists (select 1 from perso_nb_sorts where pnbs_perso_cod = lanceur and pnbs_sort_cod = num_sort) then
		insert into perso_nb_sorts (pnbs_cod,pnbs_perso_cod,pnbs_sort_cod,pnbs_nombre)
		values (nextval('seq_pnbs_cod'),lanceur,num_sort,0);
	end if;
	
	select into nb_sort,v_pnbs_cod pnbs_nombre,pnbs_cod from perso_nb_sorts
	where pnbs_perso_cod = lanceur
		and pnbs_sort_cod = num_sort;

	if nb_sort >= 2 then
		code_retour := 'Erreur : Vous ne pouvez pas lancer le même plus de 2 fois dans le même tour !';
		return code_retour;
	end if;
	
	update perso_nb_sorts
	set pnbs_nombre = pnbs_nombre + 1
	where pnbs_cod = v_pnbs_cod;
-----------------------------------------------------------
-- fin de contrôles 
------------------------------------------------------------
-- les controles semblent bons, on peut passer à la suite 
------------------------------------------------------------
	code_retour := code_retour||'<p>Vous avez tenté d’enluminer le sort <b>'||nom_sort||'</b> sur un parchemin vierge, ';
	code_retour := code_retour||'en utilisant la compétence <b>'||v_nom_comp_e||'</b>.<br><br>';
-- on rajoute le lancement du sort dans le total
	select into compt pnbst_cod from perso_nb_sorts_total
	where pnbst_perso_cod = lanceur
		and pnbst_sort_cod = num_sort;
	if not found then
		insert into perso_nb_sorts_total (pnbst_perso_cod,pnbst_sort_cod,pnbst_nombre)
		values (lanceur,num_sort,0);
	end if;
	
	update perso_nb_sorts_total
	set pnbst_nombre = pnbst_nombre + 1
	where pnbst_sort_cod = num_sort
		and pnbst_perso_cod = lanceur;	

-- on regarde s il y a concentration
	select into compt concentration_perso_cod from concentrations
		where concentration_perso_cod = lanceur;
	if found then
		v_comp_modifie := v_comp_m + 20;
		delete from concentrations where concentration_perso_cod = lanceur;
	else
		v_comp_modifie = v_comp_m;
	end if;
	--
-- modificateurs en fonction du niveau
--
	if type_lancer = 0 then
		v_malus_niveau := (2 - niveau_sort) * 10;
		select into nb_sort_niveau
			coalesce(sum(pnbst_nombre),0)
		from perso_nb_sorts_total,sorts
		where pnbst_perso_cod = lanceur
			and pnbst_sort_cod = sort_cod
			and sort_niveau = niveau_sort;
		if nb_sort_niveau is null then
			nb_sort_niveau := 0;
		end if;
		v_malus_niveau := v_malus_niveau + floor(nb_sort_niveau/15);
		if v_malus_niveau > 0 then
			v_malus_niveau := 0;
		end if;
		v_comp_modifie := v_comp_modifie + v_malus_niveau;
	end if;
--
-- on rajoute maintenant le morceau de choix : enluminure
--
	v_comp_modifie := round(v_comp_modifie * v_comp_e / 100);
--
-- fin modificateurs en fonction du niveau
--
	if v_comp_modifie < 1 then
		v_comp_modifie := 1;
	end if;
	v_special := floor(v_comp_modifie / 5);

 
	code_retour := code_retour || 'Votre chance de réussir (en tenant compte des modificateurs) est de <b>' || trim(to_char(v_comp_modifie, '9999')) || '</b> ';
-- on regarde si le sort est lancé
	des := lancer_des(1, 100);
	code_retour := code_retour || 'et votre lancer de dés est de <b>' || trim(to_char(des, '9999')) || '</b>.<br>';


	if des > 96 then
	-- echec critique
		if type_lancer = 0 then
		-- on enlève les runes
			for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
				compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
			end loop;
		end if;
		gain_renommee := gain_renommee * (-0.5);
		-- on enlève le parchemin vierge
		compt := f_del_objet(v_code_parch);
		code_retour := code_retour||'Il s’agit donc d’un échec automatique.<br><br>';
		update perso set perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee,perso_pa = perso_pa - (4 + bonus_pa) where perso_cod = lanceur;
		texte_evt := '[perso_cod1] a lamentablement échoué dans l’enluminure du sort '||nom_sort;
		perform insere_evenement(lanceur, lanceur, 77, texte_evt, 'O', '[num_sort]=' || num_sort::text);

		return code_retour;
	end if;
	if des > v_comp_modifie then
		gain_renommee := gain_renommee * (-0.2);
		update perso set perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee,perso_pa = perso_pa - (4 + bonus_pa) where perso_cod = lanceur;
		code_retour := code_retour||'Vous avez donc <b>échoué</b>.<br><br>';
		-- on regarde si on améliore la comp
		if v_comp_e <= getparm_n(1) then
			code_retour := code_retour || 'Votre compétence est inférieure à ' || trim(to_char(getparm_n(1),'9999')) || ' %. Vous tentez une amélioration.<br>';
			temp_ameliore_competence := ameliore_competence(lanceur, v_comp_cod2, v_comp_e);
			code_retour := code_retour || 'Votre lancer de dés est de <b>' || split_part(temp_ameliore_competence,';',1) || '</b>, ';
			if split_part(temp_ameliore_competence,';', 2) = '1' then
				code_retour := code_retour || 'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>' || split_part(temp_ameliore_competence,';',3) || '</b><br><br>.';
			else
				code_retour := code_retour || 'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
			end if;
		end if;
		if type_lancer = 0 then
			for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
				if lancer_des(1, 100) <= getparm_n(33) then
					compt := drop_rune(ligne_rune.srune_gobj_cod, lanceur);
				end if;
			end loop;
		end if;
		texte_evt := '[perso_cod1] a échoué dans l’enluminure du sort ' || nom_sort;
		perform insere_evenement(lanceur, lanceur, 77, texte_evt, 'O', '[num_sort]=' || num_sort::text);

		return code_retour;
	end if;
	if des <= 5 then
		code_retour := code_retour || 'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
		px_gagne := px_gagne + 1;
		cout_pa := floor(cout_pa / 2);
	else
			if des <= v_special then
				code_retour := code_retour || 'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
				cout_pa := cout_pa - 1;
			else
				code_retour := code_retour || 'Vous avez donc <b>réussi</b>.<br><br>';
			end if;
	end if;
-- on enlève les PA
	update perso set perso_pa = perso_pa - cout_pa, perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee where perso_cod = lanceur;
-- px
	if (type_lancer = 0) then
		px_gagne := px_gagne + niveau_sort - 1;
	else
		px_gagne := px_gagne + ((niveau_sort - 1) / 3.0::numeric);
	end if;
-- on tente l amélioration
	temp_ameliore_competence := ameliore_competence_px(lanceur, v_comp_cod2, v_comp_e);
	code_retour := code_retour || 'Votre jet d’amélioration est de <b>' || split_part(temp_ameliore_competence, ';', 1) || '</b>, ';
	if split_part(temp_ameliore_competence, ';', 2) = '1' then
		code_retour := code_retour || 'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>' || split_part(temp_ameliore_competence, ';', 3) || '</b>.<br><br>';
		px_gagne := px_gagne + 1;
	else
		code_retour := code_retour || 'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
	end if;
-- on supprime les runes si besoin est
	if type_lancer = 0 then
		for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
			compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
		end loop;
	end if;
-- on attribue les PX
	update perso set perso_px = perso_px + px_gagne where perso_cod = lanceur;
-- on regarde pour la mémorisation
	texte_memo := memo_sort(lanceur,num_sort);
	if split_part(texte_memo,';',1) = '-1' then
		code_retour := code_retour||'Vous ne pouvez pas mémoriser ce sort car vous avez atteint votre limite de mémorisation.<br>';
	end if;
	if split_part(texte_memo,';',1) = '1' then
		code_retour := code_retour||'Vous tentez de mémoriser le sort. Votre probabilité de mémorisation est de <b>'||split_part(texte_memo,';',2)||'</b>. ';
		code_retour := code_retour||'Votre lancer des dés est de <b>'||split_part(texte_memo,';',3)||'</b>.<br>';
		if split_part(texte_memo,';',4) = '1' then
			code_retour := code_retour||'Vous avez donc <b>mémorisé</b> ce sort.<br><br>';
			px_gagne := px_gagne + 1;
		else
			code_retour := code_retour||'Vous n’avez pas réussi à mémoriser ce sort.<br><br>';
		end if;
	end if;
	code_retour := code_retour||'Le sort est maintenant enluminé sur un parchemin.';
	code_retour := code_retour||'<br>Vous gagnez '||trim(to_char(px_gagne,'999999D99'))||' PX pour cette action.';
-- on efface le parchemin vierge
	compt := f_del_objet(v_code_parch);

-- on rajoute le parchemin valide
	select into nv_parch gobj_cod
	from objet_generique
	where gobj_sort_cod = num_sort;
	if not found then
		code_retour := code_retour||'<br>Erreur, parchemin type non trouvé! ! (désolé.... )';
		return code_retour;
	end if;

	texte_memo := cree_objet_perso_nombre(nv_parch,lanceur,1);
	texte_evt := '[perso_cod1] a enluminé le sort '||nom_sort;
	perform insere_evenement(lanceur, lanceur, 77, texte_evt, 'O', '[num_sort]=' || num_sort::text);

	return code_retour;
end;
$function$

