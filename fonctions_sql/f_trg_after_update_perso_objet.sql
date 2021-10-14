DROP TRIGGER f_trg_after_update_perso_objet ON perso_objets ;


-- Function: public.f_trg_after_update_perso_objet()

-- DROP FUNCTION public.f_trg_after_update_perso_objet();

CREATE OR REPLACE FUNCTION public.f_trg_after_update_perso_objet()
  RETURNS trigger AS
$BODY$/***********************************/
/* trigger f_trg_after_update_perso_objet   */
/***********************************/
-- traitement des bonus/malus d'équipement: sur mise à jour dans perso_objet on verifie s'il faut aussi ajouter/supprimer des bonus/malus d'équipement (en cas d'équipemenr/desequipement)
declare
  v_gobj_cod integer;
  ligne record;

begin
  -- RAISE NOTICE 'vector_update_customer invoked';

  -- seulement si on a equipé ou deséquipé un objet
  if (OLD.perobj_equipe != NEW.perobj_equipe) then

    -- recupération du code d'objet générique
    select into v_gobj_cod obj_gobj_cod from objets where obj_cod=NEW.perobj_obj_cod;

    -- boucle sur tous les BM donné par cet objet
    for ligne in
      select objbm_cod, tbonus_libc, objbm_bonus_valeur, objbm_equip_requis from objets_bm join bonus_type on tbonus_cod=objbm_tbonus_cod where (objbm_gobj_cod=v_gobj_cod or objbm_obj_cod=NEW.perobj_obj_cod) and objbm_equip_requis
    loop

        if NEW.perobj_equipe = 'N' then

            -- retirer les bonus/malus d'équipement de l'objet !!!
            perform retire_bonus_equipement(NEW.perobj_perso_cod, NEW.perobj_obj_cod, ligne.objbm_cod);

        else

            -- ajout des bonus/malus
            perform ajoute_bonus_equipement(NEW.perobj_perso_cod, ligne.tbonus_libc, ligne.objbm_cod, NEW.perobj_obj_cod, ligne.objbm_bonus_valeur);

        end if;

    end loop;

  end if;


	return NEW;
end;
 $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.f_trg_after_update_perso_objet()
  OWNER TO delain;

--
-- Name: perso_objets f_trg_after_update_perso_objet; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_update_perso_objet AFTER UPDATE ON public.perso_objets FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_update_perso_objet();

