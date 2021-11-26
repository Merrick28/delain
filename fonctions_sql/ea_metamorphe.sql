--
-- Name: ea_metamorphe(integer, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_metamorphe(integer, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_metamorphe                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du monstre)           */
/*   $2 = Probabilité d’atteindre chaque cible    */
/*   $3 = Message d’événement associé             */
/*   $4 = Paramètre additionnels                  */
/**************************************************/
declare
  -- Parameters
  v_source alias for $1;
  v_proba alias for $2;
  v_texte_evt alias for $3;
  v_params alias for $4;

  v_code_perso integer;   -- alias de v_source (pour recupération de l'attribution équipement)
  v_source_nom text;
  v_source_pv integer;
  v_source_pv_max integer;
  v_gmon integer ;
  code_retour text;

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
  v_voie_magique integer;
  v_avatar text;
  v_description text;
  v_taille numeric;
  v_sex text;
	v_genre text;
	objet_etat_min integer;
	objet_etat_max integer;
	v_fonc_cod integer;
	v_temp integer;

	ligne record;
begin

  v_code_perso := v_source ;
  
  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « métamorphose ».';
    return '';
  end if;
  -- Initialisation des conteneurs
  code_retour := '';

  -- type perso (seuls les monstres sont métamorphes)
  select into v_source_nom, v_source_pv, v_source_pv_max
              perso_nom, perso_pv, perso_pv_max
      from perso where perso_cod = v_source and perso_type_perso=2;
  if not found then
      return '';
  end if;

  v_gmon = f_tirage_aleatoire_liste('gmon_cod', 'taux', (v_params->>'fonc_trig_monstre')::json) ;

  -- se transformer en generique du monstre
  if v_gmon > 0 then

        select into 	v_nom,v_for,v_dex,v_int,v_con,v_race,v_temps_tour,v_des_regen,
                      v_valeur_regen,v_vue,v_niveau,v_amelioration_vue,v_amelioration_regen,v_amelioration_degats,v_amelioration_armure,v_nb_des_degats,v_val_des_degats,
                      v_or,v_arme,v_armure,v_dist,v_vampirisme,v_nb_recep,v_code_arme_serie,v_code_armure_serie, v_voie_magique, v_sex,
                      v_avatar, v_description, v_taille
                gmon_nom,gmon_for,gmon_dex,gmon_int,gmon_con,gmon_race_cod,gmon_temps_tour,gmon_des_regen,
                gmon_valeur_regen,gmon_vue,gmon_niveau,gmon_amelioration_vue,gmon_amelioration_regen,gmon_amelioration_degats,gmon_amelioration_armure,gmon_nb_des_degats,gmon_val_des_degats,
                coalesce(gmon_or,0),gmon_arme,gmon_armure,gmon_amel_deg_dist,gmon_vampirisme,gmon_nb_receptacle,gmon_serie_arme_cod,gmon_serie_armure_cod,COALESCE(gmon_voie_magique,0) gmon_voie_magique, COALESCE(gmon_sex,''),
                gmon_avatar, gmon_description, gmon_taille
            from monstre_generique
            where gmon_cod = v_gmon;

        code_retour := code_retour|| '<br />' || v_source_nom || ' se métamorphose en ' || v_nom || '.';

        if (coalesce(v_params->>'fonc_trig_nom'::text, '') != '') and (v_source_nom is not null) then

                v_source_nom:= substr(v_source_nom, 1, COALESCE(NULLIF(strpos(v_source_nom, ' (n°')-1,-1), char_length(v_source_nom))) ;
                update perso set perso_nom = replace(replace(v_params->>'fonc_trig_nom'::text,'[nom_generique]',v_nom), '[nom]', v_source_nom) ||' (n° '||trim(to_char(perso_cod,'99999999'))||')' where perso_cod=v_source;
        end if;


        /*****************************************/
        /* les caracs */
        /*****************************************/
        v_pv := v_con * 2 + v_niveau - 1 + lancer_des(v_niveau - 1, cast((v_con/4) as integer));
        update perso
            set perso_niveau = v_niveau,
            perso_for = v_for,
            perso_dex = v_dex,
            perso_int = v_int,
            perso_con = v_con,
            perso_race_cod = v_race,
            perso_temps_tour = v_temps_tour,
            perso_des_regen = v_des_regen,
            perso_valeur_regen = v_valeur_regen,
            perso_vue = v_vue,
            perso_pv = v_pv * (v_source_pv::numeric / v_source_pv_max::numeric),
            perso_pv_max = v_pv,
            perso_amelioration_vue = v_amelioration_vue,
            perso_amelioration_regen = v_amelioration_regen,
            perso_amelioration_degats = v_amelioration_degats,
            perso_amelioration_armure = v_amelioration_armure,
            perso_nb_des_degats = v_nb_des_degats,
            perso_val_des_degats = v_val_des_degats,
            perso_amel_deg_dex = v_dist,
            perso_sex = v_sex,
            perso_avatar = v_avatar,
            perso_description = v_description,
            perso_taille = v_taille,
            perso_voie_magique = v_voie_magique,
            perso_gmon_cod = v_gmon
            where perso_cod = v_source;

        /* *************************************************************************** */
        /* on supprime l'équipement, pour recréer en conformité avec le nouveau monstre*/
        /* *************************************************************************** */
        perform f_del_objet(obj_cod)
            from perso_objets
            inner join objets on obj_cod=perobj_obj_cod
            inner join objet_generique on gobj_cod=obj_gobj_cod 
            where perobj_equipe='O' and perobj_perso_cod =  v_code_perso ;

        /* ******************************************************************************* */
        /* on supprime l'EA qui a généré la métamorphose si elle n'est pas lié au générique*/
        /* et qu'il n'est pas persistant, sinon s'assurer d'avoir la persistance           */
        /* ******************************************************************************* */
        if  COALESCE((v_params->>'fonc_trig_ea_persistant')::text, 'N') = 'N' then
            delete from fonction_specifique where fonc_nom = 'ea_metamorphe' and fonc_perso_cod = v_code_perso ;
        else
            v_fonc_cod := ((v_params->>'ea_fonc_cod')::text)::integer ;
            select fonc_perso_cod into v_temp from fonction_specifique where fonc_cod = v_fonc_cod and fonc_perso_cod = v_code_perso ;
            if not found then
                -- l'ea est persistant et le perso en a hérité de son générique, comme il vient de changer de generique on lui créé un nouvel ea dédié
                insert into fonction_specifique( fonc_nom, fonc_gmon_cod, fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_portee,  fonc_proba, fonc_message, fonc_nombre_cible, fonc_date_limite,  fonc_trigger_param)
                  select  fonc_nom, null as fonc_gmon_cod, v_code_perso as fonc_perso_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_portee,  fonc_proba, fonc_message, fonc_nombre_cible, fonc_date_limite,  fonc_trigger_param
                      from fonction_specifique where fonc_cod = v_fonc_cod ;
            end if;
        end if;

        /*****************************************/
        /* choix d'une arme            */
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
        /* Choix d'une armure          */
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

        /*****************************************/
        /* Apprentissage des nouvellse competences          */
        /*****************************************/
        perform ajouter_comp_mon(v_code_perso, v_gmon);

        /*****************************************/
        /* Oublier les anciens sorts et apprentissage des nouveaux */
        /*****************************************/
        delete from perso_sorts where psort_perso_cod = v_code_perso;
        for ligne in select * from sorts_monstre_generique where sgmon_gmon_cod = v_gmon
        loop
            insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_code_perso,ligne.sgmon_sort_cod);
        end loop;

        -- On rajoute la ligne d’événements
        if v_texte_evt != '' then
            if strpos(v_texte_evt , '[cible]') != 0 then
              perform insere_evenement(v_source, v_source, 54, v_texte_evt, 'O', 'N', null);
            else
              perform insere_evenement(v_source, v_source, 54, v_texte_evt, 'O', 'O', null);
            end if;
        end if;

  end if;


  -- if code_retour = '' then
  --   code_retour :=  'Pas d’effet de « métamorphose ».';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_metamorphe(integer, numeric, text, json) OWNER TO delain;

