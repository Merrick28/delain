--
-- Name: monture_dechevaucher(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function monture_dechevaucher(integer) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  code_retour text;
  v_perso_pa integer;
  v_monture integer;
  v_monture_nom text;
begin


  select perso_monture, perso_pa into v_monture, v_perso_pa from perso where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_pa < 4 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select perso_nom into v_monture_nom from perso where perso_cod=v_monture ;
  if not found then
    v_monture_nom := 'monture inconnue' ;
  end if;


  -- Réaliser les actions du dé-chevauchement !!!
  update perso set perso_pa = perso_pa - 4, perso_monture=null where perso_cod=v_perso ;

  return '<p>Désormais, vous ne chevauchez plus: ' || v_monture_nom || ' !';

end;
$_$;


ALTER FUNCTION public.monture_dechevaucher(integer) OWNER TO delain;