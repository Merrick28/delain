CREATE OR REPLACE FUNCTION public.rappel_partiel_coterie(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
 STRICT
AS $function$/*****************************************************************/
/* function rappel_partiel_coterie :                             */
/*  Tente de ramener un personnage mort à sa coterie    	     */
/* On passe en paramètres                                        */
/*   $1 = perso appelé                                           */
/*   $2 = perso à rejoindre                                      */
/* Le code sortie du text HTML 									 */
/*****************************************************************/
/* Créé le 21/04/2012                                            */
/* Liste des modifications :                                     */
/* 21/06/2012 Reivax augmentation du nombre total de points en   */
/*   fonction de la taille de la coterie                         */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;               -- code html de retour
-------------------------------------------------------------
-- variables concernant le lanceur ou la cible  
-------------------------------------------------------------
	v_perso_cible 		alias for $1;		-- perso_cod du décédé
	v_perso_source 		alias for $2;		-- perso_cod du perso appeleur
	v_coterie_source	integer;		-- groupe_cod de la coterie du perso rappelant
	v_taille_coterie	integer;		-- Taille de la coterie
	v_perso_pa		integer;		-- PA du lanceur
	v_dieu			integer;		-- Dieu_cod
	v_dieu_rang		integer;		-- Rang moyen des deux protagonistes dans la religion
	v_temp			integer;		-- variable multi-usage pour les vérifications
	v_temp_str		character varying(1);	-- variable multi-usage pour les vérifications
	etage_gm		integer;		-- Valeur de l’étage du garde manger
	v_compteur_rappel	integer;		-- Valeur du compteur de rappel
	v_total_necessaire	integer;		-- Nombre de points à accumuler pour effectuer le rappel
	v_increment_rappel	integer;		-- Valeur de l’incrément du rappel
	v_cout_PA		integer;		-- Coût en PA
	v_pos_retour		integer;		-- Position de retour
	v_arene			character(1);		-- Indique si l’étage de destination est une arène
	texte_evt		text;			-- Texte pour l’événement

begin
	--
	-- Initialisation
	--
	etage_gm		:= 90;
	v_cout_PA 		:= 6;
	v_increment_rappel	:= 6;	-- Est réévalué en fonction de la taille de la coterie, peut descendre jusqu’à 3.
	v_total_necessaire	:= 0;	-- cf plus bas : égal au niveau du perso cible
	code_retour		:= '';

	--    
	-- Vérifications
	--

	-- PA
	select into v_perso_pa, v_temp
		perso_pa, perso_type_perso
		from perso where perso_cod = v_perso_source;
	if not found then
		code_retour := '<p><b>Erreur !</b> Lanceur inconnu.</p>';
		return code_retour;
	elseif v_temp <> 1 then
		code_retour := '<p><b>Erreur !</b> Les monstres et familiers ne peuvent pas rappeler leurs compagnons...</p>';
		return code_retour;
	elseif v_perso_pa < v_cout_PA then
		code_retour := '<p><b>Erreur !</b> Vous n’avez pas assez de PA pour réaliser cette action (6 PA nécessaires).</p>';
		return code_retour;
	end if;

	-- Coterie du perso source
	select into v_coterie_source
		pgroupe_groupe_cod
		from groupe_perso
		where pgroupe_perso_cod = v_perso_source and pgroupe_statut = 1;
	if not found then
		code_retour := '<p><b>Erreur !</b> Vous n’appartenez à aucune coterie.</p>';
		return code_retour;
	end if;

	-- Coterie active du perso cible
	select into v_temp
		pgroupe_groupe_cod
		from groupe_perso
		where pgroupe_perso_cod = v_perso_cible
			and pgroupe_statut = 1;
	if found then
		code_retour := '<p><b>Erreur !</b> La cible a déjà réintégré une coterie.</p>';
		return code_retour;
	end if;

	-- Coterie en suspend du perso cible
	select into v_compteur_rappel
		pgroupe_valeur_rappel
		from groupe_perso
		where pgroupe_perso_cod = v_perso_cible
			and pgroupe_statut = 2 and pgroupe_groupe_cod = v_coterie_source;
	if not found then
		code_retour := '<p><b>Erreur !</b> La cible ne peut plus être rappelée.</p>';
		return code_retour;
	end if;
        -- impalpabilité de la source
       	select into v_temp_str
		perso_tangible
		from perso
		where perso_cod = v_perso_source;
	if v_temp_str <> 'O' then
		code_retour := '<p><b>Erreur !</b> vous êtes impalpable, vous ne pouvez donc ramener votre compagnon.</p>';
		return code_retour;
	end if;

	-- impalpabilité de la cible
	select into v_temp_str, v_total_necessaire, v_temp
		perso_tangible, perso_niveau, perso_type_perso
		from perso
		where perso_cod = v_perso_cible;
	if v_temp_str <> 'N' then
		code_retour := '<p><b>Erreur !</b> La cible ne peut plus être rappelée.</p>';
		return code_retour;
	end if;
	if v_temp <> 1 then
		code_retour := '<p><b>Erreur !</b> Seul un aventurier peut être rappelé.</p>';
		return code_retour;
	end if;
	
	-- Garde manger, arènes.
	select into v_temp, v_arene
		pos_etage, etage_arene
		from perso_position
		inner join positions on pos_cod = ppos_pos_cod
		inner join etage on etage_numero = pos_etage
		where ppos_perso_cod = v_perso_source;
	if v_temp = etage_gm then
		code_retour := '<p><b>Erreur !</b> Vous êtes au garde-manger, et ne pouvez pas y rappeler de compagnons.</p>';
		return code_retour;
	end if;
	if v_arene = 'O' then
		code_retour := '<p><b>Erreur !</b> Vous êtes dans une arène, et ne pouvez pas y rappeler de compagnons.</p>';
		return code_retour;
	end if;

	--    
	-- Exécution du rappel
	--

	-- Taille de la coterie, pour détermination de l’incrément
	select into v_taille_coterie
		count(*)
		from groupe_perso
		where pgroupe_groupe_cod = v_coterie_source and pgroupe_statut = 1;
	v_increment_rappel := v_increment_rappel - (min(4, v_taille_coterie / 15));

	-- Bonus supplémentaires en fonction de la religion
	select into v_dieu
		dper_dieu_cod
		from dieu_perso
		where dper_perso_cod = v_perso_source;
	if found then
		select into v_dieu
			dper_dieu_cod
			from dieu_perso
			where dper_perso_cod = v_perso_cible and dper_dieu_cod = v_dieu;
		if found then
			-- Les deux persos sont de la même religion, on leur octroie un petit bonus.
			select into v_dieu_rang
				floor(AVG(dper_niveau))
				from dieu_perso
				where dper_perso_cod IN (v_perso_cible, v_perso_source);
			v_increment_rappel := v_increment_rappel + v_dieu_rang;
			code_retour := code_retour || '<p>Votre dieu voyant vos prières pour votre compagnon mort vous donne un petit coup de pouce.</p>';
		end if;
	end if;

	-- événements de la prière
	texte_evt := '[attaquant] a prié pour ramener [cible] à ses côtés.';
	insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
	values(29, now(), 1, v_perso_cible, texte_evt, 'N', 'N', v_perso_source, v_perso_cible);
	insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
	values(29, now(), 1, v_perso_source, texte_evt, 'O', 'N', v_perso_source, v_perso_cible);
	
	-- Incrément du compteur
	v_compteur_rappel := v_compteur_rappel + v_increment_rappel;
	
	-- Résolution
	if v_compteur_rappel >= v_total_necessaire then
		v_pos_retour := rappel_coterie(v_perso_cible, v_perso_source);
		
		if v_pos_retour = -1 then
			code_retour := '<p><b>Erreur !</b> Aucune position n’a été trouvée pour rappeler votre compagnon...</p>';
			return code_retour;
		end if;

		-- position
		update perso_position set ppos_pos_cod = v_pos_retour where ppos_perso_cod = v_perso_cible;
		
		-- coterie
		update groupe_perso set pgroupe_valeur_rappel = 0, pgroupe_statut = 1
		where pgroupe_perso_cod = v_perso_cible
			and pgroupe_groupe_cod = v_coterie_source;
			
		-- malus : prolongation de l’impalpabilité, réduction des PV
		-- autres malus à envisager ?
		update perso set
			perso_nb_tour_intangible = perso_nb_tour_intangible + 5,
			perso_pv = perso_pv / 2
		where perso_cod = v_perso_cible;
		
		-- événements
		texte_evt := '[cible] a été téléporté vers [attaquant], et ramené à sa coterie.';
		insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(38, now(), 1, v_perso_cible, texte_evt, 'N', 'O', v_perso_source, v_perso_cible);
		insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
		values(38, now(), 1, v_perso_source, texte_evt, 'O', 'N', v_perso_source, v_perso_cible);
		
		code_retour := code_retour || '<p>Enfin ! Votre compagnon décédé se matérialise à vos côtés. Il semble quelque peu sonné...</p>';
	else
		update groupe_perso set pgroupe_valeur_rappel = v_compteur_rappel
		where pgroupe_perso_cod = v_perso_cible
			and pgroupe_groupe_cod = v_coterie_source;
		
		code_retour := code_retour || '<p>Vous appelez votre compagnon, mais le lien est encore trop ténu pour le voir apparaître...</p>';
	end if;
	
	-- Décompte des PA
	update perso set perso_pa = perso_pa - v_cout_PA
	where perso_cod = v_perso_source;
	
	return code_retour;
end;
$function$

