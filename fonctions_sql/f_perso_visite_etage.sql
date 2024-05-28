
--
-- Name: f_perso_visite_etage(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION f_perso_visite_etage(integer, integer default null, integer default null) RETURNS numeric
    LANGUAGE plpgsql
    AS $$/************************************************
 fonction f_perso_visite_etage

   retourne le % de visite d'un etage ou autour d'une case

   on passe en paramètres :
    $1 = le perso_cod du perso qui fait la visite
    $2 = le pos_cod d'une des cases de l'étage ou null si c'est la position du perso
    $3 = la limit en nombre de case autour de la position donnée ou null pour tout l'étage
*************************************************/

declare
  v_perso_cod alias for $1;     -- perso dont on veut connaitre le % d'un etage ou autour d'une position
  v_pos_cod alias for $2;       -- si null le pos_cod est celui du perso
  v_limit alias for $3;         -- si null le % visité concerne tout l'étage

  v_etage integer ;             -- etage ciblé
  v_visite numeric ;            -- % etage vitité

  table_automap text;           -- nom de la table automap
  v_query text;                 -- query avec le nom de la table

begin

  if v_pos_cod is null then
      select ppos_pos_cod, etage_cod into v_pos_cod, v_etage from perso_position join positions on pos_cod=ppos_pos_cod join etage on etage_numero=pos_etage where ppos_perso_cod=v_perso_cod ;
  else
      select etage_cod into v_etage  from positions join etage on etage_numero=pos_etage where pos_cod=v_pos_cod ;
  end if;

  -- creation du nom de la table de l'automap pour létage conerné
  table_automap := 'perso_vue_pos_' || v_etage::text ;

  v_query := 'select ROUND(100*(sum(CASE WHEN pvue_perso_cod IS NULL THEN 0 ELSE 1 END)::numeric/count(*)::numeric),2)
                  from positions pc
                  join positions p on p.pos_etage = pc.pos_etage
                  left join ' || table_automap || ' on pvue_perso_cod=' || v_perso_cod::text || ' and pvue_pos_cod=p.pos_cod
                  where pc.pos_cod=' || v_pos_cod::text ;

  -- ajout de condition de limit autour d'une case ciblée, sinon tout l'étage est concerné
  if (v_limit is not null) then
      v_query := v_query  || ' and  p.pos_x > pc.pos_x - ' || v_limit::text
                          || ' and  p.pos_x < pc.pos_x + ' || v_limit::text
                          || ' and  p.pos_y > pc.pos_y - ' || v_limit::text
                          || ' and  p.pos_y < pc.pos_y + ' || v_limit::text ;
  end if;

	execute v_query INTO v_visite ;

  return v_visite;

end;$$;


ALTER FUNCTION public.f_perso_visite_etage(integer, integer, integer) OWNER TO delain;



