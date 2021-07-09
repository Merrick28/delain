
--
-- Name: meca_ajout_pos(integer,integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION meca_ajout_pos(integer,integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$/*************************************************/
/* fonction meca_ajout_pos                      */
/*************************************************/
declare
  v_meca_cod alias for $1;
  v_pos_cod alias for $2;
  v_pmeca_cod integer;

begin

  select pmeca_cod INTO v_pmeca_cod from meca_position where pmeca_meca_cod=v_meca_cod and pmeca_pos_cod=v_pos_cod;
  if found then
      return 0 ;    -- déja fait
  end if;

  select pmeca_cod INTO v_pmeca_cod from meca_position where pmeca_pos_cod=v_pos_cod order by pmeca_cod limit 1;
  if found then
       -- on a déjà un autre mécanisme a cette position, ce nouveau a la même base que tous les autres (on ne prend pas les caracs de la position qui est peut-être déjà activé par un mecanisme)
       INSERT INTO meca_position ( pmeca_meca_cod, pmeca_pos_cod, pmeca_pos_etage, pmeca_base_pos_type_aff,pmeca_base_pos_decor, pmeca_base_pos_decor_dessus, pmeca_base_pos_passage_autorise, pmeca_base_pos_modif_pa_dep, pmeca_base_pos_ter_cod, pmeca_base_mur_type, pmeca_base_mur_tangible, pmeca_base_mur_illusion )
          SELECT v_meca_cod as pmeca_meca_cod, pmeca_pos_cod, pmeca_pos_etage, pmeca_base_pos_type_aff,pmeca_base_pos_decor, pmeca_base_pos_decor_dessus, pmeca_base_pos_passage_autorise, pmeca_base_pos_modif_pa_dep, pmeca_base_pos_ter_cod, pmeca_base_mur_type, pmeca_base_mur_tangible, pmeca_base_mur_illusion
          FROM meca_position WHERE pmeca_cod=v_pmeca_cod  ;

      return 2;

  else
       -- 1ere position d'un mecanisme a cett eposition
       INSERT INTO meca_position ( pmeca_meca_cod, pmeca_pos_cod, pmeca_pos_etage, pmeca_base_pos_type_aff,pmeca_base_pos_decor, pmeca_base_pos_decor_dessus, pmeca_base_pos_passage_autorise, pmeca_base_pos_modif_pa_dep, pmeca_base_pos_ter_cod, pmeca_base_mur_type, pmeca_base_mur_tangible, pmeca_base_mur_illusion )
          SELECT v_meca_cod as pmeca_meca_cod, pos_cod, pos_etage, pos_type_aff,pos_decor,pos_decor_dessus, pos_passage_autorise, pos_modif_pa_dep ,pos_ter_cod,mur_type,mur_tangible, mur_illusion
          FROM positions LEFT JOIN murs ON mur_pos_cod=pos_cod
          WHERE pos_cod=v_pos_cod  ;
      return 1;
  end if;

  return 0;

end;$$;

ALTER FUNCTION public.meca_ajout_pos(integer,integer) OWNER TO delain;



