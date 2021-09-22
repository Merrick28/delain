
--
-- Name: meca_declenchement(integer,integer,integer,integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION meca_declenchement(integer,integer,integer,integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction meca_declenchement   (caller)        */
/*************************************************/
declare
  v_meca_cod alias for $1;
  v_sens alias for $2;    -- 0 ou 1 = active, -1 = desactive, 2 = inverse
  v_meca_pos_cod alias for $3;
  v_perso_pos_cod alias for $4;
begin

  return meca_declenchement(v_meca_cod,v_sens,v_meca_pos_cod,v_perso_pos_cod, ARRAY[0]);

end;$$;

ALTER FUNCTION public.meca_declenchement(integer,integer,integer,integer) OWNER TO delain;



--
-- Name: meca_declenchement(integer,integer,integer,integer,json); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION meca_declenchement(integer,integer,integer,integer,integer[]) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction meca_declenchement                   */
/*************************************************/
-- Comme une activation de mecanisme peut en entrainer une autre, on s'assure qu'il n'y a pas de boucle infinie en passant la liste des mecas déja activés!
declare
  v_meca_cod alias for $1;
  v_sens alias for $2;    -- 0 ou 1 = active, -1 = desactive, 2 = inverse
  v_meca_pos_cod alias for $3;  -- si -1 pour un mecanisme individuel, activation de toutes les positions
  v_perso_pos_cod alias for $4;
  v_meca_cod_list alias for $5;
  v_target_pos_cod integer;
  v_pmeca_actif integer;
  v_count integer;
  v_automap integer;

  v_meca_type character varying(1);
  v_meca_pos_etage integer;
  v_meca_pos_type_aff integer;
  v_meca_pos_decor integer;
  v_meca_pos_decor_dessus integer;
  v_meca_pos_passage_autorise integer;
  v_meca_pos_modif_pa_dep integer;
  v_meca_pos_ter_cod integer;
  v_meca_mur_type integer;
  v_meca_mur_tangible character varying(1);
  v_meca_mur_illusion character varying(1);
  v_meca_si_active json;
  v_meca_si_desactive json;
  v_meca_list json;
  v_ea_list json;
  ligne record;                -- Une ligne d’enregistrements
  row record;                -- Une row d’enregistrements
  v_fonc_cod integer;
  v_nb_cible integer;
  v_type_cible character varying(1);
  v_arr_type_cible integer[];

begin

  -- pour éviter le boulce infinie, on sort si le méca a déja été traité
  if v_meca_cod_list is not null and ARRAY[v_meca_cod]@>v_meca_cod_list then
      return -99 ;  -- break loop !
  end if;

  -- position de la case ciblé si mécanisme du type 'Individuel'
  v_target_pos_cod := COALESCE(NULLIF(v_meca_pos_cod,0), v_perso_pos_cod) ;
  v_automap := 0 ;  -- maj de l'automap requis !


  -- -------------------------------------------------------------------------------------------------------------------
  -- rechercher la ou les cases consernées et voir leur état actuel!
  select meca_pos_etage, meca_type, meca_pos_type_aff,  meca_pos_decor, meca_pos_decor_dessus, meca_pos_passage_autorise, meca_pos_modif_pa_dep, meca_pos_ter_cod, meca_mur_type, meca_mur_tangible, meca_si_active, meca_si_desactive
      into v_meca_pos_etage, v_meca_type, v_meca_pos_type_aff,  v_meca_pos_decor, v_meca_pos_decor_dessus, v_meca_pos_passage_autorise, v_meca_pos_modif_pa_dep, v_meca_pos_ter_cod, v_meca_mur_type, v_meca_mur_tangible, v_meca_si_active, v_meca_si_desactive
      from meca where meca_cod=v_meca_cod ;

  -- calculer l'état actuel du mécnisme
  if (v_meca_type = 'G') then

      select pmeca_actif into v_pmeca_actif from meca_position where pmeca_meca_cod=v_meca_cod limit 1 ;
      if not found then
          return -1;    -- position de mécanisme non trouvé
      end if;

  else
      if v_target_pos_cod = -1 then
          -- cas d'un meca individuel pour lequel on va activer/desactiver/inverser toutes les positions
          perform meca_declenchement(v_meca_cod,v_sens,pmeca_pos_cod,v_perso_pos_cod) from meca_position where pmeca_meca_cod=v_meca_cod ;
          return 0 ;
      else
          -- cas d'un meca indivisuel sur une seul position
          select pmeca_actif into v_pmeca_actif from meca_position where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod = v_target_pos_cod limit 1 ;
          if not found then
              return -1;    -- position de mécanisme non trouvé
          end if;
      end if;

  end if ;

  -- -------------------------------------------------------------------------------------------------------------------
  -- traiter le sens du déclenchement activer/desactiver
  if (v_pmeca_actif=0) and (v_sens>=0) then
      -- cas d'une activation (ou inversion)
      if (v_meca_type = 'G') then

          -- =============================================== ACTIVATION GRAPPE =========================================
          -- traiter les cases de base!
          update positions set
                pos_type_aff = coalesce(v_meca_pos_type_aff, pos_type_aff),
                pos_decor = coalesce(v_meca_pos_decor, pos_decor),
                pos_decor_dessus = coalesce(v_meca_pos_decor_dessus, pos_decor_dessus),
                pos_passage_autorise = coalesce(v_meca_pos_passage_autorise, pos_passage_autorise),
                pos_modif_pa_dep = coalesce(v_meca_pos_modif_pa_dep, pos_modif_pa_dep),
                pos_ter_cod = coalesce(v_meca_pos_ter_cod, pos_ter_cod)
              from meca_position
              where pos_cod=pmeca_pos_cod and pmeca_meca_cod = v_meca_cod ;

          -- mecanisme qui supprime des murs de base!
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and meca_mur_type = -1;
          if v_count>0 then
              delete from murs where mur_pos_cod in (
                  select pmeca_pos_cod from meca_position join meca on meca_cod=pmeca_meca_cod where pmeca_meca_cod=v_meca_cod and meca_mur_type = -1
              ) ;
              v_automap := 1 ;  -- à cause de la suppression de mur !
          end if;
          
          -- traiter les murs existants!
          update murs set
                mur_type = coalesce(v_meca_mur_type, mur_type),
                mur_tangible = coalesce(v_meca_mur_tangible, mur_tangible),
                mur_illusion = coalesce(v_meca_mur_illusion, mur_illusion)
              from meca_position
              where mur_pos_cod=pmeca_pos_cod and pmeca_meca_cod = v_meca_cod ;

          -- ajouter les eventuels nouveaux murs !
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod left join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and mur_pos_cod is null and meca_mur_type > 0 ;
          if v_count>0 then

              insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion)
                  select pmeca_pos_cod as mur_pos_cod, meca_mur_type as mur_type, COALESCE(meca_mur_tangible, 'O') as mur_tangible, COALESCE(meca_mur_illusion,'N') as mur_illusion
                    from meca_position
                    join meca on meca_cod=pmeca_meca_cod
                    left join murs on mur_pos_cod=pmeca_pos_cod
                    where pmeca_meca_cod=v_meca_cod and mur_pos_cod is null and meca_mur_type is not null ;
              v_automap := 1 ;  -- à cause de l'ajout de mur !
          end if;


          -- indique comme activé!
          update meca_position set pmeca_actif=1 where pmeca_meca_cod = v_meca_cod ;

      else

          -- =============================================== ACTIVATION INDIVIDUELLE =====================================
          update positions set
                pos_type_aff = coalesce(v_meca_pos_type_aff, pos_type_aff),
                pos_decor = coalesce(v_meca_pos_decor, pos_decor),
                pos_decor_dessus = coalesce(v_meca_pos_decor_dessus, pos_decor_dessus),
                pos_passage_autorise = coalesce(v_meca_pos_passage_autorise, pos_passage_autorise),
                pos_modif_pa_dep = coalesce(v_meca_pos_modif_pa_dep, pos_modif_pa_dep),
                pos_ter_cod = coalesce(v_meca_pos_ter_cod, pos_ter_cod)
              where pos_cod=v_target_pos_cod  ;

          select count(*) into v_count from meca left join murs on mur_pos_cod=v_target_pos_cod where meca_cod=v_meca_cod and mur_pos_cod is null and meca_mur_type > 0 ;
          if v_count>0 then
          
              -- ajout d'un mur par le mecanisme
              insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion)
                  select v_target_pos_cod as mur_pos_cod, meca_mur_type as mur_type, COALESCE(meca_mur_tangible, 'O') as mur_tangible, COALESCE(meca_mur_illusion,'N') as mur_illusion
                    from meca where meca_cod=v_meca_cod  ;
              v_automap := 1 ;  -- à cause de l'ajout de mur !

          else
             
              select count(*) into v_count from meca join murs on mur_pos_cod=v_target_pos_cod where meca_cod=v_meca_cod and meca_mur_type = -1 ;
              if v_count>0 then
              
                  -- suppression d'un mur par le mecanisme
                  delete from murs where mur_pos_cod = v_target_pos_cod ;
                  v_automap := 1 ;  -- à cause de la suppression de mur !

              else
              
                  -- modification d'un mur par le mecanisme
                  update murs set
                      mur_type = coalesce(v_meca_mur_type, mur_type),
                      mur_tangible = coalesce(v_meca_mur_tangible, mur_tangible),
                      mur_illusion = coalesce(v_meca_mur_illusion, mur_illusion)
                    where mur_pos_cod=v_target_pos_cod ;
                    
              end if;

          end if;

          -- indique comme activé!
          update meca_position set pmeca_actif=1 where pmeca_meca_cod = v_meca_cod and pmeca_pos_cod = v_target_pos_cod ;

      end if;

      -- activer les mecanismes différés (ou liés)
      v_meca_list := (v_meca_si_active->>'meca')::json;
      for ligne in (select value from json_array_elements(v_meca_list) )
      loop
          if f_to_numeric(ligne.value->>'meca_cod')>0 and f_to_numeric(ligne.value->>'meca_delai')=0 then

              v_meca_cod_list := v_meca_cod_list  || ARRAY[v_meca_cod] ;
              perform meca_declenchement(f_to_numeric(ligne.value->>'meca_cod')::integer,f_to_numeric(ligne.value->>'meca_sens')::integer,v_meca_pos_cod,v_perso_pos_cod, v_meca_cod_list );

          elseif f_to_numeric(ligne.value->>'meca_cod')>0 then

              INSERT INTO meca_action( ameca_meca_cod, ameca_date_action, ameca_sens_action,  ameca_pos_cod)
                    VALUES (f_to_numeric(ligne.value->>'meca_cod'), NOW()+ f_to_numeric(ligne.value->>'meca_delai') * '1 hour'::interval , f_to_numeric(ligne.value->>'meca_sens'),  v_target_pos_cod);
          end if;
      end loop;

      -- déclencher les ea
      v_ea_list := (v_meca_si_active->>'ea')::json;
      for ligne in (select value from json_array_elements(v_ea_list) )
      loop
          if f_to_numeric(ligne.value->>'fonc_cod')>0  then
              v_type_cible := coalesce(ligne.value->>'cible', '') ;
              if v_type_cible = 'J' then
                  v_arr_type_cible := ARRAY[1];
              elsif v_type_cible = 'L' then
                  v_arr_type_cible := ARRAY[3];
              elsif v_type_cible = 'M' then
                  v_arr_type_cible := ARRAY[2];
              elsif v_type_cible = 'P' then
                  v_arr_type_cible := ARRAY[1,3];
              elsif v_type_cible = 'T' then
                  v_arr_type_cible := ARRAY[1,2,3];
              else
                v_type_cible := '';
              end if;

              -- si type de cible pour un porteur d'EA est valide
              if ( v_type_cible != '' ) then
                  v_fonc_cod := f_to_numeric(ligne.value->>'fonc_cod') ;
                  v_nb_cible := coalesce(nullif(f_to_numeric(ligne.value->>'nb_cible'),0),1000) ;


                  if (v_meca_type = 'G') then
                      -- declenchement de l'EA sur les persos de chaque case du mecanisme
                      for row in (select pmeca_pos_cod from meca_position where pmeca_meca_cod=v_meca_cod )
                      loop
                          perform execute_fonctions(perso_cod, perso_cod, 'DEP', json_build_object('ea_fonc_cod',v_fonc_cod,'ea_pos_cod',row.pmeca_pos_cod))
                              from perso_position join perso on perso_cod=ppos_perso_cod
                              where ppos_pos_cod=row.pmeca_pos_cod and perso_actif='O' and perso_type_perso = ANY (v_arr_type_cible)
                              order by random() limit v_nb_cible ;

                      end loop;
                  else
                      -- declenchement de l'EA sur les perso de la case consernée
                      perform execute_fonctions(perso_cod, perso_cod, 'DEP', json_build_object('ea_fonc_cod',v_fonc_cod,'ea_pos_cod',v_target_pos_cod))
                          from perso_position join perso on perso_cod=ppos_perso_cod
                          where ppos_pos_cod=v_target_pos_cod and perso_actif='O' and perso_type_perso = ANY (v_arr_type_cible)
                          order by random() limit v_nb_cible ;
                  end if;
              end if;
          end if;
      end loop;

  -- -------------------------------------------------------------------------------------------------------------------
  elsif (v_pmeca_actif=1) and (v_sens=-1 or v_sens=2) then
      -- cas d'une désactivation (ou inversion)
      if (v_meca_type = 'G') then

          -- =============================================== DEACTIVATION GRAPPE =======================================
          -- retour à l'état normal des cases          
          update positions set
                pos_type_aff = pmeca_base_pos_type_aff,
                pos_decor = pmeca_base_pos_decor,
                pos_decor_dessus = pmeca_base_pos_decor_dessus,
                pos_passage_autorise = pmeca_base_pos_passage_autorise,
                pos_modif_pa_dep = pmeca_base_pos_modif_pa_dep,
                pos_ter_cod = pmeca_base_pos_ter_cod
              from meca_position
              where pos_cod=pmeca_pos_cod and pmeca_meca_cod = v_meca_cod ;

          -- supprimer les eventuels murs qui avaient été ajouté par le mécanisme!
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and pmeca_base_mur_type is null and meca_mur_type > 0 ;
          if v_count>0 then

              delete from murs where mur_pos_cod in (
                  select mur_pos_cod from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and pmeca_base_mur_type is null and meca_mur_type > 0
              ) ;
              v_automap := 1 ;  -- à cause de la suppression de mur !

          end if;

          -- traiter les murs existants et restants!
          update murs set
                mur_type = coalesce(pmeca_base_mur_type, mur_type),
                mur_tangible = coalesce(pmeca_base_mur_tangible, mur_tangible),
                mur_illusion = coalesce(pmeca_base_mur_illusion, mur_illusion)
              from meca_position
              where mur_pos_cod=pmeca_pos_cod and pmeca_meca_cod = v_meca_cod ;

          -- ajouter les murs qui avaient été supprimés par le mecanisme
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod left join murs on mur_pos_cod=pmeca_pos_cod where mur_pos_cod is null and pmeca_meca_cod=v_meca_cod and pmeca_base_mur_type is not null and meca_mur_type = -1 ;
          if v_count>0 then

              insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion)
                  select pmeca_pos_cod as mur_pos_cod, pmeca_base_mur_type as mur_type, COALESCE(pmeca_base_mur_tangible,'O') as mur_tangible, COALESCE(pmeca_base_mur_illusion,'N') as mur_illusion
                      from meca_position join meca on meca_cod=pmeca_meca_cod left join murs on mur_pos_cod=pmeca_pos_cod where mur_pos_cod is null and pmeca_meca_cod=v_meca_cod and pmeca_base_mur_type is not null and meca_mur_type = -1  ;
              v_automap := 1 ;  -- à cause de la suppression de mur !

          end if;

          -- indique comme activé!
          update meca_position set pmeca_actif=0 where pmeca_meca_cod = v_meca_cod ;

      else

          -- =============================================== DEACTIVATION INDIVIDUELLE ===================================
          -- retour à l'état normal de la case
          update positions set
                pos_type_aff = pmeca_base_pos_type_aff,
                pos_decor = pmeca_base_pos_decor,
                pos_decor_dessus = pmeca_base_pos_decor_dessus,
                pos_passage_autorise = pmeca_base_pos_passage_autorise,
                pos_modif_pa_dep = pmeca_base_pos_modif_pa_dep,
                pos_ter_cod = pmeca_base_pos_ter_cod
              from meca_position
              where pmeca_meca_cod = v_meca_cod and pmeca_pos_cod=v_target_pos_cod and pos_cod=v_target_pos_cod ;

          -- retour à l'état normal du mur
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod=v_target_pos_cod and pmeca_base_mur_type is null and meca_mur_type > 0 ;
          if v_count>0 then

              -- supression d'un mur qui avait été ajouté par le mecanisme
              delete from murs where mur_pos_cod = v_target_pos_cod ;
              v_automap := 1 ;  -- à cause de la suppression de mur !

          else

              select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod=v_target_pos_cod and pmeca_base_mur_type is not null and meca_mur_type = -1 ;
              if v_count>0 then
              
                  -- remise en état d'un mur qui avait été supprimé par le mecanisme
                  insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion)
                      select v_target_pos_cod as mur_pos_cod, pmeca_base_mur_type as mur_type, COALESCE(pmeca_base_mur_tangible,'O') as mur_tangible, COALESCE(pmeca_base_mur_illusion,'N') as mur_illusion
                          from meca_position where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod=v_target_pos_cod ;
                  v_automap := 1 ;  -- à cause de l'ajout de mur !
              
              else
                
                  -- retour à la normal d'un mur qui avait été modifié par le mecanisme
                  update murs set
                      mur_type = coalesce(pmeca_base_mur_type, mur_type),
                      mur_tangible = coalesce(pmeca_base_mur_tangible, mur_tangible),
                      mur_illusion = coalesce(pmeca_base_mur_illusion, mur_illusion)
                    from meca_position
                    where pmeca_meca_cod = v_meca_cod and pmeca_pos_cod=v_target_pos_cod and mur_pos_cod=v_target_pos_cod ;
                  
              end if;

          end if;

          -- indique comme déactivé!
          update meca_position set pmeca_actif=0 where pmeca_meca_cod = v_meca_cod and pmeca_pos_cod = v_target_pos_cod ;

      end if;

      -- activer les mecanismes différés  (ou liés)
      v_meca_list := (v_meca_si_desactive->>'meca')::json;
      for ligne in (select value from json_array_elements(v_meca_list) )
      loop
          if f_to_numeric(ligne.value->>'meca_cod')>0 and f_to_numeric(ligne.value->>'meca_delai')=0 then

              v_meca_cod_list := v_meca_cod_list  || ARRAY[v_meca_cod] ;
              perform meca_declenchement(f_to_numeric(ligne.value->>'meca_cod')::integer,f_to_numeric(ligne.value->>'meca_sens')::integer,v_meca_pos_cod,v_perso_pos_cod, v_meca_cod_list );

          elseif f_to_numeric(ligne.value->>'meca_cod')>0 then

              INSERT INTO meca_action( ameca_meca_cod, ameca_date_action, ameca_sens_action,  ameca_pos_cod)
                    VALUES (f_to_numeric(ligne.value->>'meca_cod'), NOW()+ f_to_numeric(ligne.value->>'meca_delai') * '1 hour'::interval , f_to_numeric(ligne.value->>'meca_sens'),  v_target_pos_cod);

          end if;
      end loop;

      -- déclencher les ea
      v_ea_list := (v_meca_si_desactive->>'ea')::json;
      for ligne in (select value from json_array_elements(v_ea_list) )
      loop
          if f_to_numeric(ligne.value->>'fonc_cod')>0  then

              v_type_cible := coalesce(ligne.value->>'cible', '') ;
              if v_type_cible = 'J' then
                  v_arr_type_cible := ARRAY[1];
              elsif v_type_cible = 'L' then
                  v_arr_type_cible := ARRAY[3];
              elsif v_type_cible = 'M' then
                  v_arr_type_cible := ARRAY[2];
              elsif v_type_cible = 'P' then
                  v_arr_type_cible := ARRAY[1,3];
              elsif v_type_cible = 'T' then
                  v_arr_type_cible := ARRAY[1,2,3];
              else
                v_type_cible := '';
              end if;

              -- si type de cible pour un porteur d'EA est valide
              if ( v_type_cible != '' ) then
                  v_fonc_cod := f_to_numeric(ligne.value->>'fonc_cod') ;
                  v_nb_cible := coalesce(nullif(f_to_numeric(ligne.value->>'nb_cible'),0),1000) ;

                  if (v_meca_type = 'G') then
                      -- declenchement de l'EA sur les persos de chaque case du mecanisme
                      for row in (select pmeca_pos_cod from meca_position where pmeca_cod=v_meca_cod )
                      loop
                          perform execute_fonctions(perso_cod, perso_cod, 'DEP', json_build_object('ea_fonc_cod',v_fonc_cod,'ea_pos_cod',row.pmeca_pos_cod))
                              from perso_position join perso on perso_cod=ppos_perso_cod
                              where ppos_pos_cod=row.pmeca_pos_cod and perso_actif='O' and perso_type_perso = ANY (v_arr_type_cible)
                              order by random() limit v_nb_cible ;
                      end loop;
                  else
                      -- declenchement de l'EA sur les persos de la case consernée
                      perform execute_fonctions(perso_cod, perso_cod, 'DEP', json_build_object('ea_fonc_cod',v_fonc_cod,'ea_pos_cod',v_target_pos_cod))
                          from perso_position join perso on perso_cod=ppos_perso_cod
                          where ppos_pos_cod=v_target_pos_cod and perso_actif='O' and perso_type_perso = ANY (v_arr_type_cible)
                          order by random() limit v_nb_cible ;
                  end if;

              end if;

          end if;
      end loop;

  else
      return -2;   -- mécanisme déjà dans l'état demandé (ou sens de déclenchement invalide)
  end if;

  -- maj de l'automap si des murs ont été modifiés!
  if v_automap > 0 then
      perform init_automap(v_meca_pos_etage);  -- à cause du changement des murs !
  end if; 


  return 0;

end;$$;

ALTER FUNCTION public.meca_declenchement(integer,integer,integer,integer,integer[]) OWNER TO delain;



