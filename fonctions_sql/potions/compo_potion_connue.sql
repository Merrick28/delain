--
-- Name: compo_potion_connue(integer, integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.compo_potion_connue(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*********************************************************/
/* function compo_potions_connue                         */
/* Création d’une potion connue, à partir d’un flacon    */
/*  vide                                                 */
/* parametres :                                          */
/*  $1 = personnage qui compose la potion                */
/*  $2 = potion à réaliser                               */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/*********************************************************/
/* 25/01/2013 : Ajout d’événements de réussite / échec   */
/*********************************************************/
declare
	personnage alias for $1;-- perso_cod
	potion alias for $2;    -- obj_cod
	code_retour text;       -- code retour
	pluriel text;
	ligne record;
	lignesup record;
	v_compo integer;        -- Composant qui va être consommé
	v_type_flacon integer;  --TYPE de fiole
	v_nombre integer;       --nombre de composants disponible en inventaire
	v_temp integer;         --fourre tout
	v_nouvelle integer;     --indique une nouvelle potion (afin de traiter des messages de type différent en retour)
	v_cree_monstre integer; --Crée une gelée
	pos_personnage integer; --position du personnage
	des integer;
	v_pv integer;
	compt integer;		-- valeur de la concentration
	alchimie integer;	-- valeur de la compétence en alchimie du perso
	alchimie_modif integer;	-- valeur de la compétence en alchimie du perso après modifications
	comp_alchimie integer;	-- Compétence en alchimie du perso (Niv1, 2 ou 3)
	resultat text;		--texte d’amélioration de la comp

	v_gobj_cod integer;	-- code de l’objet générique
	v_frmpr_num integer;	-- nombre de potion à créer
	v_obj_cod integer;	-- obj_cod de la potion
	v_nom_potion text;	-- nom de la potion
	v_description_potion text; -- nom de la potion
	v_valeur_potion integer; -- valeur de la potion
	v_pa integer;           -- PA de l’utilisateur
	v_stabilite integer;    -- stabilite de la potion
	v_des_stabilite integer;-- lancer de dés sur la stabilité
	v_texte_stabilite text; -- texte lié à l’instabilité de la potion
	v_bonus_existant integer;-- bonus existant ?
	gain_renommee numeric;  -- gain (ou perte) de renommée artisanale
	texte_evt text;

begin
/*********************************************************/
/*        I N I T I A L I S A T I O N S                  */
/*********************************************************/
	code_retour := '0;'; --par défaut, tout va bien
	gain_renommee := 2;
/*********************************************************/
/*                  C O N T R O L E S                    */
/*********************************************************/
	-- controle sur la possession d’un flacon vide
	select into v_obj_cod, v_type_flacon
		obj_cod, obj_gobj_cod
		from objets, perso_objets
		where perobj_perso_cod = personnage
		and obj_cod = perobj_obj_cod
		and obj_gobj_cod = 412
		limit 1;
	if not found then
		return '<br>1;Erreur ! Vous ne possédez pas de flacon vide pour réaliser cette potion !';
	end if;

	-- contrôle sur la possession des un composants nécessaires
	for ligne in
		select frmco_gobj_cod, frmco_num, gobj_nom
		from formule_composant, formule, formule_produit, objet_generique
		where frm_cod = frmco_frm_cod
			and frm_cod = frmpr_frm_cod
			and frmco_gobj_cod = gobj_cod
			and frmpr_gobj_cod = potion
	loop
		select into v_compo, v_nombre
			obj_cod, count(obj_cod)
		from objets, perso_objets
		where perobj_perso_cod = personnage
			and perobj_obj_cod = obj_cod
			and obj_gobj_cod = ligne.frmco_gobj_cod
		group by obj_cod;

		if not found then
			return '1;Erreur ! Vous ne possédez pas un des types de composant nécessaire à cette recette !';
		end if;
		if v_nombre > ligne.frmco_num then
			return '<br>1;Erreur ! Vous ne pouvez pas constituer cette potion. Il vous manque des composants, notamment le composant ' || ligne.gobj_nom || ' !';
		end if;
	end loop;

	-- Jet de compétence et amélioration
	select into alchimie, comp_alchimie
		pcomp_modificateur, pcomp_pcomp_cod
	from perso_competences
	where pcomp_perso_cod = personnage and pcomp_pcomp_cod in (97, 100, 101);

	-- on regarde s il y a concentration
	select into compt concentration_perso_cod
	from concentrations
	where concentration_perso_cod = personnage;
	if found then
		alchimie_modif := alchimie + 20;
		delete from concentrations where concentration_perso_cod = personnage;
	else
		alchimie_modif := alchimie;
	end if;

	des := lancer_des(1, 100);
	code_retour := code_retour || 'Votre chance de réussir (en tenant compte des modificateurs) est de <b>' || trim(to_char(alchimie_modif, '9999')) || '</b> ';
      	code_retour := code_retour || 'et votre lancer de dés est de <b>' || trim(to_char(des, '9999')) || '</b>.<br>';

	if des > 96 then
	-- echec critique
		code_retour := code_retour || 'Il s’agit donc d’un échec automatique.<br><br>';
		gain_renommee := gain_renommee * (-0.5);
		update perso
		set perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
		where perso_cod = personnage;

		texte_evt := '[perso_cod1] a tenté de créer une potion et a échoué.';
		perform insere_evenement(personnage, personnage, 91, texte_evt, 'O', NULL);

		return code_retour;
	end if;
	if des > alchimie_modif then
		code_retour := code_retour || 'Vous avez donc <b>échoué</b>.<br><br>';
		gain_renommee := gain_renommee * (-0.5);

		if alchimie <= getparm_n(1) then -- amélioration auto
			code_retour := code_retour || 'Votre compétence est inférieure à ' || trim(to_char(getparm_n(1), '9999')) || ' %. Vous tentez une amélioration.<br>';
			resultat := ameliore_competence_px(personnage, comp_alchimie, alchimie);
			code_retour := code_retour || 'Votre jet d’amélioration est de ' || split_part(resultat, ';', 1) || ', '; -- pos 7 8 9 10
			if split_part(resultat, ';', 2) = '1' then
				code_retour := code_retour || 'vous avez donc <b>amélioré</b> cette compétence. <br>';
				code_retour := code_retour || 'Sa nouvelle valeur est ' || split_part(resultat, ';', 3) || '<br>';
				code_retour := code_retour || 'Vous gagnez 1 PX.' || split_part(resultat, ';', 3) || '<br><br>';
			else
				code_retour := code_retour || 'vous n’avez pas amélioré cette compétence.<br><br> ';
			end if;
		end if;
		update perso
		set perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
		where perso_cod = personnage;

		texte_evt := '[perso_cod1] a tenté de créer une potion et a échoué.';
		perform insere_evenement(personnage, personnage, 91, texte_evt, 'O', NULL);

		return code_retour;
	end if;

	-- Compétence réussie
	code_retour := code_retour || 'Vous avez donc <b>réussi</b>.<br><br>';
	resultat := ameliore_competence_px(personnage, comp_alchimie, alchimie);
	code_retour := code_retour || 'Votre jet d’amélioration est de ' || split_part(resultat, ';', 1) || ', '; -- pos 7 8 9 10
	if split_part(resultat, ';', 2) = '1' then
		code_retour := code_retour || 'vous avez donc <b>amélioré</b> cette compétence. <br>';
		code_retour := code_retour || 'Sa nouvelle valeur est ' || split_part(resultat, ';', 3) || '<br>';
		code_retour := code_retour || 'Vous gagnez 1 PX.' || split_part(resultat, ';', 3) || '<br><br>';
	else
		code_retour := code_retour || 'vous n’avez pas amélioré cette compétence.<br><br> ';
	end if;
--  fin d’amélioration des compétences

	code_retour := code_retour || '<br>Vous avez tous les composants nécessaires à la confection de cette potion.
		<br>Ceux-ci sont consommés pour la fabrication : ';
	/* On relance une boucle pour supprimer les composants*/

	for ligne in
		select frmco_gobj_cod, frmco_num, gobj_nom
		from formule_composant, formule, formule_produit, objet_generique
		where frm_cod = frmco_frm_cod
			and frm_cod = frmpr_frm_cod
			and frmco_gobj_cod = gobj_cod
			and frmpr_gobj_cod = potion
	loop
		pluriel = '';
		if ligne.frmco_num > 1 then
			pluriel = 's';
		end if;
		code_retour := code_retour || ligne.frmco_num::text || ' dose' || pluriel || ' de ' || ligne.gobj_nom || ', ';

		for lignesup in
			select obj_cod, obj_nom
			from objets, perso_objets
			where perobj_perso_cod = personnage
				and perobj_obj_cod = obj_cod
				and obj_gobj_cod = ligne.frmco_gobj_cod
			limit ligne.frmco_num
		loop
			v_temp := f_del_objet (lignesup.obj_cod);
		end loop;
	end loop;

	/* On transforme la fiole vide en potion*/
	select into v_nom_potion, v_description_potion, v_valeur_potion
		gobj_nom, gobj_description, gobj_valeur
	from objet_generique
	where gobj_cod = potion;

	update objets
	set obj_nom = v_nom_potion,
		obj_description = v_description_potion,
		obj_valeur = v_valeur_potion,
		obj_gobj_cod = potion
	where obj_cod = v_obj_cod;

  -- creation de plusieurs potions avec les mêmes produits
  select into v_frmpr_num frmpr_num from formule_produit where frmpr_gobj_cod  = potion ;
  if v_frmpr_num>1 then
    perform cree_objet_perso_nombre(potion,personnage,v_frmpr_num-1);
    code_retour := code_retour || '<br>Les composants vous on permis de créer <b>' || v_frmpr_num::text || ' potions</b>.';
  end if;

/*********************************************************/
/*                  Gestion des effets / messages        */
/*********************************************************/

	select into pos_personnage, v_pv
		ppos_pos_cod, perso_pv
	from perso_position, perso
	where ppos_perso_cod = personnage
		and ppos_perso_cod = perso_cod;

	des := lancer_des(1, 100);
	if des <= 1 and des < 5 then
		code_retour := code_retour || '<br>Une petite fumée blanche s’échappe du flacon';
	elsif des <= 5 and des < 10 then
		code_retour := code_retour || '<br>Une petite fumée bleue s’échappe du flacon';
	elsif des <= 10 and des < 15 then
		code_retour := code_retour || '<br>Une petite fumée rouge s’échappe du flacon';
	elsif des <= 15 and des < 20  then
		code_retour := code_retour || '<br>Une petite fumée jaune s’échappe du flacon';
	elsif des <= 20 and des < 25 then
		code_retour := code_retour || '<br>Vous sentez une odeur âcre';
	elsif des <= 25 and des < 30 then
		code_retour := code_retour || '<br>Vous sentez une odeur sucrée';
	elsif des <= 30 and des < 35 then
		code_retour := code_retour || '<br>Le mélange se met à bouillir doucement, puis revient à la normal';
	elsif des <= 35 and des < 40 then
		code_retour := code_retour || '<br>un sifflement se produit !';
	elsif des <= 40 and des < 45 then
		code_retour := code_retour || '<br>Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
		v_cree_monstre := cree_monstre_pos(31, pos_personnage);
	elsif des <= 45 and des < 50 then
		code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
		v_cree_monstre := cree_monstre_pos(32, pos_personnage);
	elsif des = 50 then
		code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
		v_cree_monstre := cree_monstre_pos(33, pos_personnage);
	elsif des = 51 then
		code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
		v_cree_monstre := cree_monstre_pos(34, pos_personnage);
	elsif des = 52 then
		code_retour := code_retour || '<br>Rien ne se produit au premier abord... puis une bulle, puis une autre... Le mélange se met à bouillir, et tout d’un coup un corps commence à se former, et à sortir du flacon. Arrivé au sol, vous pouvez distinguer... une gelée !!';
		v_cree_monstre := cree_monstre_pos(46, pos_personnage);
	elsif des = 53 then
		code_retour := code_retour || '<br>Une petite explosion se produit ! Heureusement qu’elle reste limitée à l’intérieur de la fiole !';
	elsif des <= 54 and des < 56 then
		code_retour := code_retour || '<br>Pendant quelques instants, rien ne se passe... Et tout d’un coup, le mélange explose vous provoquant quelques contusions mineures';
		v_pv := v_pv - 4;
		if v_pv <= 0 then
			v_temp = tue_perso_final(personnage);
		else
			update perso set perso_pv = v_pv where perso_cod = personnage;
		end if;
	elsif des <= 56 and des < 58 then
		code_retour := code_retour || '<br>Vous lachez le flacon qui devenait brûlant ! Malheureusement, ce n’était pas que l’enveloppe qui l’était, et un peu de mélange se répand sur vous.';
		v_pv := v_pv - 6;
		if v_pv <= 0 then
			v_temp = tue_perso_final(personnage);
		else
			update perso set perso_pv = v_pv where perso_cod = personnage;
		end if;
	elsif des = 58 then
		code_retour := code_retour || '<br>Le liquide se solidifie, et commence à se mouvoir, puis à se structurer, prenant toute la place dans le flacon. Le verre ne résiste pas longtemps, et des bras et des jambes se forment ! Vous venez d’assister à la naissance d’un farfadet !!';
		v_cree_monstre := cree_monstre_pos(355, pos_personnage);
	elsif des = 59 then
		code_retour := code_retour || '<br>Le liquide se solidifie, et commence à se mouvoir, puis à se structurer, prenant toute la place dans le flacon. Le verre ne résiste pas longtemps, et des bras et des jambes se forment ! Vous venez d’assister à la naissance d’un farfadet !!';
		v_cree_monstre := cree_monstre_pos(223, pos_personnage);
	elsif des = 60 then
		code_retour := code_retour || '<br>Le liquide se solidifie, et commence à se mouvoir, puis à se structurer, prenant toute la place dans le flacon. Le verre ne résiste pas longtemps, et des bras et des jambes se forment ! Vous venez d’assister à la naissance d’un farfadet !!';
		v_cree_monstre := cree_monstre_pos(245, pos_personnage);
	else
		code_retour := code_retour;
	end if;
	code_retour := code_retour || '<br>Vous parvenez à préparer la potion ! Cela pourra prendre un peu de temps quand même, mais il vaut mieux être patient, les accidents sont si vite arrivés...';

	update perso set perso_px = perso_px + 1.5,
		perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
	where perso_cod = personnage;

	texte_evt := '[perso_cod1] a réalisé une ' || v_nom_potion;
	perform insere_evenement(personnage, personnage, 91, texte_evt, 'O', '[gobj_cod]=' || potion::text);

	return code_retour;
end;$_$;


ALTER FUNCTION potions.compo_potion_connue(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION compo_potion_connue(integer, integer); Type: COMMENT; Schema: potions; Owner: delain
--

COMMENT ON FUNCTION potions.compo_potion_connue(integer, integer) IS 'Création d’une potion connue.';