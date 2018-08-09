CREATE OR REPLACE FUNCTION public.choisir_monstre_nom(integer, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function choisir_monstre_nom : retourne un nom en fonction    */
/* d'un type de monstre générique et d'un genre                  */
/* On passe en paramètres                                        */
/*    $1 = code du monstre générique                             */
/*    $2 = genre du monstre                                      */
/* Le code sortie est un texte (gobj_cod)                       */
/*****************************************************************/
/* Créé le 07/07/2005                                            */
/* Liste des modifications :                                     */
/*   31/01/2008 : gestion d'un facteur pour ne pas mettre de nom */
/*****************************************************************/
declare
	code_retour text;				-- code sortie
	v_gmon alias for $1;
	mon_genre alias for $2;
	v_nom text;
	v_race integer;
	temp_num integer;
	temp_nom text;
	des integer;
	chance integer;
	surnom text;
begin
	surnom = '';
	code_retour := '';
	select into v_nom,v_race gmon_nom,gmon_race_cod from monstre_generique
		where gmon_cod = v_gmon;
	-- NOM (genre indifférent)
	select into temp_num,chance count(rac_nom_nom),rac_nom_chance
		from race_nom_monstre
		where rac_nom_race_cod = v_race
		and rac_nom_type = 'N'
		group by rac_nom_chance;
	if found and  temp_num > 0 and lancer_des(1,100) < chance then
		surnom = 'OK';
		des := lancer_des(1,temp_num)-1;
		select into temp_nom rac_nom_nom
			from race_nom_monstre
			where rac_nom_race_cod = v_race
			and rac_nom_type = 'N'
			offset des limit 1;
		code_retour := temp_nom||' '||code_retour;
	end if;
	-- PRENOM
	select into temp_num,chance count(rac_nom_nom),rac_nom_chance
		from race_nom_monstre
		where rac_nom_race_cod = v_race
		and rac_nom_type = 'P'
		and rac_nom_genre = mon_genre
		group by rac_nom_chance;
	if found and  temp_num > 0 and lancer_des(1,100) < chance then
		surnom = 'OK';
		des := lancer_des(1,temp_num)-1;
		select into temp_nom rac_nom_nom
			from race_nom_monstre
			where rac_nom_race_cod = v_race
			and rac_nom_type = 'P'
			and rac_nom_genre = mon_genre
			offset des limit 1;
		code_retour := temp_nom||' '||code_retour;
	end if;
	-- SURNOM
	select into temp_num,chance count(rac_nom_nom),rac_nom_chance
		from race_nom_monstre
		where rac_nom_race_cod = v_race
		and rac_nom_type = 'S'
		and rac_nom_genre = mon_genre
		group by rac_nom_chance;
	if found and temp_num > 0 and lancer_des(1,100) < chance and surnom = 'OK' then
		des := lancer_des(1,temp_num)-1;
		select into temp_nom rac_nom_nom
			from race_nom_monstre
			where rac_nom_race_cod = v_race
			and rac_nom_type = 'S'
			and rac_nom_genre = mon_genre
			offset des limit 1;
		code_retour := code_retour||' '||temp_nom;
	end if;
	if code_retour = '' then
		code_retour := v_nom;
	else
		code_retour := code_retour||'- '||v_nom;
	end if;
	return code_retour;
end;$function$

