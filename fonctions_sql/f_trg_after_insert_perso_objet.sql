DROP TRIGGER f_trg_after_insert_perso_objet ON perso_objets ;

--
-- Name: f_trg_after_insert_perso_objet(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.f_trg_after_insert_perso_objet() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_insert_perso_objet   */
/***********************************/
-- traitement des bonus/malus d'équipement: sur insertion dans perso_objet on verifie s'il faut aussi ajouter des bonus/malus d'équipement
declare
  v_gobj_cod integer;
  v_tobj_cod integer;
  ligne record;

begin

  -- recupération du code d'objet générique
  select into v_gobj_cod, v_tobj_cod obj_gobj_cod, gobj_tobj_cod from objets join objet_generique on gobj_cod=obj_gobj_cod where obj_cod=NEW.perobj_obj_cod;

  -- On ne peut posséder qu'un seul exemplaire des objets du type "Animation' (tobj_cod=44), suppression de ceux que l'on posséde déjà si on en prend un nouveau!
  if  v_tobj_cod = 44 then
      for ligne in
          select obj_cod from perso_objets join objets on obj_cod=perobj_obj_cod join objet_generique on gobj_cod=obj_gobj_cod where perobj_perso_cod=NEW.perobj_perso_cod and gobj_tobj_cod=44 and obj_cod!=NEW.perobj_obj_cod
      loop
          -- supprimer l'objet définitivement
          perform f_del_objet(ligne.obj_cod);
      end loop;
  end if;

  -- traitement des bonus d'objet
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

--
-- Name: f_trg_after_update_perso_objet(); Type: FUNCTION; Schema: public; Owner: delain
--

--
-- Name: perso_objets f_trg_after_insert_perso_objet; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_insert_perso_objet AFTER INSERT ON public.perso_objets FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_insert_perso_objet();

