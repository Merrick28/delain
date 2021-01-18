
DROP FUNCTION IF EXISTS public.f_trg_after_update_perso_position() CASCADE ;

--
-- Name: f_trg_after_update_perso_position(); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE FUNCTION public.f_trg_after_update_perso_position() RETURNS trigger
    LANGUAGE plpgsql
    AS $$/***********************************/
/* trigger f_trg_after_update_perso_position   */
/***********************************/
-- traitement des déplacement => trigger pour faire suivre la monture et son cavalier.
declare
	v_ppos_cod integer;
begin

  -- cas d'un joueur qui se déplace
  select ppos_cod into v_ppos_cod
      from perso as p
      join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
      join perso_position on ppos_perso_cod = m.perso_cod
      where p.perso_cod=NEW.ppos_perso_cod and p.perso_type_perso=1 and ppos_pos_cod<>NEW.ppos_pos_cod ;
  if found then
      -- le joueur a une monture active qui n'est pas sur ça case, on bouge sa monture !
      update perso_position set ppos_pos_cod=NEW.ppos_pos_cod where ppos_cod=v_ppos_cod ;
  end if;

  -- cas d'une monture qui se déplace
  select ppos_cod into v_ppos_cod
      from perso as m
      join perso as p on p.perso_monture = m.perso_cod and p.perso_actif = 'O' and p.perso_type_perso=1
      join perso_position on ppos_perso_cod = p.perso_cod
      where m.perso_cod=NEW.ppos_perso_cod and m.perso_type_perso=2 and ppos_pos_cod<>NEW.ppos_pos_cod ;
  if found then
      -- la monture bouge et le perso n'est pas sur sa case, on bouge le joureur qui sui sa monture !
      update perso_position set ppos_pos_cod=NEW.ppos_pos_cod where ppos_cod=v_ppos_cod ;
  end if;


	return NEW;
end;
 $$;


ALTER FUNCTION public.f_trg_after_update_perso_position() OWNER TO delain;

--
-- Name: perso_objets f_trg_after_update_perso_position; Type: TRIGGER; Schema: public; Owner: delain
--

CREATE TRIGGER f_trg_after_update_perso_position AFTER UPDATE ON public.perso_position FOR EACH ROW EXECUTE PROCEDURE public.f_trg_after_update_perso_position();

