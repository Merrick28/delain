CREATE OR REPLACE FUNCTION public.modif_type_arme(integer, integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$declare
	v_attaquant alias for $1;
	v_cible alias for $2;
	m_def numeric;
	v_type_arme integer;
	v_type_arme_cible integer;
	v_main_cible integer;
	v_main_attaquant integer;
begin
	v_type_arme := type_arme(v_attaquant);
	v_type_arme_cible := type_arme(v_cible);
	select into v_main_cible gobj_nb_mains
					from perso_objets,objets,objet_generique
					where perobj_perso_cod = v_cible
					and perobj_equipe = 'O'
					and perobj_obj_cod = obj_cod
					and obj_gobj_cod = gobj_cod
					and gobj_tobj_cod = 1;
	select into v_main_attaquant gobj_nb_mains
					from perso_objets,objets,objet_generique
					where perobj_perso_cod = v_attaquant
					and perobj_equipe = 'O'
					and perobj_obj_cod = obj_cod
					and obj_gobj_cod = gobj_cod
					and gobj_tobj_cod = 1;			
	--
	-- attaquant mains nues
	--
	if v_type_arme = 0 then 				-- attaquant mains nues
		if v_type_arme_cible = 0 then			-- cible mains nues
			m_def := 0.8;
		elsif v_type_arme_cible = 1 then		-- cible contact
			if v_main_cible = 1 then -- arme 1 main
				m_def := 1.2;
			else
				m_def := 1.4;
			end if;
		elsif v_type_arme_cible = 2 then		-- cible distance
			m_def := 0.5;
		end if;
	-- 
	-- attaquant contact
	--
	elsif v_type_arme = 1 then				
		if v_main_attaquant = 1 then -- arme 1 main
			if v_type_arme_cible = 0 then			-- cible mains nues
				m_def := 0.7;
			elsif v_type_arme_cible = 1 then		-- cible contact
				if v_main_cible = 1 then -- arme 1 main
					m_def := 0.9;
				else
					m_def := 1.4;
				end if;
			elsif v_type_arme_cible = 2 then		-- cible distance
				m_def := 0.4;
			end if;			
		else
			if v_type_arme_cible = 0 then			-- cible mains nues
				m_def := 0.7;
			elsif v_type_arme_cible = 1 then		-- cible contact
				if v_main_cible = 1 then -- arme 1 main
					m_def := 1.1;
				else
					m_def := 1.2;
				end if;
			elsif v_type_arme_cible = 2 then		-- cible distance
				m_def := 0.4;
			end if;			
		end if;
	-- 
	-- attaquant distance
	--
	elsif v_type_arme = 2 then				-- attaquant distance
		if v_type_arme_cible = 0 then			-- cible mains nues
			m_def := 0.6;
		elsif v_type_arme_cible = 1 then		-- cible contact
			if v_main_cible = 1 then -- arme 1 main
				m_def := 0.7;
			else
				m_def := 0.7;
			end if;
		elsif v_type_arme_cible = 2 then		-- cible distance
			m_def := 0.5;
		end if;
	end if;
	return m_def;
end;


$function$

