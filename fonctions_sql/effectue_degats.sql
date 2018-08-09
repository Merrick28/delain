CREATE OR REPLACE FUNCTION public.effectue_degats(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************/
/* effectue_degats                           */
/*  $1 = perso sur lequel on fait les dégats */
/*  $2 = degats de base                      */
/*  $3 = type de perso effectuant les degs   */
/* Retour : dégats rééls                     */
/*********************************************/
/* créé le 30/05/2007 par Merrick            */
/*********************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_degats alias for $2;
	v_type_att alias for $3;
	--
	v_type_perso integer;
	v_compt_pvp integer;
        v_compt_pvp1 integer;    -- ajout azaghal pour garde fou
	v_pv numeric;
	v_pv_max numeric;
	v_test text;
	v_anc_niveau integer;
	v_nv_niveau integer;
	v_correction_1 numeric;
	v_correction_2 numeric;
	v_correction_3 numeric;
	v_ratio numeric;
	v_marge_pv integer;
	v_degats_restants integer;
        nb_sort_tour integer;


begin
	v_correction_1 := 0.7;
	v_correction_2 := 0.45;
	v_correction_3 := 0.25;

        -- ajout azaghal, en cas de sorts de malédiction d'écatis, les seuils sont moins puissants
	if valeur_bonus(personnage, 'MEC') <> 0 then
	 v_correction_1 := 0.8;
	 v_correction_2 := 0.55;
	 v_correction_3 := 0.35;
       end if;

	v_degats_restants := v_degats;
	--
	-- test sur le perso cible et attaquant
	--
	select into v_type_perso , v_compt_pvp , v_pv , v_pv_max
		perso_type_perso , perso_compt_pvp , perso_pv , perso_pv_max
		from perso
		where perso_cod = personnage;
	if v_type_perso = 2 then
		return v_degats;
	end if;
	if v_type_att = 2 then
		return v_degats;
	end if;
        -- test arène : pas de réforme si on est dans une arène
        if est_dans_arene(personnage) then
                return v_degats;
        end if;
	--
	-- a priori, on peut commencer à y aller maintenant
	--
	code_retour := 0;

	while (v_degats_restants > 0) loop
		if v_compt_pvp = 1 then
			v_degats_restants := ceil(v_degats_restants*v_correction_1);
		elsif v_compt_pvp = 2 then
			v_degats_restants := ceil(v_degats_restants*v_correction_2);
		elsif v_compt_pvp >= 3 then
			v_degats_restants := ceil(v_degats_restants*v_correction_3);
		end if;
		-- Marge restante avant de changer de seuil de blessures
		v_ratio := (v_pv - code_retour - 1) / cast(v_pv_max as numeric);
		-- Pour des seuils à 0.8, 0.66, 0.33, 0.2 * pv_max
		v_marge_pv := 1 + floor(v_pv_max * (v_ratio - floor(3*v_ratio) / cast(3 as numeric) 
			- 0.20 * cast(v_ratio < 0.33 and v_ratio > 0.20 as integer)
			- 0.13 * cast(v_ratio > 0.80 as integer)));

		if (v_degats_restants < v_marge_pv) then
			code_retour := code_retour + v_degats_restants;
			v_degats_restants := 0; -- On quitte la boucle
		else
			v_degats_restants := v_degats_restants - v_marge_pv;
			code_retour := code_retour + v_marge_pv;
			-- On restaure les degats restants, pour le prochain passage dans la boucle
			if (v_compt_pvp = 1) then
				v_degats_restants := floor(v_degats_restants / v_correction_1);
			elsif (v_compt_pvp = 2) then
				v_degats_restants := floor(v_degats_restants / v_correction_2);
			elsif (v_compt_pvp >= 3) then
				v_degats_restants := floor(v_degats_restants / v_correction_3);
			end if;
			v_compt_pvp := v_compt_pvp + 1;
			if (code_retour >= v_pv) then -- La cible est décédée. On arrête le massacre
				v_degats_restants := 0;
			end if;
		end if;
	end loop;




	--
	-- on est bien d'accord les dégats vont être effectués par un joueur, il faut donc mettre les compteurs à jour
	-- même si c'est pas encore vrai :)
	--
	if ((v_pv/v_pv_max) < 0.20) then
		v_anc_niveau := 4;
	elsif ((v_pv/v_pv_max) < 0.33) then
		v_anc_niveau := 3;
	elsif ((v_pv/v_pv_max) < 0.66) then
		v_anc_niveau := 2;
	elsif  ((v_pv/v_pv_max) < 0.80) then
		v_anc_niveau := 1;
	elsif  ((v_pv/v_pv_max) >= 0.80) then
		v_anc_niveau := 0;
	end if;
	-- nouveau niveau
	v_pv := v_pv - code_retour;
	if ((v_pv/v_pv_max) < 0.20) then
		v_nv_niveau := 4;
	elsif ((v_pv/v_pv_max) < 0.33) then
		v_nv_niveau := 3;
	elsif ((v_pv/v_pv_max) < 0.66) then
		v_nv_niveau := 2;
	elsif  ((v_pv/v_pv_max) < 0.80) then
		v_nv_niveau := 1;
	elsif  ((v_pv/v_pv_max) >= 0.80) then
		v_nv_niveau := 0;
	end if;
	v_compt_pvp := v_nv_niveau - v_anc_niveau;

        -- garde fou compteur pvp bloqué à 4 max ajout azaghal
	select into v_compt_pvp1 
		perso_compt_pvp 
		from perso
		where perso_cod = personnage;
        v_compt_pvp1 := v_compt_pvp1 + v_compt_pvp;
        if v_compt_pvp1 > 4 then
        v_compt_pvp := 4 - v_compt_pvp1;
        end if;
        -- evidemment on ne permet pas d'ajout negatif
        if v_compt_pvp < 0 then
        v_compt_pvp = 0;
        end if;

	update perso
		set perso_compt_pvp = perso_compt_pvp + v_compt_pvp
		where perso_cod = personnage;
	return code_retour;
end;$function$

