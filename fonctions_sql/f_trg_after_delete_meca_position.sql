
DROP FUNCTION IF EXISTS public.f_trg_after_delete_meca_position() CASCADE ;

--
-- Name: f_trg_after_delete_meca_position(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.f_trg_after_delete_meca_position() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_delete_meca_position   */
/***********************************/
-- suppression d'un mecanisme d'étage, retour à la "map" normale
declare
    v_count integer;
    v_target_pos_cod integer;
begin

    -- si le mecanisme a été activé, il faut le désactiver avant suppression
    if OLD.pmeca_actif = 1 then
          v_target_pos_cod := OLD.pmeca_pos_cod ;

         -- =============================================== DEACTIVATION INDIVIDUELLE ===================================
          -- retour à l'état normal de la case
          update positions set
                pos_type_aff = OLD.pmeca_base_pos_type_aff,
                pos_decor = OLD.pmeca_base_pos_decor,
                pos_decor_dessus = OLD.pmeca_base_pos_decor_dessus,
                pos_passage_autorise = OLD.pmeca_base_pos_passage_autorise,
                pos_modif_pa_dep = OLD.pmeca_base_pos_modif_pa_dep,
                pos_ter_cod = OLD.pmeca_base_pos_ter_cod
              where pos_cod=v_target_pos_cod  ;

          -- retour à l'état normal du mur
          select count(*) into v_count from murs where mur_pos_cod=OLD.pmeca_pos_cod   ;
          if v_count>0 and OLD.pmeca_base_mur_type is null then

              -- supression d'un mur qui avait été ajouté par le mecanisme
              delete from murs where mur_pos_cod = v_target_pos_cod ;

          else

              if v_count=0 and OLD.pmeca_base_mur_type is not null then

                  -- remise en état d'un mur qui avait été supprimé par le mecanisme
                  insert into murs (mur_pos_cod, mur_type, mur_tangible, mur_illusion) VALUES (v_target_pos_cod, OLD.pmeca_base_mur_type, COALESCE(OLD.pmeca_base_mur_tangible,'O'), COALESCE(OLD.pmeca_base_mur_illusion,'N') ) ;

              else

                  -- retour à la normal d'un mur qui avait été modifié par le mecanisme
                  update murs set
                      mur_type = coalesce(OLD.pmeca_base_mur_type, mur_type),
                      mur_tangible = coalesce(OLD.pmeca_base_mur_tangible, mur_tangible),
                      mur_illusion = coalesce(OLD.pmeca_base_mur_illusion, mur_illusion)
                    where mur_pos_cod=v_target_pos_cod ;

              end if;

          end if;

    end if;

	return OLD;
end;
 $$;


ALTER FUNCTION public.f_trg_after_delete_meca_position() OWNER TO delain;

--
-- Name: f_trg_after_delete_meca_position; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_delete_meca_position AFTER DELETE ON public.meca_position FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_delete_meca_position();

