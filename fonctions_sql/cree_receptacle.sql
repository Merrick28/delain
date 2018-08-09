CREATE OR REPLACE FUNCTION public.cree_receptacle(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cree_receptable : met un sort dans un réceptavle     */
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
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	texte_memo text;				-- texte pour mémorisation
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	lanceur_pa integer;			-- pa du lanceur
	pos_lanceur integer;			-- position du lanceur
	v_comp integer;				-- valeur de compétence initiale
	v_comp_modifie integer;		-- valeur de compétence modifiée
	v_comp_cod integer;			-- comp_cod utilisée
	nom_comp text;					-- nom de la compétence utilisée
	px_gagne numeric;				-- px gagnes pour ce sort
	temp_renommee numeric;		-- calcul pour renommee
	pa_magie integer;				-- bonus en cout de lancer de sort
	v_nb_receptacle integer;	-- nombre de réceptacle du perso
	v_nb_rec_utilise integer;	-- nb de réceptacle utilisé
	v_malus_niveau integer;		-- malus lié au niveau
	v_bonus_malus integer;		-- limiteur au malus de niveau
	nb_sort_niveau integer; 	-- nombre de sorts du même niveau déjà lancés
        v_voie_magique integer;         -- voie magique du lanceur
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort alias for $2;		-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	distance_sort integer;		-- portée du sort
	nom_sort varchar(50);		-- nom du sort
	niveau_sort integer;			-- niveau du sort
	aggressif varchar(2);		-- sort aggressif ?
	temp integer;					-- fourre tout
	compt_rune integer;
	nom_rune text;
	nb_sort integer;
	v_pnbs_cod integer;
        v_special integer;            -- chance de faire un spécial
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	deb_res_controle text;		-- partie 1 du controle sort
	res_controle text;			-- totalité du contrôle sort
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	niveau_reussite integer;
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;		
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
-- sur le lanceur
	select into v_nb_receptacle perso_nb_receptacle
		from perso where perso_cod = lanceur;
	select into v_nb_rec_utilise count(recsort_cod)
		from recsort
		where recsort_perso_cod = lanceur;
	if v_nb_rec_utilise >= v_nb_receptacle then
		code_retour := code_retour||'<p>Erreur : tous les réceptacles sont utilisés !';
		return code_retour;
	end if;

-- sur le sort
	select into nom_sort,cout_pa,aggressif,niveau_sort sort_nom,sort_cout,sort_bloquable,sort_niveau from sorts where sort_cod = num_sort;
	if not found then
		code_retour := code_retour||'<p>Erreur : sort non trouvé !';
		return code_retour;
	end if;
	temp_renommee := ((niveau_sort-1)*0.1)::numeric;
-- sur la compétence
	select into 	v_comp,
						v_comp_cod,
						nom_comp
					pcomp_modificateur,
					pcomp_pcomp_cod,
					comp_libelle
		from perso_competences,sorts,competences
		where pcomp_perso_cod = lanceur
		and pcomp_pcomp_cod = sort_comp_cod
		and sort_cod = num_sort
		and pcomp_pcomp_cod = comp_cod;
	if not found then
		code_retour := code_retour||'<p>Erreur : infos compétence non trouvées !';
		return code_retour;
	end if;
-- on verifie les voies magiques
        select into v_voie_magique perso_voie_magique from perso
        where perso_cod = lanceur;
    -- les maitres du savoir pouvant lancer tous les sorts, on les exclues du test sauf pour le sort de familier sorcier qui reste uniquement pour les sorciers
  if v_voie_magique != 7 then 
        -- guerisseur
        if num_sort = 150 then
            if v_voie_magique != (1) then
                code_retour := 'Erreur vous n''etes pas guérisseur !';
		return code_retour;
            end if;
        end if;
        -- maitre des arcanes
        if num_sort = 146 then
            if v_voie_magique != 2 then
                code_retour := 'Erreur vous n''etes pas Maître des runes !';
		return code_retour;
            end if;
        end if;
        -- enchanteur runique
        if num_sort in(138,167) then
            if v_voie_magique != 5 then
                code_retour := 'Erreur vous n''etes pas enchanteur runique !';
		return code_retour;
            end if;
        end if;  
        -- Mage de guerre
        if num_sort = 152 then
            if v_voie_magique != 4 then
                code_retour := 'Erreur vous n''etes pas mage de guerre !';
		return code_retour;
            end if;
        end if;   
         -- Mage de bataille
        if num_sort in(153,168) then
            if v_voie_magique != 6 then
                code_retour := 'Erreur vous n''etes pas mage de bataille !';
		return code_retour;
            end if;
        end if;
         -- Sorcier
        if num_sort in(149,166)then
            if v_voie_magique != 3 then
                code_retour := 'Erreur vous n''etes pas sorcier !';
		return code_retour;
            end if;
        end if; 
 end if;
-- exclusion du sort familier sorcier
 if v_voie_magique != 7 then 
       if num_sort in(149)then
            if v_voie_magique != 3 then
                code_retour := 'Erreur vous n''etes pas sorcier !';
		return code_retour;
            end if;
        end if; 
 end if;    
-- on controle les PA
	select into cout_pa sort_cout from sorts where sort_cod = num_sort;
	select into lanceur_pa,pos_lanceur
		perso_pa,ppos_pos_cod
		from perso,perso_position
		where perso_cod = lanceur
		and ppos_perso_cod = lanceur;
	cout_pa := cout_pa + valeur_bonus(lanceur, 'PAM');
        if type_lancer != 4 and num_sort in(11,25,38,67,128,136,139) then
		cout_pa := cout_pa + valeur_bonus(lanceur, 'ERU');
         end if;
	if lanceur_pa < cout_pa then
		code_retour := 'Erreur : Vous n’avez pas assez de PA pour lancer ce sort !';
		return code_retour;
	end if;	
-- on controle les runes si type lancer = 0
	if type_lancer = 0 then
		for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
			select into compt_rune perobj_obj_cod from perso_objets,objets
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
	if not exists (select 1 from perso_nb_sorts
		where pnbs_perso_cod = lanceur
		and pnbs_sort_cod = num_sort) then
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
	code_retour := code_retour||'<p>Vous avez lancé le sort <b>'||nom_sort||'</b> dans un réceptacle, ';
	code_retour := code_retour||'en utilisant la compétence <b>'||nom_comp||'</b>.<br><br>';
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
		v_comp_modifie := v_comp + 20;
		delete from concentrations where concentration_perso_cod = lanceur;
	else
		v_comp_modifie = v_comp;
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
-- fin modificateurs en fonction du niveau
--
	if v_comp_modifie < 1 then
		v_comp_modifie := 1;
	end if;
-- on determine les chances de faire un spécial
v_special := floor(v_comp_modifie/5);

	code_retour := code_retour||'Votre chance de réussir (en tenant compte des modificateurs) est de <b>'||trim(to_char(v_comp_modifie,'9999'))||'</b> ';
-- on regarde si le sort est lancé
	des := lancer_des(1,100);
	code_retour := code_retour||'et votre lancer de dés est de <b>'||trim(to_char(des,'9999'))||'</b>.<br>';


	if des > 96 then
	-- echec critique
		if type_lancer = 0 then
		-- on enlève les runes
			for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
				compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
			end loop;
		end if;
		code_retour := code_retour||'Il s’agit donc d’un échec automatique.<br><br>';
		update perso set perso_renommee_magie = perso_renommee_magie - (temp_renommee*2),perso_pa = perso_pa - 4 where perso_cod = lanceur;
	texte_evt := '[perso_cod1] a lamentablement échoué dans la préparation du sort '||nom_sort;
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
     	values(nextval('seq_levt_cod'),28,now(),1,lanceur,texte_evt,'O','O',lanceur);
		return code_retour;
		-- renomme magique
	end if;
	if des > v_comp_modifie then
	-- sort loupé
		-- renomme magique
		update perso set perso_renommee_magie = perso_renommee_magie - temp_renommee,perso_pa = perso_pa - 4 where perso_cod = lanceur;
		code_retour := code_retour||'Vous avez donc <b>échoué</b>.<br><br>';
		-- on regarde si on améliore la comp
		if v_comp <= getparm_n(1) then
			code_retour := code_retour||'Votre compétence est inférieure à '||trim(to_char(getparm_n(1),'9999'))||' %. Vous tentez une amélioration.<br>';
			temp_ameliore_competence := ameliore_competence(lanceur,v_comp_cod,v_comp);
			code_retour := code_retour||'Votre lancer de dés est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
			if split_part(temp_ameliore_competence,';',2) = '1' then
				code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b><br><br>.';
			else
				code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
			end if;
		end if;
		if type_lancer = 0 then
			for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
				if lancer_des(1,100) <= getparm_n(33) then
					compt := drop_rune(ligne_rune.srune_gobj_cod,lanceur);
				end if;
			end loop;
		end if;
	texte_evt := '[perso_cod1] a échoué dans la préparation du sort '||nom_sort;
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
     	values(nextval('seq_levt_cod'),28,now(),1,lanceur,texte_evt,'O','O',lanceur);
		return code_retour;
	end if;
if des <= 5 then
			code_retour := code_retour||'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
			px_gagne := px_gagne + 1;
			cout_pa := floor(cout_pa/2);
		else
                      if des <= v_special then
                        code_retour := code_retour||'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
                        cout_pa := cout_pa - 1;
	              else
			code_retour := code_retour||'Vous avez donc <b>réussi</b>.<br><br>';
                      end if;
		end if;

	niveau_reussite := v_comp_modifie - des;
-- on enlève les PA
	update perso set perso_pa = perso_pa - cout_pa where perso_cod = lanceur;
-- renomme magique
	update perso set perso_renommee_magie = perso_renommee_magie + temp_renommee where perso_cod = lanceur;	
-- px
	if (type_lancer = 0) then
		px_gagne := px_gagne + niveau_sort - 1;
	else
		px_gagne := px_gagne + ((niveau_sort - 1)/3.0::numeric);
	end if;
-- on tente l amélioration
	temp_ameliore_competence := ameliore_competence_px(lanceur,v_comp_cod,v_comp);
	code_retour := code_retour||'Votre jet d’amélioration est de <b>'||split_part(temp_ameliore_competence,';',1)||'</b>, ';
	if split_part(temp_ameliore_competence,';',2) = '1' then
		code_retour := code_retour||'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>'||split_part(temp_ameliore_competence,';',3)||'</b>.<br><br>';
		px_gagne := px_gagne + 1;
	else
		code_retour := code_retour||'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
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
	code_retour := code_retour||'Le sort est maintenant dans un réceptacle.';
	code_retour := code_retour||'<br>Vous gagnez '||trim(to_char(px_gagne,'999999D99'))||' PX pour cette action.';
	insert into recsort
		(recsort_perso_cod,recsort_sort_cod,recsort_reussite)
		values
		(lanceur,num_sort,niveau_reussite);	
	texte_evt := '[perso_cod1] a préparé le sort '||nom_sort;
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
     	values(nextval('seq_levt_cod'),28,now(),1,lanceur,texte_evt,'O','O',lanceur);
	return code_retour;
end;
$function$

