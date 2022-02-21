DROP TRIGGER f_trg_after_delete_perso_objet ON perso_objets ;
--
-- Name: f_trg_after_delete_perso_objet(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.f_trg_after_delete_perso_objet() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_delete_perso_objet   */
/***********************************/
-- traitement des bonus/malus d'équipement: sur supression dans perso_objet on verifie s'il faut aussi supprimer les bonus/malus d'équipement
declare
  v_gobj_cod integer;
  ligne record;

begin

  perform retire_bonus_equipement(OLD.perobj_perso_cod, OLD.perobj_obj_cod, null);

	return OLD;
end;
 $$;


ALTER FUNCTION public.f_trg_after_delete_perso_objet() OWNER TO delain;

--
-- Name: perso_objets f_trg_after_update_perso_objet; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_delete_perso_objet AFTER DELETE ON public.perso_objets FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_delete_perso_objet();

