CREATE OR REPLACE FUNCTION public.consolide_classement_concours()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/* ****************************************************************
function consolide_classement_concours: 
Met à jour les classements, pour tous les concours en cours.
-----------------
09/11/2012 - Création
**************************************************************** */
declare
	code_retour text;
	ligne record;
	tranche_min integer;
	max_tranche_min integer;
	texte_tranche text;

begin
	code_retour := '';

	for ligne in select ccol_cod, ccol_titre, ccol_gobj_cod, ccol_tranche_niveau, ccol_differencier_4e, 10 as ccol_taille_classement from concours_collections
		where now() between ccol_date_ouverture and ccol_date_fermeture + '1 day 1 hour'::interval
	loop
		code_retour := code_retour || 'Traitement de « ' || ligne.ccol_titre || E' »...\
';
		delete from concours_collections_resultats where ccolres_ccol_cod = ligne.ccol_cod;

		insert into concours_collections_resultats (ccolres_ccol_cod, ccolres_perso_cod, ccolres_nombre, ccolres_division)
			select ligne.ccol_cod, perso_cod, count(*) as nombre, 'Tous aventuriers'
			from objets
			inner join perso_objets on perobj_obj_cod = obj_cod
			inner join perso on perso_cod = perobj_perso_cod
			inner join perso_position on ppos_perso_cod = perso_cod
			inner join positions on pos_cod = ppos_pos_cod
			inner join etage on etage_numero = pos_etage
			where obj_gobj_cod = ligne.ccol_gobj_cod
				AND etage_reference <> -100
				AND perso_actif <> 'N'
			group by perso_cod
			order by nombre desc
			limit ligne.ccol_taille_classement;

		code_retour := code_retour || E'\	Classement « Tous aventuriers » OK.\
';

		if ligne.ccol_differencier_4e = 'O' then
			insert into concours_collections_resultats (ccolres_ccol_cod, ccolres_perso_cod, ccolres_nombre, ccolres_division)
				select ligne.ccol_cod, p.perso_cod, count(*) as nombre, 'Hors quatrièmes'
				from objets
				inner join perso_objets on perobj_obj_cod = obj_cod
				inner join perso p on p.perso_cod = perobj_perso_cod
				inner join perso_position on ppos_perso_cod = perso_cod
				inner join positions on pos_cod = ppos_pos_cod
				inner join etage on etage_numero = pos_etage
				left outer join perso_familier on pfam_familier_cod = p.perso_cod
				left outer join perso maitre on maitre.perso_cod = pfam_perso_cod
				where obj_gobj_cod = ligne.ccol_gobj_cod
					AND etage_reference <> -100
					AND p.perso_actif <> 'N'
					AND p.perso_pnj = 0
					AND (maitre.perso_pnj = 0 OR maitre.perso_pnj IS NULL)
				group by p.perso_cod
				order by nombre desc
				limit ligne.ccol_taille_classement;
			code_retour := code_retour || E'\	Classement « Hors quatrièmes » OK.\
';
		elsif ligne.ccol_tranche_niveau > 0 then
			-- On récupère le niveau maximal au-dessus duquel on ne divise plus en tranches (plus assez de joueurs)
			select into max_tranche_min perso_niveau
			from perso
			inner join perso_position on ppos_perso_cod = perso_cod
			inner join positions on pos_cod = ppos_pos_cod
			inner join etage on etage_numero = pos_etage
			where etage_reference <> -100
				AND perso_actif <> 'N'
				AND perso_type_perso = 1
			order by perso_niveau desc
			offset ligne.ccol_taille_classement * 3
			limit 1;

			tranche_min := 1;
			-- Boucle sur les tranches de niveau
			while (tranche_min <= max_tranche_min) loop
				if tranche_min + ligne.ccol_tranche_niveau < max_tranche_min then
					texte_tranche := 'Niv. ' || tranche_min::text || ' à ' || (tranche_min + ligne.ccol_tranche_niveau - 1)::text;
					insert into concours_collections_resultats (ccolres_ccol_cod, ccolres_perso_cod, ccolres_nombre, ccolres_division)
						select ligne.ccol_cod, p.perso_cod, count(*) as nombre, texte_tranche
						from objets
						inner join perso_objets on perobj_obj_cod = obj_cod
						inner join perso p on p.perso_cod = perobj_perso_cod
						inner join perso_position on ppos_perso_cod = perso_cod
						inner join positions on pos_cod = ppos_pos_cod
						inner join etage on etage_numero = pos_etage
						left outer join perso_familier on pfam_familier_cod = p.perso_cod
						left outer join perso maitre on maitre.perso_cod = pfam_perso_cod
						where obj_gobj_cod = ligne.ccol_gobj_cod
							AND etage_reference <> -100
							AND p.perso_actif <> 'N'
							AND (maitre.perso_niveau IS NULL AND p.perso_niveau >= tranche_min AND p.perso_niveau < tranche_min + ligne.ccol_tranche_niveau
								OR maitre.perso_niveau >= tranche_min AND maitre.perso_niveau < tranche_min + ligne.ccol_tranche_niveau)
						group by p.perso_cod
						order by nombre desc
						limit ligne.ccol_taille_classement;
				else
					texte_tranche := 'Niv. ' || tranche_min::text || ' et plus.';
					insert into concours_collections_resultats (ccolres_ccol_cod, ccolres_perso_cod, ccolres_nombre, ccolres_division)
						select ligne.ccol_cod, p.perso_cod, count(*) as nombre, texte_tranche
						from objets
						inner join perso_objets on perobj_obj_cod = obj_cod
						inner join perso p on p.perso_cod = perobj_perso_cod
						inner join perso_position on ppos_perso_cod = perso_cod
						inner join positions on pos_cod = ppos_pos_cod
						inner join etage on etage_numero = pos_etage
						left outer join perso_familier on pfam_familier_cod = p.perso_cod
						left outer join perso maitre on maitre.perso_cod = pfam_perso_cod
						where obj_gobj_cod = ligne.ccol_gobj_cod
							AND etage_reference <> -100
							AND p.perso_actif <> 'N'
							AND (maitre.perso_niveau IS NULL AND p.perso_niveau >= tranche_min
								OR maitre.perso_niveau >= tranche_min)
						group by p.perso_cod
						order by nombre desc
						limit ligne.ccol_taille_classement;
				end if;
				code_retour := code_retour || E'\	Classement « ' || texte_tranche || E' » OK.\
';
				tranche_min := tranche_min + ligne.ccol_tranche_niveau;
			end loop;
		end if;

		update concours_collections set ccol_date_consolidation = now()
		where ccol_cod = ligne.ccol_cod;
	end loop;

	return code_retour;
end;$function$

