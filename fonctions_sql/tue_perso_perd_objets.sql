CREATE OR REPLACE FUNCTION public.tue_perso_perd_objets(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction tue_perso_perd_objets                            */
/*   accomplit la perte d’objets consécutive à la mort       */
/*   d’un perso                                              */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod du mort                                  */
/*   $2 = mort définitive (1) ou non (0)                     */
/* on a en sortie le code HTML de la liste des objets perdus */
/*************************************************************/
/* Créé le 18/01/2013 Reivax Extraction de tue_perso_final   */
/*  et gestion des persos qui meurent définitivement         */
/* Modif Marlyza 02/05/2018 : à la mort du maitre, le fam    */
/*             perd aussi du matériel, mais il ne doit pas   */
/*             perdre ses griffes                            */
/*************************************************************/
declare
	code_retour text;
	v_cible alias for $1;
	mort_definitive alias for $2;

	-- variables de la cible
	v_or integer;
	type_cible integer;  -- pour traiter le cas particulier d'un familier
	pos_cible integer;

	-- variables de calcul
	ligne_objet record;

	-- gestion des sacs
	v_chance_sac integer;
begin
	v_chance_sac := 0;
	code_retour := '';

	select into v_or,type_cible perso_po,perso_type_perso from perso where perso_cod = v_cible;
	select into pos_cible ppos_pos_cod from perso_position where ppos_perso_cod = v_cible;

	-- Mort définitive : on perd tout ce qui est déposable, et tout l’or.
	if mort_definitive = 1 then
		for ligne_objet in
			select perobj_cod, perobj_obj_cod, gobj_deposable, perobj_equipe, obj_nom, tobj_libelle from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where perobj_perso_cod = v_cible
		loop
			if ligne_objet.gobj_deposable = 'N' then
			  -- ajout 16-05-2018 - marlyza - le familier ne perd pas son equipement non déposable s'il est équipé (c'est son equipement de base: griffes, etc...)
			  -- il doit le garder, même s'il decéde car un sort de réssurection pourrait le ramener à la vie.
			  if type_cible!=3 or ligne_objet.perobj_equipe!='O' then
				  delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
				  delete from perso_identifie_objet where pio_obj_cod = ligne_objet.perobj_obj_cod;
				  delete from objets where obj_cod = ligne_objet.perobj_obj_cod;
				end if;
			else
				insert into objet_position (pobj_cod, pobj_obj_cod, pobj_pos_cod)
				values (nextval('seq_pobj_cod'), ligne_objet.perobj_obj_cod, pos_cible);

				delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
				code_retour := code_retour || '<br><b>' || ligne_objet.obj_nom || '</b> <i>(' || ligne_objet.tobj_libelle || ')</i>';
			end if;
		end loop;

		if v_or > 0 then
			insert into or_position (por_cod, por_pos_cod, por_qte)
			values (nextval('seq_por_cod'), pos_cible, v_or);
			update perso set perso_po = 0 where perso_cod = v_cible;
			code_retour := code_retour || '<br>ainsi que <b>' || trim(to_char(v_or, '99999999999999')) || ' brouzoufs</b>';
		end if; -- fin or
	else
	-- Mort temporaire : on ne perd qu’un nombre restreint d’objets.
		-- on regarde d’abord s’il droppe un sac
		for ligne_objet in
			select perobj_cod, perobj_obj_cod, gobj_deposable, obj_nom, obj_poids, obj_chance_drop, tobj_libelle, perobj_equipe, perobj_identifie
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where perobj_perso_cod = v_cible
				and perobj_equipe = 'O'
				and gobj_tobj_cod = 25
		loop
			if lancer_des(1, 100) <= ligne_objet.obj_chance_drop then
				insert into objet_position (pobj_cod, pobj_obj_cod, pobj_pos_cod)
				values (nextval('seq_pobj_cod'), ligne_objet.perobj_obj_cod, pos_cible);

				delete from perso_objets where perobj_obj_cod = ligne_objet.perobj_obj_cod;
				code_retour := code_retour || '<br><b>' || ligne_objet.obj_nom || '</b> <i>(' || ligne_objet.tobj_libelle || ')</i>';
			end if;
			v_chance_sac := v_chance_sac + (ligne_objet.obj_poids * -1);
		end loop;

		for ligne_objet in
			select perobj_cod, perobj_obj_cod, gobj_deposable, obj_nom, obj_poids,
				case
					when perobj_equipe = 'N' AND obj_chance_drop > 0 then obj_chance_drop + v_chance_sac
					else obj_chance_drop
				end as obj_chance_drop,
				tobj_libelle, perobj_equipe, perobj_identifie, perobj_equipe
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where perobj_perso_cod = v_cible
		loop
			if ligne_objet.gobj_deposable = 'N' then
			  -- ajout 02-05-2018 - marlyza - le familier ne perd pas son equipement non déposable s'il est équipé (c'est son equipement de base: griffes, etc...)
			  if type_cible!=3 or ligne_objet.perobj_equipe!='O' then
          delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
          delete from perso_identifie_objet where pio_obj_cod = ligne_objet.perobj_obj_cod;
          delete from objets where obj_cod = ligne_objet.perobj_obj_cod;
        end if;
			else
				if lancer_des(1, 100) <= ligne_objet.obj_chance_drop then
					if ligne_objet.perobj_identifie = 'O' then
						insert into perso_identifie_objet (pio_cod, pio_perso_cod, pio_obj_cod, pio_nb_tours)
						values (nextval('seq_pio_cod'), v_cible, ligne_objet.perobj_obj_cod, getparm_n(22));
					end if;
					insert into objet_position (pobj_cod, pobj_obj_cod, pobj_pos_cod)
					values (nextval('seq_pobj_cod'), ligne_objet.perobj_obj_cod, pos_cible);

					delete from perso_objets where perobj_cod = ligne_objet.perobj_cod;
					code_retour := code_retour || '<br><b>' || ligne_objet.obj_nom || '</b> <i>(' || ligne_objet.tobj_libelle || ')</i>';
				end if;
			end if;
		end loop;
		if v_or != 0 then
			v_or := lancer_des(1, v_or);
			insert into or_position (por_cod, por_pos_cod, por_qte)
			values (nextval('seq_por_cod'), pos_cible, v_or);

			update perso set perso_po = perso_po - v_or where perso_cod = v_cible;
			code_retour := code_retour || '<br>ainsi que <b>' || trim(to_char(v_or, '99999999999999')) || ' brouzoufs</b>';
		end if; -- fin or
	end if; -- end type de mort
	return code_retour;
end;$function$

