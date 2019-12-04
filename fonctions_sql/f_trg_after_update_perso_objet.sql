
--
-- Name: f_trg_after_update_perso_objet(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_trg_after_update_perso_objet() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_update_perso_objet   */
/***********************************/
-- traitement des bonus/malus d'équipement: sur insertion dans perso_objet on verifie s'il faut aussi ajouter des bonus/malus d'équipement (en cas d'équipemenr/desequipement)
declare
  v_gobj_cod integer;
  ligne record;

begin

  -- seulement si on a equipé ou deséquipé un objet
  if (OLD.perobj_equipe != NEW.perobj_equipe) then

    if NEW.perobj_equipe = 'N' then

        -- retirer les bonus d'équipement de l'objet !!!
        perform retire_bonus_equipement(NEW.perobj_perso_cod, NEW.perobj_obj_cod);

    else

      -- on a équipé un objet, ajouter des bonus d'équipement s'il y en a
      for ligne in
        select tbonus_libc, objbm_bonus_valeur from objets_bm join bonus_type on tbonus_cod=objbm_tbonus_cod where objbm_gobj_cod=v_gobj_cod or objbm_obj_cod=NEW.perobj_obj_cod
      loop
        -- ajout des bonus
        perform ajoute_bonus_equipement(NEW.perobj_perso_cod, ligne.tbonus_libc, NEW.perobj_obj_cod, ligne.objbm_bonus_valeur);

      end loop;

    end if;

  end if;

	return NEW;
end;
 $$;


ALTER FUNCTION public.f_trg_after_update_perso_objet() OWNER TO delain;

DROP TRIGGER IF EXISTS f_trg_after_update_perso_objet ON perso_objets ;

CREATE TRIGGER f_trg_after_update_perso_objet AFTER INSERT ON perso_objets FOR EACH ROW EXECUTE PROCEDURE f_trg_after_update_perso_objet();
