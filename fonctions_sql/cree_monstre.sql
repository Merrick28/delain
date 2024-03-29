-- FUNCTION: public.cree_monstre(integer, integer)

-- DROP FUNCTION public.cree_monstre(integer, integer);

CREATE OR REPLACE FUNCTION public.cree_monstre(
	integer,
	integer)
RETURNS integer
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE 
AS $BODY$
 /*****************************************************************/
/* function cree_monstre :Procédure de création de monstre en    */
/*   position aleatoire                                          */
/*                                                               */
/* On passe en paramètre :                                       */
/*   $1 = le gmon_cod (monstre générique)                        */
/*   $2 = le niveau ou il doit apparaitre                        */
/* Le code sortie est :                                          */
/*    0 = Tout s est bien passé                                  */
/*   -1 = le gmon_cod n existe pas                               */
/*    2 = cree perso anomalie                                    */
/*****************************************************************/
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	code_retour integer;
        v_temp_num numeric;
	v_gmon alias for $1;
	v_level alias for $2;
-- récupération des données génériques
	v_nom varchar(255);
	v_for integer;
	v_dex integer;
	v_int integer;
	v_con integer;
	v_race integer;
	v_temps_tour integer;
	v_des_regen integer;
	v_valeur_regen integer;
	v_vue integer;
	v_niveau integer;
	v_amelioration_vue integer;
	v_amelioration_regen integer;
	v_amelioration_degats integer;
	v_amelioration_armure integer;
	v_nb_des_degats integer;
	v_val_des_degats integer;
	v_or integer;
	v_arme integer;
	v_armure integer;
	v_code_arme integer;
	v_code_armure integer;
	v_code_arme_serie integer;
	v_code_armure_serie integer;	
	v_dist integer;
	v_vampirisme numeric;
	v_pv integer;
  v_nb_recep integer;
  v_voie_magique integer;   --Marlyza - 2018-08-14 - ajout de la voie magique sur les mosntres generique
  v_type_portail integer;        -- portail à monture (41) ou à monstre (8) ?
  v_gmon_cod integer;        -- cod generique
-- variables globales
	compt integer;
	v_code_perso integer;
	retour_cree_perso integer;
	nb_portail integer;
	-- num_portail integer;     -- utilité ?
	pos_portail integer;
	ligne record;
	ligne_objet record;
	v_code_objet integer;
	v_sex text;
	v_genre text;
	objet_etat_min integer;
	objet_etat_max integer;
	texte text;
	v_nb_portail integer;
begin
/**********************************************/
/* Etape 1 : on insère dans perso les valeurs */
/**********************************************/
	code_retour := 0;
	v_code_perso := nextval('seq_perso');
	select into v_gmon_cod,v_type_portail gmon_cod,  CASE WHEN gmon_monture='N' THEN 8 ELSE 41 END from monstre_generique
		where gmon_cod = v_gmon;
	if not found then
		code_retour := -1;
		return code_retour;
	end if;
	/*******************************/
	/* Position                    */
	/*******************************/
	/*
	Marlyza - 2021-04-15 - On calcul un nombre de portail mais on ne s'en sert pas? => supression de cette portion de code
	select into nb_portail count(*)
		from lieu,lieu_position,positions
		where lieu_tlieu_cod = v_type_portail
		and lpos_lieu_cod = lieu_cod
		and lpos_pos_cod = pos_cod
		and pos_etage = v_level;
	num_portail := lancer_des(1,nb_portail);
	num_portail := num_portail - 1;
  */

  -- choisir un portail
  v_nb_portail :=0 ;
  if v_type_portail = 8 then
      -- recherche d'un portail démoniaque
      select into pos_portail pos_cod
        from lieu,lieu_position,positions
        where lieu_tlieu_cod = 8
        and lpos_lieu_cod = lieu_cod
        and lpos_pos_cod = pos_cod
        and pos_etage = v_level
        order by random()
        limit 1;
  else
      -- recherche d'un portail à monture compatible avecles terrains accessibles pour la monture
      select count(*) into v_nb_portail
        from lieu,lieu_position,positions
        where lieu_tlieu_cod = 41
        and lpos_lieu_cod = lieu_cod
        and lpos_pos_cod = pos_cod
        and pos_etage = v_level ;

      select into pos_portail pos_cod
        from lieu,lieu_position,positions
        where lieu_tlieu_cod = 41
        and lpos_lieu_cod = lieu_cod
        and lpos_pos_cod = pos_cod
        and pos_etage = v_level
        and coalesce(pos_ter_cod,0) in (
              select ter_cod from terrain
              left join monstre_terrain mt on mt.tmon_ter_cod=ter_cod and mt.tmon_gmon_cod=v_gmon_cod
              left join monstre_terrain mtd on mtd.tmon_ter_cod=-1 and mtd.tmon_gmon_cod=v_gmon_cod
              where ter_cod>=0 and coalesce(coalesce(mt.tmon_accessible, mtd.tmon_accessible), CASE WHEN ter_cod=0 THEN 'O' else 'N'end) = 'O'
              order by random()
        )
        order by random()
        limit 1;
  end if;

  -- Si pas de portail trouvé, pour les monstre on pop au hazard (pas pour les montures sauf si aucun portail a monture suer l'étage)
	if pos_portail is null and (v_type_portail=8 or v_nb_portail=0) then
		pos_portail := pos_aleatoire(v_level);
	end if;
	if pos_portail is null then 
		code_retour := 0;
		return code_retour;
	end if;
	select into 	v_nom,v_for,v_dex,v_int,v_con,v_race,v_temps_tour,v_des_regen,
						v_valeur_regen,v_vue,v_niveau,v_amelioration_vue,v_amelioration_regen,v_amelioration_degats,v_amelioration_armure,v_nb_des_degats,v_val_des_degats,
						v_or,v_arme,v_armure,v_dist,v_vampirisme,v_nb_recep,v_code_arme_serie,v_code_armure_serie, v_voie_magique, v_sex
		gmon_nom,gmon_for,gmon_dex,gmon_int,gmon_con,gmon_race_cod,gmon_temps_tour,gmon_des_regen,
		gmon_valeur_regen,gmon_vue,gmon_niveau,gmon_amelioration_vue,gmon_amelioration_regen,gmon_amelioration_degats,gmon_amelioration_armure,gmon_nb_des_degats,gmon_val_des_degats,
		coalesce(gmon_or,0),gmon_arme,gmon_armure,gmon_amel_deg_dist,gmon_vampirisme,gmon_nb_receptacle,gmon_serie_arme_cod,gmon_serie_armure_cod,COALESCE(gmon_voie_magique,0) gmon_voie_magique, COALESCE(gmon_sex,'')
		from monstre_generique
		where gmon_cod = v_gmon;
		-- genre
	if 	v_sex != '' then
	  v_genre := v_sex ;
	elseif lancer_des(1,2) > 1 then
		v_genre := 'M';
	else
		v_genre := 'F';
	end if;
	v_nom := choisir_monstre_nom(v_gmon,v_genre);
	v_nom := v_nom||' (n° '||trim(to_char(v_code_perso,'99999999'))||')';
	/****************************************************/
	/* Un peu d'aléatoire ?                             */
	/****************************************************/
	-- caracs
	if v_level != 0 then
		v_for := max(v_for+(lancer_des(1,7)-4),6);
		v_dex := max(v_dex+(lancer_des(1,7)-4),6);
		v_int := max(v_int+(lancer_des(1,7)-4),6);
		v_con := max(v_con+(lancer_des(1,7)-4),6);
		-- temps de tour
		v_temps_tour := max(v_temps_tour+(lancer_des(1,100)-50),360);
		-- regen
		v_amelioration_regen := max(v_valeur_regen+(lancer_des(1,7)-4),0);
		-- niveau
		v_niveau := max(v_niveau+(lancer_des(1,8)-4),1);
		-- degats
		v_amelioration_degats := max(v_amelioration_degats+(lancer_des(1,7)-4),0);
		-- armure
		v_amelioration_armure := max(v_amelioration_armure+(lancer_des(1,5)-3),0);
	end if;
	/*****************************************************/
	/* Insertion dans la bonne table                     */
	/*****************************************************/
	insert into perso 	(perso_cod,perso_sex,perso_nom,perso_for,perso_dex,perso_int,perso_con,perso_race_cod,perso_temps_tour,perso_des_regen,perso_valeur_regen,perso_vue,
								perso_niveau,perso_amelioration_vue,perso_amelioration_regen,perso_amelioration_degats,perso_amelioration_armure,perso_actif,perso_type_perso,perso_gmon_cod,perso_dirige_admin,perso_sta_combat,perso_sta_hors_combat,
								perso_amel_deg_dex,perso_vampirisme,perso_utl_pa_rest)
		values	(v_code_perso,v_genre,v_nom,v_for,v_dex,v_int,v_con,v_race,v_temps_tour,v_des_regen,
						v_valeur_regen,v_vue,v_niveau,v_amelioration_vue,v_amelioration_regen,v_amelioration_degats,v_amelioration_armure,'N',2,v_gmon,'N','N','N',
						v_dist,v_vampirisme,0);
/************************************/
/* Etape 2 : on lance le cree_perso */
/************************************/ 
	retour_cree_perso := cree_perso(v_code_perso);
	if retour_cree_perso != 0 then
		code_retour := retour_cree_perso;
		return code_retour;
	end if;
/*****************************************/
/* Etape 3 : on remet les bonnes valeurs */
/*****************************************/
-- Bleda 17/1/11 Calcul des pv en fonction du niveau.
	v_pv := v_con * 2 + v_niveau - 1 + lancer_des(v_niveau - 1, cast((v_con/4) as integer));
	update perso
		set perso_niveau = v_niveau,
		perso_temps_tour = v_temps_tour,
		perso_des_regen = v_des_regen,
		perso_valeur_regen = v_valeur_regen,
		perso_vue = v_vue,
		perso_pv = v_pv,
		perso_pv_max = v_pv,
		perso_amelioration_vue = v_amelioration_vue,
		perso_amelioration_regen = v_amelioration_regen,
		perso_amelioration_degats = v_amelioration_degats,
		perso_amelioration_armure = v_amelioration_armure,
		perso_nb_des_degats = v_nb_des_degats,
		perso_val_des_degats = v_val_des_degats,
		perso_amel_deg_dex = v_dist,
		perso_actif= 'O',
		perso_kharma = -10,
		perso_avatar = (select gmon_avatar from monstre_generique where gmon_cod = v_gmon),
		perso_description = (select gmon_description from monstre_generique where gmon_cod = v_gmon),
		perso_po = lancer_des(1,v_or),
		perso_taille = (select gmon_taille from monstre_generique where gmon_cod = v_gmon),
		perso_nb_receptacle = v_nb_recep,
		perso_voie_magique = v_voie_magique
		where perso_cod = v_code_perso;
	update perso set perso_px = limite_niveau_actuel(v_code_perso),perso_dlt = perso_dlt - '12 hours'::interval where perso_cod = v_code_perso;
/*****************************************/
/* Etape 4 : choix d'une arme            */
/*****************************************/
	if v_code_arme_serie is not null then
		-- choix d'une arme dans une serie
		select into v_arme	serie_choisir_objet(v_code_arme_serie);
		if v_arme is not null then
			select into objet_etat_min,objet_etat_max
				seequo_etat_min,seequo_etat_max
				from  	serie_equipement_objet
				where seequo_seequ_cod = v_code_arme_serie
				and seequo_gobj_cod = v_arme;
			objet_etat_min = min(objet_etat_min + lancer_des(1,objet_etat_max - objet_etat_min),100);
			v_code_arme := nextval('seq_obj_cod');
			insert into objets (obj_cod,obj_gobj_cod,obj_etat) values (v_code_arme,v_arme,objet_etat_min);
			insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
			values (nextval('seq_perobj_cod'),v_code_perso,v_code_arme,'O','O');
		end if;
	else
		if v_arme is not null then
			v_code_arme := nextval('seq_obj_cod');
			insert into objets (obj_cod,obj_gobj_cod) values (v_code_arme,v_arme);
			insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
			values (nextval('seq_perobj_cod'),v_code_perso,v_code_arme,'O','O');
		end if;
	end if;
/*****************************************/
/* Etape 4 : choix d'une armure          */
/*****************************************/
	if v_code_armure_serie is not null then
		-- choix d'une arme dans une serie
		select into v_armure	serie_choisir_objet(v_code_armure_serie);
		if v_armure is not null then
			select into objet_etat_min,objet_etat_max
				seequo_etat_min,seequo_etat_max
				from  	serie_equipement_objet
				where seequo_seequ_cod = v_code_armure_serie
				and seequo_gobj_cod = v_armure;
			objet_etat_min = min(objet_etat_min + lancer_des(1,objet_etat_max - objet_etat_min),100);
			v_code_armure := nextval('seq_obj_cod');
			insert into objets (obj_cod,obj_gobj_cod,obj_etat) values (v_code_armure,v_armure,objet_etat_min);
			insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
			values (nextval('seq_perobj_cod'),v_code_perso,v_code_armure,'O','O');
		end if;
	else
		if v_armure is not null then
			v_code_armure := nextval('seq_obj_cod');
			insert into objets (obj_cod,obj_gobj_cod) values (v_code_armure,v_armure);
			insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
			values (nextval('seq_perobj_cod'),v_code_perso,v_code_armure,'O','O');
		end if;
	end if;
/* ANCIENNE VERSION*/
/*	if v_arme is not null then
		v_code_arme := nextval('seq_obj_cod');
		insert into objets (obj_cod,obj_gobj_cod) values (v_code_arme,v_arme);
		insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
		values (nextval('seq_perobj_cod'),v_code_perso,v_code_arme,'O','O');
	end if;
	if v_armure is not null then

		v_code_armure := nextval('seq_obj_cod');
		insert into objets (obj_cod,obj_gobj_cod) values (v_code_armure,v_armure);
		insert into perso_objets (perobj_cod,perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
		values (nextval('seq_perobj_cod'),v_code_perso,v_code_armure,'O','O');
	end if;
*/
	for ligne in select * from gmon_type_comp where gtypc_gmon_cod = v_gmon loop
		update perso_competences
		set pcomp_modificateur = max(ligne.gtypc_valeur+(lancer_des(1,50)-25),15)
		where pcomp_perso_cod = v_code_perso
		and pcomp_pcomp_cod in
			(select comp_cod from competences
			where comp_typc_cod = ligne.gtypc_typc_cod);
	end loop;
	for ligne_objet in select * from objets_monstre_generique where ogmon_gmon_cod = v_gmon loop
		if lancer_des(1,10000) <= ligne_objet.ogmon_chance then
			v_code_objet := nextval('seq_obj_cod');
			insert into objets(obj_cod,obj_gobj_cod) values (v_code_objet,ligne_objet.ogmon_gobj_cod);
			insert into perso_objets (perobj_perso_cod,perobj_obj_cod,perobj_identifie,perobj_equipe)
				values (v_code_perso,v_code_objet,'O','N');

			-- 2019/11/08 - ajout de la possibilité d'équiper du matériel !
      if ligne_objet.ogmon_equipe then
        -- tenter d'équpier
				perform equipe_objet(v_code_perso,v_code_objet);
      end if;

			if ligne_objet.ogmon_gobj_cod in ('723','829') then
        select into texte obj_text_texte from potions.objet_texte where obj_text_gobj_cod = ligne_objet.ogmon_gobj_cod order by random() limit 1;
        update objets set obj_description = texte where obj_cod = v_code_objet;
			end if;
		end if;
	end loop;
	for ligne_objet in select * from sorts_monstre_generique where sgmon_gmon_cod = v_gmon loop
		insert into perso_sorts (psort_perso_cod,psort_sort_cod)
			values (v_code_perso,ligne_objet.sgmon_sort_cod);
	end loop;
        compt := ajouter_comp_mon(v_code_perso, v_gmon);
/*************************/
/* Etape 4 : on le place */
/*************************/
	insert into perso_position (ppos_cod,ppos_pos_cod,ppos_perso_cod) 
		values (nextval('seq_ppos_cod'),pos_portail,v_code_perso);
	code_retour := v_code_perso;
	return code_retour;
end;
 

$BODY$;

ALTER FUNCTION public.cree_monstre(integer, integer)
    OWNER TO delain;

GRANT EXECUTE ON FUNCTION public.cree_monstre(integer, integer) TO delain;

GRANT EXECUTE ON FUNCTION public.cree_monstre(integer, integer) TO PUBLIC;
