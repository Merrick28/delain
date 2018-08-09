CREATE OR REPLACE FUNCTION public.controle_sort_case(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* fonction controle_sort_case                  */
/*  effectue les controles préalables au        */
/*  lancement du sort par un perso              */
/* on passe en params :                         */
/*  $1 = sort_cod à lancer                      */
/*  $2 = perso_cod du lanceur                   */
/*  $3 = pos_cod de la cible                    */
/*  $4 = type_lancer                            */
/************************************************/
/* on a en retour une chaine séparées par ;     */
/*   pos0 = résultat (0 OK, -1 bad)             */
/*   pos1 = motif si bad                        */
/************************************************/
/* Créé le 24/07/2003                           */
/************************************************/
declare
	code_retour text;			-- code retour
	num_sort alias for $1;
	lanceur alias for $2;
	v_pos alias for $3;
	type_lancer alias for $4;
-- sort
	cout_pa integer;			-- cout en PA du sort
	distance_sort integer;	-- distance maxi pour sort
	lanceur_pa integer;		-- PA du lanceur
	nb_sort integer;			-- nombre de fois ou la lanceur à lancé ce sort dans le tour
	v_pnbs_cod integer;		-- pnbs_cod pour perso_nb_tours
	pos_lanceur integer;		-- position du lanceur
	distance_cibles integer;-- distance entre les cibles
	compt_rune integer;		-- nombre de runes
	nom_rune varchar(50);	-- nom de la rune
	temp_traj text;			-- variable pour controle trajeactoire
	x_mur integer;				-- X du mur
	y_mut integer;				-- Y du mur
	v_mur integer;
	ligne_rune record;
	pa_magie integer;				-- bonus en cout de lancer de sort
	niveau_religion integer;
	facteur_reussite integer;
	v_passage_autorise integer;
        temp integer;
       resultat text;
begin
	code_retour := '0;';
	
	select into cout_pa sort_cout from sorts where sort_cod = num_sort;
-- on controle les PA
	select into lanceur_pa,pos_lanceur
		perso_pa,ppos_pos_cod
		from perso,perso_position
		where perso_cod = lanceur
		and ppos_perso_cod = lanceur;
	if num_sort in (56,62,144,151) then
		select into v_passage_autorise pos_passage_autorise
			from positions
			where pos_cod = pos_lanceur;
		if v_passage_autorise = 0 then
			code_retour := 'Les flux magiques présents en ces lieux ne sont décidément pas suffisants pour que vous puissiez les rassembler. Votre tentative de lancer ce sort se traduit par un échec.';
			return code_retour;
		end if;
		select into v_passage_autorise pos_passage_autorise
			from positions
			where pos_cod = v_pos;
		if v_passage_autorise = 0 then
			code_retour := 'Les flux magiques présents en ces lieux ne sont décidément pas suffisants pour que vous puissiez les rassembler. Votre tentative de lancer ce sort se traduit par un échec.';
			return code_retour;
		end if;
	end if;
	-- appel de la fonction cout_pa_magie pour les calculs de cout de pa avec correlation pour l'affichage dans la page magie_php
select into resultat cout_pa_magie(lanceur,num_sort,type_lancer);
cout_pa := resultat;

	if lanceur_pa < cout_pa then
		code_retour := 'Erreur : Vous n''avez pas assez de PA pour lancer ce sort !';
		return code_retour;
	end if;	
-- on controle les runes si type lancer = 0
	if type_lancer = 0 then
		for ligne_rune in select * from sort_rune where srune_sort_cod = num_sort loop
			select into compt_rune perobj_obj_cod from perso_objets,objets
				where perobj_perso_cod = lanceur
				and perobj_obj_cod = obj_cod
				and not exists (select 1 from transaction where tran_obj_cod = perobj_obj_cod)
				and obj_gobj_cod = ligne_rune.srune_gobj_cod;
			if not found then
				select into nom_rune gobj_nom
					from objet_generique
					where gobj_cod = ligne_rune.srune_gobj_cod;
				code_retour := 'Erreur : Vous ne possédez pas de rune '||nom_rune||' nécessaire pour ce sort, qui ne soit pas dans une transaction !';
				return code_retour;
			end if;
		end loop;
	end if;
	if type_lancer = 1 then
		select into niveau_religion dper_niveau
			from dieu_perso
			where dper_perso_cod = lanceur;
		if found then
			if niveau_religion >= 2 then
				code_retour := 'Erreur : Vous ne pouvez pas lancer de sorts mémorisés à cause de votre grade religieux !';
				return code_retour;
			end if;
		end if;
		select into compt_rune psort_cod from perso_sorts
			where psort_perso_cod = lanceur
			and psort_sort_cod = num_sort;
		if not found then
			code_retour := 'Erreur : Vous n''avez pas mémorisé le sort !';
			return code_retour;
		end if;
	end if;
	if type_lancer = 2 then
		select into compt_rune recsort_cod from recsort
			where recsort_perso_cod = lanceur
			and recsort_sort_cod = num_sort;
		if not found then
			code_retour := 'Erreur : Ce sort n''est pas dans un réceptacle !';
			return code_retour;
		end if;
	end if;
        if type_lancer = 4 then
		select into compt_rune perobj_obj_cod from perso_objets,objets,objet_generique
			where perobj_perso_cod = lanceur
			and perobj_identifie = 'O' 
			and perobj_obj_cod = obj_cod
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod = 20
			and obj_sort_cod = num_sort;
		if not found then
			code_retour := 'Erreur : Ce sort n''est pas dans un parchemin !';
			return code_retour;
		end if;
	end if;
	if type_lancer > 4 then
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
	if type_lancer < 0 then
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
-- on vérifie les distances
	select into distance_sort
		sort_distance
		from sorts
		where sort_cod = num_sort;
	distance_cibles := distance(pos_lanceur,v_pos);
	if distance_cibles > distance_sort then
		code_retour := 'Erreur : La cible est trop éloignée pour lancer le sort !';
		return code_retour;
	end if;
-- on vérfie les murs
	select into v_mur mur_pos_cod
		from murs
		where mur_pos_cod = v_pos;
	if found then
		code_retour := 'Erreur : Vous ne pouvez pas lancer le sort surun mur !';
		return code_retour;
	end if;
-- on vérifie que le sort ait pas déjà été lancé plusieurs fois
	if type_lancer <3 then
		if not exists (select 1 from perso_nb_sorts
			where pnbs_perso_cod = lanceur
			and pnbs_sort_cod = num_sort) then
			insert into perso_nb_sorts (pnbs_cod,pnbs_perso_cod,pnbs_sort_cod,pnbs_nombre)
				values (nextval('seq_pnbs_cod'),lanceur,num_sort,0);
		end if;
		select into nb_sort,v_pnbs_cod pnbs_nombre,pnbs_cod from perso_nb_sorts
			where pnbs_perso_cod = lanceur
			and pnbs_sort_cod = num_sort;
		if nb_sort >= 2 then
			code_retour := 'Erreur : Vous ne pouvez pas lancer le même plus de 2 fois dans le même tour !';
			return code_retour;
		end if;
		update perso_nb_sorts
			set pnbs_nombre = pnbs_nombre + 1
			where pnbs_cod = v_pnbs_cod;
	end if;
	if type_lancer = 2 then
		select into compt_rune recsort_reussite from recsort
			where recsort_perso_cod = lanceur
			and recsort_sort_cod = num_sort limit 1;
		facteur_reussite := compt_rune;
select into compt_rune recsort_cod from recsort
			where recsort_perso_cod = lanceur
			and recsort_sort_cod = num_sort limit 1;
delete from recsort where recsort_cod = compt_rune;
	else
		facteur_reussite := 0;
	end if;
	code_retour := code_retour||trim(to_char(facteur_reussite,'9999999999'));
        if type_lancer = 4 then
		temp := f_del_objet(compt_rune);
	end if;
	return code_retour;
end;
$function$

