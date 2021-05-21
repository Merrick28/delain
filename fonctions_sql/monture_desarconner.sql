--
-- Name: monture_desarconner(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function monture_desarconner(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  v_cavalier alias for $2;
  code_retour text;
  v_perso_pa integer;
  v_perso_cod integer;
  v_cavalier_pos_cod integer;
  v_cavalier_nom text;
  v_perso_pos_cod integer;
  v_perso_type_perso integer;
  temp_competence text;   -- text du jet de compétence
  v_nb_action integer;   -- nombre de désarçonnage déjà fait dans le tour
begin
	code_retour := '';

  select ppos_pos_cod, perso_pa, perso_type_perso into v_perso_pos_cod, v_perso_pa, v_perso_type_perso from perso join perso_position on ppos_perso_cod=perso_cod where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_type_perso = 3 then
    return '<p>Erreur ! Les familiers ne peuvent pas désarconner !';
  end if;

  if v_perso_pa < 6 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select ppos_pos_cod, perso_nom into v_cavalier_pos_cod, v_cavalier_nom from perso
      join perso_position on ppos_perso_cod=perso_cod
      where perso_cod=v_cavalier and perso_actif='O' and perso_monture is not null;
  if not found then
    return '<p>Erreur ! Le cavalier à désarçonner n''a pas été trouvé!';
  end if;

  if v_cavalier_pos_cod <> v_perso_pos_cod then
    return '<p>Erreur ! Le cavalier à désarçonner est trop loin !';
  end if;

  -- verification du nombre de désarçonnage par tour !
  select pnbact_nombre into v_nb_action from perso_nb_action where pnbact_perso_cod=v_perso and pnbact_action = 'EQI-desarconner' ;
  if not found then
      insert into perso_nb_action(pnbact_perso_cod, pnbact_action, pnbact_nombre, pnbact_date_derniere_action) values(v_perso, 'EQI-desarconner', 1, now());
  elsif v_nb_action > 0 then
      return '<p>Erreur ! Vous ne pouvez désarçonner qu’une seule fois par tour !';
  else
      update perso_nb_action set pnbact_nombre=pnbact_nombre+1, pnbact_date_derniere_action=now() where pnbact_perso_cod=v_perso and pnbact_action = 'EQI-desarconner' ;
  end if;

  -- Test de compétence équitation (difficulté 0) => gère le la consommation de PA
  temp_competence := monture_competence(v_perso, 4, v_cavalier, 0);
  code_retour := code_retour||split_part(temp_competence,';',3);

  -- Test sur le jet de compétence
  if split_part(temp_competence,';',1) = '1' then
      -- faire l'action (jet de compétence reussi)
      code_retour := code_retour||'<br><p>Vous avez réussi à désarçonner: ' || v_cavalier_nom || ' !<br>';

      -- Réaliser les actions du désarçonnage !!!
      update perso set perso_monture=null where perso_cod=v_cavalier ;

      -- evenement désarçonnage (108)
      perform insere_evenement(v_perso, v_cavalier, 109, '[cible] a été désarçonner de sa monture par [attaquant]', 'O', NULL);

  else
      -- si echec du jet de compétence
      code_retour := code_retour||'<br><p>Vous n’avez pas réussi à désarçonner: ' || v_cavalier_nom || ' !<br>';
  end if;

  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_desarconner(integer, integer) OWNER TO delain;