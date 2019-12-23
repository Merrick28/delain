
--
-- Name: f_trg_after_insert_perso_objet(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_trg_after_insert_perso_objet() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_insert_perso_objet   */
/***********************************/
-- traitement des bonus/malus d'équipement: sur insertion dans perso_objet on verifie s'il faut aussi ajouter des bonus/malus d'équipement
declare
  v_gobj_cod integer;
  ligne record;

begin

  -- recupération du code d'objet générique
  select into v_gobj_cod obj_gobj_cod from objets where obj_cod=NEW.perobj_obj_cod;

	for ligne in
		select objbm_cod, tbonus_libc, objbm_bonus_valeur from objets_bm join bonus_type on tbonus_cod=objbm_tbonus_cod where objbm_gobj_cod=v_gobj_cod or objbm_obj_cod=NEW.perobj_obj_cod
	loop
	  -- ajout des bonus (si l'objet est équipé)
	  if  NEW.perobj_equipe='O' then

	    perform ajoute_bonus_equipement(NEW.perobj_perso_cod, ligne.tbonus_libc, ligne.objbm_cod, NEW.perobj_obj_cod, ligne.objbm_bonus_valeur);

	  end if;

	end loop;

	return NEW;
end;
 $$;



ALTER FUNCTION public.f_trg_after_insert_perso_objet() OWNER TO delain;

DROP TRIGGER IF EXISTS f_trg_after_insert_perso_objet ON perso_objets ;

CREATE TRIGGER f_trg_after_insert_perso_objet AFTER INSERT ON perso_objets FOR EACH ROW EXECUTE PROCEDURE f_trg_after_insert_perso_objet();
