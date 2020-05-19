--
-- Name: controle_sort(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION controle_sort(integer, integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/************************************************/
/* fonction controle_sort                       */
/*  effectue les controles préalables au        */
/*  lancement du sort par un perso              */
/* on passe en params :                         */
/*  $1 = sort_cod à lancer                      */
/*  $2 = perso_cod du lanceur                   */
/*  $3 = perso_cod de la cible                  */
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
	cible alias for $3;
	type_lancer alias for $4;
-- sort
	cout_pa integer;			-- cout en PA du sort
	distance_sort integer;	-- distance maxi pour sort
	lanceur_pa integer;		-- PA du lanceur
  v_voie_magique integer;  -- voie magique du lanceur
	nb_sort integer;			-- nombre de fois ou la lanceur à lancé ce sort dans le tour
	v_pnbs_cod integer;		-- pnbs_cod pour perso_nb_tours
	pos_lanceur integer;		-- position du lanceur
	pos_cible integer;		-- position de la cible
	distance_cibles integer;-- distance entre les cibles
	compt_rune integer;		-- nombre de runes
	nom_rune varchar(50);	-- nom de la rune
	temp_traj text;			-- variable pour controle trajeactoire
	x_mur integer;				-- X du mur
	y_mur integer;				-- Y du mur
	ligne_rune record;
	pa_magie integer;				-- bonus en cout de lancer de sort
	niveau_religion integer;
	facteur_reussite integer;
	temp integer;
        resultat text;
    v_etage_cible integer;
    v_etage_lanceur integer;
    v_bonus_tour integer;
    v_bonus_valeur integer;

begin
	code_retour := '0;';

  select into cout_pa sort_cout from sorts where sort_cod = num_sort;

  -- on controle les PA
  select into lanceur_pa,pos_lanceur,v_etage_lanceur
    perso_pa,ppos_pos_cod,pos_etage
    from perso,perso_position,positions
    where perso_cod = lanceur
    and ppos_perso_cod = lanceur
    and ppos_pos_cod = pos_cod;

  if type_lancer != -1 then   -- on controle le nombre de PA sauf cas des EA
      -- appel de la fonction cout_pa_magie pour les calculs de cout de pa avec correlation pour l'affichage dans la page magie_php
      select into resultat cout_pa_magie(lanceur,num_sort,type_lancer);
      cout_pa := resultat;
      if lanceur_pa < cout_pa then
        code_retour := 'Erreur : Vous n''avez pas assez de PA pour lancer ce sort !';
        return code_retour;
      end if;

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
			code_retour := 'Erreur : Vous n''avez pas mémorisé le sort !'||trim(to_char(lanceur,'999999999999999999'))||' - '||trim(to_char(num_sort,'999999999999999999'));
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
			and not exists (select 1 from transaction where tran_obj_cod = perobj_obj_cod)
			and obj_sort_cod = num_sort;
		if not found then
			code_retour := 'Erreur : vous ne possédez pas de parchemin contenant ce sort (qui ne soit pas engagé dans une transaction).';
			return code_retour;
		end if;
	end if;
	-- lancement à partir d'un objet
	if type_lancer = 5 then
		select into compt_rune perobj_obj_cod from objets_sorts_magie
    join objets_sorts on objsort_cod=objsortm_objsort_cod
    join sorts on sort_cod=objsort_sort_cod
    join objets on obj_cod=objsort_obj_cod
    join perso_objets on perobj_obj_cod=obj_cod and perobj_perso_cod=objsortm_perso_cod
    where objsortm_perso_cod = lanceur
      and sort_cod = num_sort
      and perobj_identifie = 'O'
      and (perobj_equipe='O' or objsort_equip_requis=false)
      and (objsort_nb_utilisation_max>objsort_nb_utilisation or COALESCE(objsort_nb_utilisation_max,0) = 0)
      and not exists (select 1 from transaction where tran_obj_cod = perobj_obj_cod);
		if not found then
			code_retour := 'Erreur : vous ne possédez plus l''objet, ou il n''est plus équipé, ou il est engagé dans une transaction ou il ne dispose plus de charge.';
			return code_retour;
		end if;
	end if;
	if type_lancer = 3 then -- le 3 est pour la magie divine
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
	if type_lancer > 5 then
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
	if type_lancer < -1 then
		code_retour := 'Erreur sur le type de lancer !';
		return code_retour;
	end if;
-- on verifie les voies magiques
        select into v_voie_magique perso_voie_magique from perso
        where perso_cod = lanceur;
    -- les maitres du savoir pouvant lancer tous les sorts, on les exclues du test sauf pour le sort de familier sorcier qui reste uniquement pour les sorciers
  if v_voie_magique != 7 then
        -- guerisseur
        if num_sort in (150, 177) then
            if v_voie_magique != (1) then
                code_retour := 'Erreur vous n''etes pas guérisseur !';
		            return code_retour;
            end if;
        end if;
        -- maitre des arcanes
        if num_sort = 146 then
            if v_voie_magique != 2 then
                code_retour := 'Erreur vous n''etes pas Maître des runes !';
		            return code_retour;
            end if;
        end if;
        -- enchanteur runique
        if num_sort in(138,167) then
            if v_voie_magique != 5 then
                code_retour := 'Erreur vous n''etes pas enchanteur runique !';
		            return code_retour;
            end if;
        end if;
        -- Mage de guerre
        if num_sort in (152,176) then
            if v_voie_magique != 4 then
                code_retour := 'Erreur vous n''etes pas mage de guerre !';
		            return code_retour;
            end if;
        end if;
         -- Mage de bataille
        if num_sort in(153,168) then
            if v_voie_magique != 6 then
                code_retour := 'Erreur vous n''etes pas mage de bataille !';
		            return code_retour;
            end if;
        end if;
         -- Sorcier
        if num_sort in(149,166)then
            if v_voie_magique != 3 then
                code_retour := 'Erreur vous n''etes pas sorcier !';
		            return code_retour;
            end if;
        end if;
 end if;
-- exclusion du sort familier sorcier
 if v_voie_magique != 7 then
       if num_sort in(149)then
            if v_voie_magique != 3 then
                code_retour := 'Erreur vous n''etes pas sorcier !';
		return code_retour;
            end if;
        end if;
 end if;
-- Pour les enchanteurs runiques lançant les sorts bénéficiant de Conscience runique on réduit l'impacte de
-- de conscience runique sur les dlt futurs
            if num_sort in(11,25,38,67,128,136,139) AND type_lancer IN (0, 1) then
               select into v_bonus_tour,v_bonus_valeur bonus_nb_tours,bonus_valeur from bonus
		where bonus_perso_cod = lanceur
                and bonus_valeur < 0
		and bonus_tbonus_libc = 'ERU';
	        if found then
                v_bonus_tour := v_bonus_tour - 1;
                  if v_bonus_tour > 0 then
		   delete from bonus
			where bonus_perso_cod = lanceur
			and bonus_valeur < 0
			and bonus_tbonus_libc = 'ERU';
               	   perform ajoute_bonus(lanceur, 'ERU', V_bonus_tour, v_bonus_valeur);
                  end if;
                 end if;
              end if;
-- on vérifie les distances
	select into pos_cible,distance_sort,v_etage_cible
		ppos_pos_cod,sort_distance,pos_etage
		from perso_position,sorts,positions
		where ppos_perso_cod = cible
		and sort_cod = num_sort
		and ppos_pos_cod = pos_cod;
	if(v_etage_lanceur != v_etage_cible) then
		code_retour := 'Erreur : Etages différents !';
		return code_retour;
	end if;
	distance_cibles := distance(pos_lanceur,pos_cible);
	if distance_cibles > distance_sort then
		code_retour := 'Erreur : La cible est trop éloignée pour lancer le sort !';
		return code_retour;
	end if;

	if distance_sort > 1 and trajectoire_vue_murs(pos_lanceur,pos_cible) != 1 then
		code_retour := 'Votre sort arrive dans un mur.';
		return code_retour;
	end if;
-- on vérifie que le sort ait pas déjà été lancé plusieurs fois
-- ajout azaghal le 12/09/2008
-- les sorts lancés depuis receptacles sont maintenant comptés pour évaluer la limite de 2
-- 2019-10-01@marlyza idem pour les parchemins, et maintenant les objets magiques
	if type_lancer not in (-1, 3) then
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
	elsif type_lancer = 3 then
		facteur_reussite := 50;
	else
		facteur_reussite := 0;
	end if;
	code_retour := code_retour||trim(to_char(facteur_reussite,'9999999999'));
	if type_lancer = 4 then
		temp := f_del_objet(compt_rune);
	end if;

	return code_retour;
end;
$_$;


ALTER FUNCTION public.controle_sort(integer, integer, integer, integer) OWNER TO delain;
