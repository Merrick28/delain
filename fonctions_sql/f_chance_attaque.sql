CREATE OR REPLACE FUNCTION public.f_chance_attaque(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/****************************************************/
/* f_chance_attaque                                 */
/*  donne les chances d'attaque d'un perso avec     */
/*  son arme équipée                                */
/****************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_type_arme integer;
	comp_attaque integer;
	v_seuil_force integer;
	v_seuil_dex integer;
	v_force integer;
	v_dex integer;
	compt integer;
	v_bonus_toucher integer;
begin
	-- récupération des variables pour l'attaquant
	select into
		v_type_arme,
		v_force,
		v_dex
		type_arme(perso_cod),
		perso_for,
		perso_dex
		from perso
		where perso_cod = personnage;
	if v_type_arme = 0 then
		-- on est à mains nues, pas de modificateur
		select into comp_attaque pcomp_modificateur
			from perso_competences
			where pcomp_perso_cod = personnage
			and pcomp_pcomp_cod = 30;
		v_seuil_force := 0;
		v_seuil_dex := 0;
	else 
		-- on a une arme équipée
		select into 	comp_attaque,
							v_seuil_force,
							v_seuil_dex
						pcomp_modificateur,
						obj_seuil_force,
						obj_seuil_dex
			from perso_competences,perso_objets,objets,objet_generique
			where perobj_perso_cod = personnage
			and perobj_equipe = 'O'
			and perobj_obj_cod = obj_cod
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod = 1
			and gobj_comp_cod = pcomp_pcomp_cod
			and pcomp_perso_cod = personnage;
	end if;
	-- on a maintenant la compétence de base
	--
	-- reste à vérifier les valeurs des seuils
	--
	if v_seuil_force != 0 then
		if v_force >= v_seuil_force then
			comp_attaque := comp_attaque + ((v_force - v_seuil_force) * 1);
		else
			comp_attaque := comp_attaque + ((v_force - v_seuil_force) * 3);
		end if;
	end if;
	if v_seuil_dex != 0 then
		if v_dex >= v_seuil_dex then
			comp_attaque := comp_attaque + ((v_dex - v_seuil_dex) * 2);
		else
			comp_attaque := comp_attaque + ((v_dex - v_seuil_dex) * 4);
		end if;
	end if;
	--
	-- Concentration ?
	--
	select into compt concentration_perso_cod from concentrations
		where concentration_perso_cod = personnage;
	if found then
		comp_attaque := comp_attaque + 20;
	end if;
	--
	-- Sorts ?
	--
	comp_attaque := comp_attaque + valeur_bonus(personnage, 'TOU');
	--
	-- Minimum atteint ?
	--
	if comp_attaque < 10 then
		comp_attaque := 10;
	end if;
	return comp_attaque;
end;$function$

