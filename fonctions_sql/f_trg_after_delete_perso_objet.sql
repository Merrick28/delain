--
-- Name: f_trg_after_delete_perso_objet(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_trg_after_delete_perso_objet() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_delete_perso_objet   */
/***********************************/
-- traitement des bonus/malus d'équipement: sur supression dans perso_objet on verifie s'il faut aussi supprimer les bonus/malus d'équipement
declare
  v_gobj_cod integer;
  ligne record;

begin

  perform retire_bonus_equipement(OLD.perobj_perso_cod, OLD.perobj_obj_cod);

	return OLD;
end;
 $$;



ALTER FUNCTION public.f_trg_after_delete_perso_objet() OWNER TO delain;

DROP TRIGGER IF EXISTS f_trg_after_delete_perso_objet ON perso_objets ;

CREATE TRIGGER f_trg_after_delete_perso_objet AFTER DELETE ON perso_objets FOR EACH ROW EXECUTE PROCEDURE f_trg_after_delete_perso_objet();
