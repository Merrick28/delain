CREATE OR REPLACE FUNCTION public.copier_etage_par_morceau(integer, integer, integer, integer, integer, integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/********************************************************/
/* function copier_etage_par_morceau                          */
/* Copie un bout d’étage vers un autre étage.                 */
/* parametres :                                               */
/*  $1 = etage_numero de l’étage source                       */
/*  $2 = pos_x min de l’étage source                          */
/*  $3 = pos_x max de l’étage source                          */
/*  $4 = pos_y min de l’étage source                          */
/*  $5 = pos_y max de l’étage source                          */
/*  $6 = etage_numero de l’étage destination                  */
/*  $7 = pos_x min de l’étage destination                     */
/*  $8 = pos_x max de l’étage destination                     */
/*  $9 = pos_y min de l’étage destination                     */
/*  $10 = pos_y max de l’étage destination                     */
/* Sortie :                                                   */
/*  code_retour = texte récapitulatif                         */
/**************************************************************/
/**************************************************************/
/* Création - 23/10/2012 - Reivax                             */
/**************************************************************/
declare
	source_numero alias for $1;
	source_x_min alias for $2;
	source_x_max alias for $3;
	source_y_min alias for $4;
	source_y_max alias for $5;
	dest_numero alias for $6;
	dest_x_min alias for $7;
	dest_x_max alias for $8;
	dest_y_min alias for $9;
	dest_y_max alias for $10;

	vecteur_x integer;          -- le vecteur de décalage de la zone sur les x
	vecteur_y integer;          -- le vecteur de décalage de la zone sur les y
	dest_pos_cod integer;       -- pos_cod cible (dans la boucle)
	type_affichage_source text; -- affichage étage source
	type_affichage_dest text;   -- affichage étage destination
	source record;              -- variable de boucle
	ligne_perso record;         -- variable de boucle
	ligne_objet record;         -- variable de boucle
	ligne_ingredients record;   -- variable de boucle

	code_retour text;                -- le code retour
begin
/*********************************************************/
/*        I N I T I A L I S A T I O N S                  */
/*********************************************************/
	code_retour := '';
	-- Vérification de l’adéquation de la forme source avec la forme de destination
	if (source_x_max - source_x_min <> dest_x_max - dest_x_min)
		OR (source_y_max - source_y_min <> dest_y_max - dest_y_min) then
		return 'Erreur ! La surface source et la surface de destination sont incompatibles !';
	end if;
	if NOT EXISTS(select * from etage where etage_numero = source_numero)
		OR NOT EXISTS(select * from etage where etage_numero = dest_numero) then
		return 'Erreur ! L’étage source, ou l’étage de destination, est inconnu !';
	end if;
	if NOT EXISTS(select * from positions where pos_etage = source_numero AND pos_x = source_x_min AND pos_y = source_y_min)
		OR NOT EXISTS(select * from positions where pos_etage = source_numero AND pos_x = source_x_max AND pos_y = source_y_max)
		OR NOT EXISTS(select * from positions where pos_etage = dest_numero AND pos_x = dest_x_min AND pos_y = dest_y_min)
		OR NOT EXISTS(select * from positions where pos_etage = dest_numero AND pos_x = dest_x_max AND pos_y = dest_y_max) then
		return 'Erreur ! L’étage source, ou l’étage de destination, ne possède pas toutes les cases demandées !';
	end if;
	select into type_affichage_source etage_affichage from etage where etage_numero = source_numero;
	select into type_affichage_dest etage_affichage from etage where etage_numero = dest_numero;
	if type_affichage_source <> type_affichage_dest then
		return 'Erreur ! L’étage source et l’étage de destination ne partagent pas le même jeu de graphismes !';
	end if;

/*********************************************************/
/*             E X É C U T I O N                         */
/*********************************************************/

	vecteur_x := dest_x_min - source_x_min;
	vecteur_y := dest_y_min - source_y_min;

	-- suppression des murs existants
	delete from murs where mur_pos_cod IN
		(select pos_cod
		from positions
		inner join murs on mur_pos_cod = pos_cod
		where pos_etage = dest_numero
			and pos_x between dest_x_min and dest_x_max
			and pos_y between dest_y_min and dest_y_max
		);
	code_retour := code_retour || 'Suppression des murs...
';

	-- suppression des champs de composants existants
	delete from ingredient_position where ingrpos_pos_cod IN
		(select pos_cod
		from positions
		inner join ingredient_position on ingrpos_pos_cod = pos_cod
		where pos_etage = dest_numero
			and pos_x between dest_x_min and dest_x_max
			and pos_y between dest_y_min and dest_y_max
		);
	code_retour := code_retour || 'Suppression des herbes...
';

	-- Pour chaque case dans la source : modifier paramètres de cases ; modifier paramètres de murs ; déplacer personnages et objets coincés dans les murs
	for source in 
		select pos_cod, pos_x, pos_y, pos_type_aff, pos_decor, pos_decor_dessus, pos_modif_pa_dep, pos_passage_autorise, pos_entree_arene, 
			mur_type, mur_tangible, mur_creusable, mur_usure, mur_richesse
		from positions
		left outer join murs on mur_pos_cod = pos_cod
		where pos_x between source_x_min and source_x_max
			and pos_y between source_y_min and source_y_max
			and pos_etage = source_numero
	loop
		select into dest_pos_cod pos_cod from positions
		where pos_x = source.pos_x + vecteur_x
			and pos_y = source.pos_y + vecteur_y
			and pos_etage = dest_numero;

		update positions set
			pos_type_aff = source.pos_type_aff,
			pos_decor = source.pos_decor,
			pos_decor_dessus = source.pos_decor_dessus,
			pos_modif_pa_dep = source.pos_modif_pa_dep,
			pos_passage_autorise = source.pos_passage_autorise,
			pos_entree_arene = source.pos_entree_arene
		where pos_cod = dest_pos_cod;
		code_retour := code_retour || 'Mise à jour de la position ' || dest_pos_cod::text || ', ' || (source.pos_x + vecteur_x)::text || ', ' || (source.pos_y + vecteur_y)::text || ', ' || dest_numero::text || ', à partir de la position ' || source.pos_x::text || ', ' || source.pos_y::text || ', ' || source_numero::text || '.
';

		-- création d’un mur copie-conforme de l’existant
		if source.mur_tangible is not null then
			insert into murs(mur_pos_cod, mur_type, mur_tangible, mur_creusable, mur_usure, mur_richesse)
			values (dest_pos_cod, source.mur_type, source.mur_tangible, source.mur_creusable, source.mur_usure, source.mur_richesse);
			code_retour := code_retour || '		Création d’un mur...
';
		end if;

		-- déplacer les persos présents
		-- Spécifique pour la copie du 0 : le replacement ne se fait que dans la zone -21 / + 21 car l’extérieur est inaccessible

		for ligne_perso in 
			select ppos_perso_cod
			from perso_position
			where ppos_pos_cod = dest_pos_cod
		loop
			code_retour := code_retour || '		Déplacement du perso ' || ligne_perso.ppos_perso_cod::text || '...
';
			update perso_position
			set ppos_pos_cod = (
				select pos_cod from positions
				left outer join murs on mur_pos_cod = pos_cod
				where pos_etage = dest_numero
					and mur_pos_cod IS NULL
					and pos_x between -21 and 21
					and pos_y between -21 and 21
				order by random()
				limit 1
			)
			where ppos_perso_cod = ligne_perso.ppos_perso_cod;

			-- suppression des locks
			delete from lock_combat where lock_attaquant = ligne_perso.ppos_perso_cod;
			delete from lock_combat where lock_cible = ligne_perso.ppos_perso_cod;
		end loop;
		
		-- déplacer les objets présents
		-- Spécifique pour la copie du 0 : le replacement ne se fait que dans la zone -21 / + 21 car l’extérieur est inaccessible

		for ligne_objet in 
			select pobj_obj_cod
			from objet_position
			where pobj_pos_cod = dest_pos_cod
		loop
			code_retour := code_retour || '		Déplacement de l’objet ' || ligne_objet.pobj_obj_cod::text || '...
';
			update objet_position
			set pobj_pos_cod = (
				select pos_cod from positions
				left outer join murs on mur_pos_cod = pos_cod
				where pos_etage = dest_numero
					and mur_pos_cod IS NULL
					and pos_x between -21 and 21
					and pos_y between -21 and 21
				order by random()
				limit 1
			)
			where pobj_obj_cod = ligne_objet.pobj_obj_cod;
		end loop;
		
		-- déplacer les brouzoufs présents
		-- Spécifique pour la copie du 0 : le replacement ne se fait que dans la zone -21 / + 21 car l’extérieur est inaccessible

		for ligne_objet in 
			select por_cod
			from or_position
			where por_pos_cod = dest_pos_cod
		loop
			code_retour := code_retour || '		Déplacement d’un tas de brouzoufs...
';
			update or_position
			set por_pos_cod = (
				select pos_cod from positions
				left outer join murs on mur_pos_cod = pos_cod
				where pos_etage = dest_numero
					and mur_pos_cod IS NULL
					and pos_x between -21 and 21
					and pos_y between -21 and 21
				order by random()
				limit 1
			)
			where por_cod = ligne_objet.por_cod;
		end loop;

		-- Gérer les herbes
		for ligne_ingredients in 
			select ingrpos_gobj_cod, ingrpos_max, ingrpos_chance_crea, ingrpos_qte
			from ingredient_position
			where ingrpos_pos_cod = source.pos_cod
		loop
			code_retour := code_retour || '		Création d’un champ de gobj_cod=' || ligne_ingredients.ingrpos_gobj_cod::text || '...
';
			insert into ingredient_position (ingrpos_pos_cod, ingrpos_gobj_cod, ingrpos_max, ingrpos_chance_crea, ingrpos_qte)
			values (dest_pos_cod, ligne_ingredients.ingrpos_gobj_cod, ligne_ingredients.ingrpos_max, ligne_ingredients.ingrpos_chance_crea, ligne_ingredients.ingrpos_qte);
		end loop;
	end loop;
	/*
	-> vérifier les cachettes et fontaines. Fontaines == pos_decor IN (101, 102)
	*/
	return code_retour;
end;		$function$

