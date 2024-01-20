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
  v_dernier_cavalier integer;
  v_monture_pos_cod integer;
  v_monture_nom text;
  v_perso_pos_cod integer;
  v_perso_type_perso integer;
  v_gmon_monture text;
	v_max_pa integer ;  -- PA max  de la monture après chevauchement
  temp_competence text;   -- text du jet de compétence
  temp_txt text;   -- text divers
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


  select ppos_pos_cod, gmon_monture, perso_nom, coalesce(f_to_numeric(((perso_misc_param->>'monture_cavalier')::jsonb)->>'perso_cod')::integer, 0)
      into v_monture_pos_cod, v_gmon_monture, v_monture_nom, v_dernier_cavalier
      from perso
      join perso_position on ppos_perso_cod=perso_cod
      join monstre_generique on gmon_cod=perso_gmon_cod
      where perso_cod=v_monture and perso_actif='O';
  if not found then
    return '<p>Erreur ! La monture n''a pas été trouvée !';
  end if;

  if v_gmon_monture <> 'O' then
    return '<p>Erreur ! Ce que vous essayez de chevaucher n''est pas une monture !';
  end if;

  if (v_monture_pos_cod <> v_perso_pos_cod) and (distance(v_monture_pos_cod, v_perso_pos_cod)<>1 or v_dernier_cavalier<>v_perso) then
    return '<p>Erreur ! Votre monture est trop loin !';
  end if;

  select perso_cod into v_perso_cod from perso where perso_monture=v_monture ;
  if found then
    return '<p>Erreur ! Cette monture a déjà un cavalier !';
  end if;

  if (v_monture_pos_cod <> v_perso_pos_cod) then
      -- 6 PA nécéssaire pour siffler et chevaucher
      if v_perso_pa < 6 then
          return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
      end if;
      if  get_pa_dep_terrain(v_monture, v_perso_pos_cod) < 0 then
          return '<p>Erreur ! La monture ne peut pas venir sur ce terrain !';
      end if;

      -- on utilise 2 PA pour siffler la monture et la faire venir, cette action est faite sans jet de compétence
      update perso set perso_pa = perso_pa  - 2 where perso_cod = v_perso;
  end if;


  -- Test de compétence équitation (difficulté 0) => gère le la consommation de PA
  /* update perso set perso_pa = perso_pa  - 4 where perso_cod = v_perso; fait par le test de compétence */
  temp_competence := monture_competence(v_perso, 1, v_monture, 0);
  code_retour := code_retour||split_part(temp_competence,';',3);

  -- Test sur le jet de compétence
  if split_part(temp_competence,';',1) = '1' then
      -- faire l'action (jet de compétence reussi)
      code_retour := code_retour||'<br><p>Désormais, vous chevauchez: ' || v_monture_nom || ' !<br>';

      -- Réaliser les actions du chevauchement !!!
      update perso set perso_monture=v_monture where perso_cod=v_perso ;

      -- la monture était à une case, il faut la faire venir!
      if (v_monture_pos_cod <> v_perso_pos_cod) then
          update perso_position set ppos_pos_cod=v_perso_pos_cod where ppos_perso_cod=v_monture ;
      end if;

      -- memo du dernier cavalier de cette monture (permet le raccourci de chevaucher monture à une case)
      update perso set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'monture_cavalier' ,  (json_build_object( 'perso_cod', v_perso )::jsonb))::jsonb) where perso_cod=v_monture ;

      -- on va s'assurer que la monture n'a pas trop de PA dispo par rapport à sa DLT (sinon ça pourrait permettre un rush)
      temp_txt := calcul_dlt2(v_monture);
      select  (EXTRACT(EPOCH FROM ( perso_dlt - now()))::int/60::numeric / f_temps_tour_perso(perso_cod) * 12)::int into v_max_pa from perso where perso_cod = v_monture ;
      update perso set perso_pa=GREATEST(0, LEAST(perso_pa, v_max_pa)) where perso_cod=v_monture ;

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
