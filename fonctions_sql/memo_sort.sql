CREATE OR REPLACE FUNCTION public.memo_sort(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***************************************************/
/* fonction memo_sort : tente de mémoriser un sort */
/* on passe en param :                             */
/*    $1 = le lanceur                              */
/*    $2 = le sort_cod                             */
/* on a en retour une chaine séparée par ;         */
/*   pos0 = possibilité                            */
/*       -1 = trop de sorts mémorisés              */
/*        0 = sort déjà mémorisé                   */
/*        1 = sort mémorisable                     */
/*       -2 = familier                             */
/*   pos1 = proba de mémorisation                  */
/*   pos2 = lancement de dés                       */
/*   pos3 = réussite ? (1=oui, 0=non)              */
/*   pos4 = px gagnés pour mémo                    */
/***************************************************/
/* Créé le 24/07/2003                              */
/***************************************************/
declare
	code_retour text;
	lanceur alias for $1;
	num_sort alias for $2;
--
	sort_memorise integer;				-- sort actuel mémorisé ?
	nb_sort_memorise integer;			-- nb de sort mémorisés pour le perso
	nb_lancer_sort integer;				-- nb de fois ou le sort a été lancé
	intelligence integer;				-- intelligence du perso
	proba_memo_temp numeric;			-- probabilité de mémoriser le sort (temp)
	proba_memo integer;					-- probabilité de mémoriser le sort
	des integer;							-- lancer de des
	texte_evt text;
	nom_sort varchar(50);
	v_amel_sort integer;
	v_gmon_cod integer;
	v_niveau_sort integer;
	race integer;
	limite integer;
begin
	code_retour := '';
	
	select into sort_memorise psort_perso_cod from perso_sorts
		where psort_sort_cod = num_sort
		and psort_perso_cod = lanceur;
	if not found then
	-- on tente une mémorisation
		select into nb_sort_memorise count(psort_perso_cod)
			from perso_sorts
			where psort_perso_cod = lanceur;
		select into nb_lancer_sort,intelligence,v_amel_sort,race
			pnbst_nombre,perso_int,perso_amelioration_nb_sort,perso_race_cod
			from perso_nb_sorts_total,perso
			where pnbst_perso_cod = lanceur
			and pnbst_sort_cod = num_sort
			and perso_cod = lanceur;
-- on regarde si le sort peut être mémorisé
		if race = '2' then
			limite := floor((intelligence / 2) + v_amel_sort);
		else
			limite := (intelligence + v_amel_sort);
		end if;
		if nb_sort_memorise >= limite then
			-- on ne peut plus mémoriser
			code_retour := code_retour||'-1;';
			return code_retour;	
		end if;
		code_retour := code_retour||'1;';
		select into v_gmon_cod perso_gmon_cod
		from perso
		where perso_cod = lanceur;
		if v_gmon_cod in (191,192,193) then
			select into v_niveau_sort
				sort_niveau
				from sorts
				where sort_cod = num_sort;
			if v_niveau_sort >= 3 then
				code_retour := '-2;';
				return code_retour;	
			end if;
		end if;
-- on calcule la probabilité de mémorisation		
		proba_memo := f_chance_memo(lanceur,num_sort);
		code_retour := code_retour||trim(to_char(proba_memo,'9999'))||';';
		des := lancer_des(1,100);
                code_retour := code_retour||trim(to_char(des,'9999'))||';';
		if des > proba_memo then
			code_retour:=code_retour||'0;0;';
			return code_retour;
		end if;
		insert into perso_sorts (psort_sort_cod,psort_perso_cod)
			values (num_sort,lanceur);
		select into nom_sort sort_nom from sorts
			where sort_cod = num_sort;
		texte_evt := '[attaquant] a mémorisé le sort '||nom_sort||'.';
      insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)
      	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','N',lanceur);
	
		update perso set perso_px = perso_px + 1
			where perso_cod = lanceur;
		code_retour := code_retour||'1;1;';
	else
		code_retour := code_retour||'0;0;0;0;';
	end if;
	return code_retour;
end;
$function$

