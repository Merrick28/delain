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
  v_perso_type_perso integer;
  v_gmon_monture text;
  v_equitation integer;
begin


  select ppos_pos_cod, perso_pa, perso_type_perso into v_perso_pos_cod, v_perso_pa, v_perso_type_perso from perso join perso_position on ppos_perso_cod=perso_cod where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_type_perso = 3 then
    return '<p>Erreur ! Les familiers ne peuvent pas chevaucher !';
  end if;

  if v_perso_pa < 4 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select ppos_pos_cod, gmon_monture, perso_nom into v_monture_pos_cod, v_gmon_monture, v_monture_nom from perso
      join perso_position on ppos_perso_cod=perso_cod
      join monstre_generique on gmon_cod=perso_gmon_cod
      where perso_cod=v_monture and perso_actif='O';
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

  -- si le perso n'a pas de compétence équitation, on lui met 30% par défaut
  select pcomp_modificateur into v_equitation from perso_competences where pcomp_perso_cod = v_perso and pcomp_pcomp_cod=104 ;
  if not found then
      v_equitation:= 30 ;
      INSERT INTO perso_competences( pcomp_perso_cod, pcomp_pcomp_cod, pcomp_modificateur) VALUES (v_perso, 104, v_equitation);
  end if;

  -- Réaliser les actions du chevauchement !!!
  update perso set perso_pa = perso_pa - 4, perso_monture=v_monture where perso_cod=v_perso ;

  -- evenement chevaucher (105)
  perform insere_evenement(v_perso, v_monture, 105, '[attaquant] monte sur sa monture [cible].', 'O', NULL);

  return '<p>Désormais, vous chevauchez: ' || v_monture_nom || ' !';

end;
$_$;


ALTER FUNCTION public.monture_chevaucher(integer, integer) OWNER TO delain;