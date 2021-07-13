
--
-- Name: meca_declenchement(integer,integer,integer,integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION meca_declenchement(integer,integer,integer,integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction meca_declenchement                      */
/*************************************************/
declare
  v_meca_cod alias for $1;
  v_sens alias for $2;    -- 0 ou 1 = active, -1 = desactive, 2 = inverse
  v_meca_pos_cod alias for $3;
  v_perso_pos_cod alias for $4;
  v_target_pos_cod integer;
  v_pmeca_actif integer;
  v_count integer;

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

begin

  -- position de la case ciblé si mécanisme du type 'Individuel'
  v_target_pos_cod := COALESCE(v_meca_pos_cod, v_target_pos_cod) ;

  -- rechercher la ou les cases conserné et voir leur état actuel!
  select meca_pos_etage, meca_type, meca_pos_type_aff,  meca_pos_decor, meca_pos_decor_dessus, meca_pos_passage_autorise, meca_pos_modif_pa_dep, meca_pos_ter_cod, meca_mur_type, meca_mur_tangible
      into v_meca_pos_etage, v_meca_type, v_meca_pos_type_aff,  v_meca_pos_decor, v_meca_pos_decor_dessus, v_meca_pos_passage_autorise, v_meca_pos_modif_pa_dep, v_meca_pos_ter_cod, v_meca_mur_type, v_meca_mur_tangible
      from meca where meca_cod=v_meca_cod ;

  -- calculer l'état actuel du mécnisme
  if (v_meca_type = 'G') then

      select pmeca_actif into v_pmeca_actif from meca_position where pmeca_meca_cod=v_meca_cod limit 1 ;
      if not found then
          return -1;    -- position de mécanisme non trouvé
      end if;

  else

      select pmeca_actif into v_pmeca_actif from meca_position where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod = v_target_pos_cod limit 1 ;
      if not found then
          return -1;    -- position de mécanisme non trouvé
      end if;

  end if ;

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

          -- traiter les murs existants!
          update murs set
                mur_type = coalesce(v_meca_mur_type, mur_type),
                mur_tangible = coalesce(v_meca_mur_tangible, mur_tangible),
                mur_illusion = coalesce(v_meca_mur_illusion, mur_illusion)
              from meca_position
              where pos_cod=pmeca_pos_cod and pmeca_meca_cod = v_meca_cod ;

          -- ajouter les eventuels nouveaux murs !
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod left join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and mur_pos_cod is null and meca_mur_type is not null ;
          if v_count>0 then

              insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion)
                  select pmeca_pos_cod as mur_pos_cod, meca_mur_type as mur_type, COALESCE(meca_mur_tangible, 'O') as mur_tangible, COALESCE(meca_mur_illusion,'N') as mur_illusion
                    from meca_position
                    join meca on meca_cod=pmeca_meca_cod
                    left join murs on mur_pos_cod=pmeca_pos_cod
                    where pmeca_meca_cod=v_meca_cod and mur_pos_cod is null and meca_mur_type is not null ;

              perform init_automap(v_meca_pos_etage);  -- a cause du changement des murs !
          end if;

          -- indique comme activé!
          update meca_position set pmeca_actif=1 where pmeca_meca_cod = v_meca_cod ;

      else

          -- =============================================== ACTIVATION INDIVIDUEL =====================================
          update positions set
                pos_type_aff = coalesce(v_meca_pos_type_aff, pos_type_aff),
                pos_decor = coalesce(v_meca_pos_decor, pos_decor),
                pos_decor_dessus = coalesce(v_meca_pos_decor_dessus, pos_decor_dessus),
                pos_passage_autorise = coalesce(v_meca_pos_passage_autorise, pos_passage_autorise),
                pos_modif_pa_dep = coalesce(v_meca_pos_modif_pa_dep, pos_modif_pa_dep),
                pos_ter_cod = coalesce(v_meca_pos_ter_cod, pos_ter_cod)
              where pos_cod=v_target_pos_cod  ;

          select count(*) into v_count from meca left join murs on mur_pos_cod=v_target_pos_cod where meca_cod=v_meca_cod and mur_pos_cod is null and meca_mur_type is not null ;
          if v_count>0 then

              insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion)
                  select v_target_pos_cod as mur_pos_cod, meca_mur_type as mur_type, COALESCE(meca_mur_tangible, 'O') as mur_tangible, COALESCE(meca_mur_illusion,'N') as mur_illusion
                    from meca where meca_cod=v_meca_cod  ;

              perform init_automap(v_meca_pos_etage);  -- a cause du changement des murs !

          else

              update murs set
                  mur_type = coalesce(v_meca_mur_type, mur_type),
                  mur_tangible = coalesce(v_meca_mur_tangible, mur_tangible),
                  mur_illusion = coalesce(v_meca_mur_illusion, mur_illusion)
                where mur_pos_cod=v_target_pos_cod ;

          end if;


          -- indique comme activé!
          update meca_position set pmeca_actif=1 where pmeca_meca_cod = v_meca_cod and pmeca_pos_cod = v_target_pos_cod ;

      end if;

  elsif (v_pmeca_actif=1) and (v_sens=-1 or v_sens=2) then
      -- cas d'une désactivation (ou inversion)
      if (v_meca_type = 'G') then

          -- =============================================== DEACTIVATION GRAPPE =======================================
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
          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and pmeca_base_mur_type is null and meca_mur_type is not null ;
          if v_count>0 then

              delete from murs where mur_pos_cod in (
                  select mur_pos_cod from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and pmeca_base_mur_type is null and meca_mur_type is not null
              ) ;

              perform init_automap(v_meca_pos_etage);  -- a cause du changement des murs !
          end if;

          -- traiter les murs existants et restants!
          update murs set
                mur_type = coalesce(pmeca_base_mur_type, mur_type),
                mur_tangible = coalesce(pmeca_base_mur_tangible, mur_tangible),
                mur_illusion = coalesce(pmeca_base_mur_illusion, mur_illusion)
              from meca_position
              where mur_pos_cod=pmeca_pos_cod and pmeca_meca_cod = v_meca_cod ;

          -- indique comme activé!
          update meca_position set pmeca_actif=0 where pmeca_meca_cod = v_meca_cod ;

      else

          -- =============================================== DEACTIVATION INDIVIDUEL ===================================
          update positions set
                pos_type_aff = pmeca_base_pos_type_aff,
                pos_decor = pmeca_base_pos_decor,
                pos_decor_dessus = pmeca_base_pos_decor_dessus,
                pos_passage_autorise = pmeca_base_pos_passage_autorise,
                pos_modif_pa_dep = pmeca_base_pos_modif_pa_dep,
                pos_ter_cod = pmeca_base_pos_ter_cod
              where pos_cod=v_target_pos_cod  ;

          select count(*) into v_count from meca_position join meca on meca_cod=pmeca_meca_cod join murs on mur_pos_cod=pmeca_pos_cod where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod=v_target_pos_cod and pmeca_base_mur_type is null and meca_mur_type is not null ;
          if v_count>0 then

              delete from murs where mur_pos_cod = v_target_pos_cod ;
              perform init_automap(v_meca_pos_etage);  -- a cause du changement des murs !

          else

              update murs set
                  mur_type = coalesce(pmeca_base_mur_type, mur_type),
                  mur_tangible = coalesce(pmeca_base_mur_tangible, mur_tangible),
                  mur_illusion = coalesce(pmeca_base_mur_illusion, mur_illusion)
                where mur_pos_cod=v_target_pos_cod ;

          end if;

          -- indique comme activé!
          update meca_position set pmeca_actif=0 where pmeca_meca_cod = v_meca_cod and pmeca_pos_cod = v_target_pos_cod ;

      end if;

  else
      return -2;   -- mécanisme déjà dans l'état demandé (ou sens de déclenchement invalide)
  end if;

  return 0;

end;$$;

ALTER FUNCTION public.meca_declenchement(integer,integer,integer,integer) OWNER TO delain;



