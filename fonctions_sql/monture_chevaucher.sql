--
-- Name: monture_chevaucher(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function monture_chevaucher(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  v_monture alias for $2;
  code_retour text;
  v_perso_pa integer;
  v_perso_cod integer;
  v_monture_pos_cod integer;
  v_monture_nom text;
  v_perso_pos_cod integer;
  v_gmon_monture text;
begin


  select ppos_pos_cod, perso_pa into v_perso_pos_cod, v_perso_pa from perso join perso_position on ppos_perso_cod=perso_cod where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_pa < 4 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select ppos_pos_cod, gmon_monture, perso_nom into v_monture_pos_cod, v_gmon_monture, v_monture_nom from perso
      join perso_position on ppos_perso_cod=perso_cod
      join monstre_generique on gmon_cod=perso_gmon_cod
      where perso_cod=v_monture ;
  if not found then
    return '<p>Erreur ! La monture n''a pas été trouvée !';
  end if;

  if v_gmon_monture <> 'O' then
    return '<p>Erreur ! Ce que vous essayez de chevaucher n''est pas une monture !';
  end if;

  if v_monture_pos_cod <> v_perso_pos_cod then
    return '<p>Erreur ! Votre monture est trop loin !';
  end if;

  select perso_cod into v_perso_cod from perso where perso_monture=v_monture ;
  if found then
    return '<p>Erreur ! Cette monture a déjà un cavalier !';
  end if;

  -- Réaliser les actions du chevauchement !!!
  update perso set perso_pa = perso_pa - 4, perso_monture=v_monture where perso_cod=v_perso ;

  return '<p>Désormais, vous chevauchez: ' || v_monture_nom || ' !';

end;
$_$;


ALTER FUNCTION public.monture_chevaucher(integer, integer) OWNER TO delain;