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
  temp_competence text;   -- text du jet de compétence
begin
	code_retour := '';

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


  -- Test de compétence équitation (difficulté 0) => gère le la consommation de PA
  temp_competence := monture_competence(v_perso, 1, v_monture, 0);
  code_retour := code_retour||split_part(temp_competence,';',3);

  -- Test sur le jet de compétence
  if split_part(temp_competence,';',1) = '1' then
      -- faire l'action (jet de compétence reussi)
      code_retour := code_retour||'<br><p>Désormais, vous chevauchez: ' || v_monture_nom || ' !<br>';

      -- Réaliser les actions du chevauchement !!!
      update perso set perso_monture=v_monture where perso_cod=v_perso ;

      -- evenement chevaucher (105)
      perform insere_evenement(v_perso, v_monture, 105, '[attaquant] monte sur sa monture [cible].', 'O', NULL);

  else
      -- si echec du jet de compétence
      code_retour := code_retour||'<br><p>Vous n’avez pas réussi à chevaucher: ' || v_monture_nom || ' !<br>';
  end if;

  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_chevaucher(integer, integer) OWNER TO delain;