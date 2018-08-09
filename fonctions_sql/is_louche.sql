CREATE OR REPLACE FUNCTION public.is_louche(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************/
/* is_louche : rend un perso_louche si besoin                 */
/*  $1 = perso_cod                                            */
/* retour = texte                                             */
/**************************************************************/
declare
	code_retour text;
	personnage alias for $1;
	nb_objet integer;
	nb_objet2 integer;
	is_louche integer;
	v_comp_ori integer;
	v_comp_modifie integer;
	v_chance integer;
	v_des integer;
	ligne record;
begin
	-- on vérifie qu'il porte des objets 
	select into nb_objet count(perobj_cod) from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod 
		and obj_gobj_cod = 186;
	if nb_objet = 0 then
		code_retour := '';
		return code_retour;
	end if;
	-- on vérifie qu'il ne soit pas déjà louche
	/*select into is_louche
		plouche_perso_cod
		from perso_louche
		where plouche_perso_cod = personnage;
	if found then
		code_retour := '';
		return code_retour;
	end if;*/
	select into v_comp_ori
		pcomp_modificateur
		from perso_competences
		where pcomp_perso_cod = personnage
		and pcomp_pcomp_cod = 78;
	if found then
		-- on a la comp
		v_comp_modifie := round(v_comp_ori / (1.5 * nb_objet));
		nb_objet2 := 0;
		code_retour := 'Avec comp, ';
		for ligne in select perobj_cod from perso_objets,objets
			where perobj_perso_cod = personnage
			and perobj_obj_cod = obj_cod 
			and obj_gobj_cod = 186 loop
			v_des := lancer_des(1,100);
			if v_des > 96 then
				nb_objet2 := nb_objet2 + 2;
			else
				if v_des > v_comp_modifie then
					nb_objet2 := nb_objet2 + 1;
				end if;
			end if;	
		end loop;
		v_chance := 100 - (nb_objet2 * 10);
	else
		-- on a pas la comp
		v_chance := 100 - (nb_objet * 10);
		code_retour := 'Sans comp, ';
	end if;
	v_des := lancer_des(1,100);
	if v_des > 96 then
		insert into perso_louche
			(plouche_perso_cod,plouche_nb_tours)
			values
			(personnage,3);
	end if;
	if v_des > v_chance then
		insert into perso_louche
			(plouche_perso_cod,plouche_nb_tours)
			values
			(personnage,3);
	end if;
	code_retour := code_retour||'Chance : '||trim(to_char(v_chance,'9999999'))||', des : '||trim(to_char(v_des,'99999999'));
	return code_retour;
end;$function$

