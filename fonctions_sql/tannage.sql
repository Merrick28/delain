CREATE OR REPLACE FUNCTION public.tannage(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function tannage                                      */
/* Création d’un parchemin à partie d’une peau de monstre*/
/* en cours                                              */
/* parametres :                                          */
/*  $1 = personnage qui tanne la peau                    */
/*  $2 = peau concernée                                  */
/*  $3 = parchemin                                       */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**************************************************************/
/*	06/04/2012 - Reivax - Rajout d’une usure sur le tannage, histoire de
             permettre la réutilisation d’une grande peau pour une autre opération.*/
/**************************************************************/
declare
	personnage alias for $1;	-- perso_cod
	objet alias for $2;				-- gobj_cod du type de peau
	parchemin alias for $3;   -- parchemin qui est tenté (gobj_cod)
	code_retour text;				-- code retour
	v_compo integer;				-- Composant qui va être consommé
	v_compo_etat integer;				-- usure courante du composant
	pos_personnage integer;		--position du personnage
	des integer;
	v_pv integer;
	v_pa integer;					-- PA de l’utilisateur
	v_competence integer; --compétence de l’enlumineur
	v_niv_competence integer;	--pourcentage de la compétence3
	v_niv_competence_modif integer;
	v_gobj_cod integer;			-- code de l’objet générique
	v_usure integer;			-- l'usure infligée à l’objet
	v_obj_cod integer;			-- obj_cod de la potion
	v_nom text;			-- nom du parchemin (formule) produit
	v_temps integer; -- Temps de production
	v_heure integer; -- temps de production en heures
	v_nombre integer; --nombre de parchemin produit
	v_comp integer; -- compétence nécessaire pour produire ce type de parchemin
	v_niveau_parchemin integer;
	v_niveau_peau integer;
	chance integer;
	texte_evt text;
	texte_tannage text;
	px_gagne numeric;
	temp_ameliore_competence text;
	v_special integer;
	param_pa integer;
	v_temp_text text;
	v_temp integer;
	compt integer;
	cout_pa integer;
	v_code_formule integer;
	v_max integer;
	peau_tannee integer;
	i integer;
	temp_renommee numeric;		-- calcul pour renommee

begin
/*********************************************************/
/*        I N I T I A L I S A T I O N S                  */
/*********************************************************/
	code_retour := '0;'; --par défaut, tout va bien
	px_gagne := 0;
/*********************************************************/
/*                  C O N T R O L E S                    */
/*********************************************************/
	-- controle sur la possession d’un composant du type indiqué
	select into v_compo, v_compo_etat, v_niveau_peau
		obj_cod, obj_etat, gobj_niv_peau
	from objets, perso_objets, objet_generique
	where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = objet
		and gobj_cod = obj_gobj_cod
	order by obj_etat
	limit 1;
	if not found then
		return '1;1;Erreur ! Vous ne possédez pas de peau de monstre de ce type !';
	end if;
	--Controle des PA
	select into v_pa, param_pa, v_competence, v_niv_competence
		perso_pa, getparm_n(117), pcomp_pcomp_cod, pcomp_modificateur
	from perso, perso_competences
	where perso_cod = personnage
		and pcomp_perso_cod = perso_cod
		and pcomp_pcomp_cod in (91, 92, 93);
	if not found then
		return '1;1;Erreur ! Vous ne possédez pas la compétence nécessaire à cette action !';
	end if;
	if v_pa < param_pa then
		return '1;1;Erreur ! Vous ne possédez pas suffisamment de PA pour cette action !';
	end if;

	--Controler le niveau de l’enlumineur par rapport au parchemin à produire
	select into v_code_formule, v_nom, v_temps, v_nombre, v_comp, v_niveau_parchemin, v_usure
		frm_cod, frm_nom, frm_temps_travail, frmpr_num, frm_comp_cod, gobj_niv_parchemin, frmco_usure
	from formule_produit, formule_composant, formule, objet_generique, perso_competences
	where frmpr_frm_cod = frm_cod
		and frmco_gobj_cod = objet
		and frmco_frm_cod = frm_cod
		and frmpr_gobj_cod = parchemin
		and frm_type = 4
		and gobj_cod = frmpr_gobj_cod
		and pcomp_pcomp_cod in (91, 92, 93)
		and pcomp_pcomp_cod = frm_comp_cod
		and pcomp_perso_cod = personnage;
	if v_comp > v_competence then
		return '1;1;Erreur ! Vous ne possédez pas une compétence suffisamment élevée pour produire ce type de parchemin !';
	end if;

	-- Gestion de l’usure
	v_compo_etat := v_compo_etat - v_usure;
	if v_compo_etat <= 0 then
		v_temp := f_del_objet(v_compo);
		texte_tannage := '<br>Cette opération a requis l’intégralité du support.';
	else
		update objets set obj_etat = v_compo_etat where obj_cod = v_compo;
		texte_tannage := '<br>Vous arrivez à conserver une partie du support lors de l’opération.';
	end if;
	-- Fin gestion usure

	--On regarde maintenant si le perso y arrive
	temp_renommee := ((v_niveau_parchemin/10)*0.1)::numeric;
	des := lancer_des(1, 100);

	-- on regarde s il y a concentration
	v_niv_competence_modif := v_niv_competence;
	select into compt concentration_perso_cod from concentrations
	where concentration_perso_cod = personnage;
	if found then
		v_niv_competence_modif := v_niv_competence_modif + 20;
		delete from concentrations where concentration_perso_cod = personnage;
	end if;


	-- Modif Morgenese : le * 3.5 est bcp trop je remplace par 1.5
        -- sachant que les niveaux de parcho sont 40 - 60 - 80 et 100
        -- et les niveaux de peau 20 - 40 - 60 - 80 - 100 - 120
	chance := floor(1.5*v_niveau_parchemin) - v_niveau_peau;
	v_niv_competence_modif := (v_niv_competence_modif - chance);
	v_max := floor((v_niveau_peau * 2000 / v_niveau_parchemin)/100);
	v_niv_competence_modif := max(v_niv_competence_modif, v_max);
	code_retour := code_retour || '<br>Votre chance de réussite en tenant compte des modificateurs est de <b>' || trim(to_char(v_niv_competence_modif, '9999')) || '</b>.
		<br>Votre lancer de dés est de <b>' || trim(to_char(des, '9999')) || '</b>.<br>';
	v_special := floor(v_niv_competence_modif/4);
	if des > 96 then
		-- echec critique
		texte_evt := '[perso_cod1] a tenté de fabriquer un parchemin vierge et a lamentablement échoué.';

		insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(nextval('seq_levt_cod'), 83, now(), 1, personnage, texte_evt, 'O', 'O', personnage, personnage);

		code_retour := code_retour || 'Il s’agit donc d’un échec automatique.<br><br>';
		code_retour := '0;' || code_retour || texte_tannage;
		update perso set perso_renommee_artisanat = perso_renommee_artisanat - (temp_renommee*2), perso_pa = perso_pa - param_pa where perso_cod = personnage;
		return code_retour;
	end if;
	if des > v_niv_competence_modif then
		code_retour := code_retour || 'Vous avez donc <b>échoué</b>.<br><br>';
		-- on regarde si on améliore la comp
		if v_niv_competence <= getparm_n(1) then
			code_retour := code_retour || 'Votre compétence est inférieure à ' || trim(to_char(getparm_n(1), '9999')) || ' %. Vous tentez une amélioration.<br>';
			temp_ameliore_competence := ameliore_competence_px(personnage, v_competence, v_niv_competence);
			code_retour := code_retour || 'Votre lancer de dés est de <b>' || split_part(temp_ameliore_competence, ';', 1) || '</b>, ';
			if split_part(temp_ameliore_competence, ';', 2) = '1' then
				code_retour := code_retour || 'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>' || split_part(temp_ameliore_competence, ';', 3) || '</b><br><br>.';
			else
				code_retour := code_retour || 'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
			end if;
		end if;
		texte_evt := '[perso_cod1] a tenté de fabriquer un parchemin vierge et a échoué.';

		insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(nextval('seq_levt_cod'), 83, now(), 1, personnage, texte_evt, 'O', 'O', personnage, personnage);

		code_retour := '0;' || code_retour || texte_tannage;
		update perso set perso_renommee_artisanat = perso_renommee_artisanat - temp_renommee, perso_pa = perso_pa - param_pa where perso_cod = personnage;
		return code_retour;
	end if;

	texte_tannage := '<br>Vous réalisez votre opération de tannage, qui va prendre du temps, principalement pour le séchage.' || texte_tannage;

	if des <= 5 then
		code_retour := code_retour || 'il s’agit donc d’une <b>réussite critique</b>.<br><br>';
		px_gagne := px_gagne + 2;
		cout_pa := floor(param_pa/2);
	else
		if des <= v_special then
			code_retour := code_retour || 'il s’agit donc d’une <b>réussite spéciale</b>.<br><br>';
			px_gagne := px_gagne + 1;
		else
			code_retour := code_retour || 'Vous avez donc <b>réussi</b>.<br><br>';
		end if;
	end if;
	-- px
	px_gagne := px_gagne + (((v_niveau_parchemin/10) - 1)/3.0::numeric);
	-- on tente l amélioration
	temp_ameliore_competence := ameliore_competence_px(personnage, v_competence, v_niv_competence);
	code_retour := code_retour || 'Votre jet d’amélioration est de <b>' || split_part(temp_ameliore_competence, ';', 1) || '</b>, ';
	if split_part(temp_ameliore_competence, ';', 2) = '1' then
		code_retour := code_retour || 'Vous avez amélioré cette compétence. Sa nouvelle valeur est <b>' || split_part(temp_ameliore_competence, ';', 3) || '</b>.<br><br>';
	else
		code_retour := code_retour || 'Vous n’avez pas réussi à améliorer cette compétence.<br><br>';
	end if;
	if split_part(temp_ameliore_competence, ';', 2) = '1' then
		px_gagne := px_gagne + 1;
	end if;
	texte_evt := '[perso_cod1] est parvenu à fabriquer un parchemin vierge.';

 	insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
	values(nextval('seq_levt_cod'), 83, now(), 1, personnage, texte_evt, 'O', 'O', personnage, personnage);

	--On introduit une peau tannée dans l’équipement du perso
	v_temp_text := cree_objet_perso_nombre(846, personnage, v_nombre);
	--On insère dans la table des formules objets inachevés cette nouvelle occurence
	for i in 1..v_nombre loop
		peau_tannee := split_part(v_temp_text, ';', i);
		if peau_tannee is null then
			exit;
		else
			insert into formule_objet_inacheve (foi_obj_cod, foi_gobj_cod, foi_formule_cod) values (peau_tannee, parchemin, v_code_formule);
		end if;
	end loop;
	v_heure := floor(v_temps/60);
	code_retour := code_retour || 'Votre peau sera prête pour une enluminure dans approximativement ' || trim(to_char(v_heure, '99999999')) || ' heures.<br><br>';
	-- on attribue les PX
	update perso set perso_px = perso_px + px_gagne, perso_renommee_artisanat = perso_renommee_artisanat + temp_renommee, perso_pa = perso_pa - param_pa where perso_cod = personnage;
	code_retour := code_retour || '<br>Pour cette action, vous gagnez ' || to_char(px_gagne, '999999990D99') || ' PX.';
	code_retour := '1;' || code_retour || texte_tannage;
	return code_retour;
end;$function$

