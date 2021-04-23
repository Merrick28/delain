--
-- Name: monture_ordonner(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function monture_ordonner(integer, varchar(3), json) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  v_ordre alias for $2;
  v_param alias for $3;
  code_retour text;

  v_perso_pa integer;
  v_monture integer;
  v_monture_nom text;
  temp_competence text;   -- text du jet de compétence
  des integer ;     -- jet de dé (incident facheux)


begin
	code_retour := '';

  select perso_monture, perso_pa into v_monture, v_perso_pa from perso where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_pa < 2 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select perso_nom into v_monture_nom from perso where perso_cod=v_monture ;
  if not found then
    return '<p>Erreur ! la monture n''a pas été trouvée !';
  end if;


  -- code_retour := code_retour|| ' dir=' || ( COALESCE( (((v_param)->>'dir')::text) , '1:2')::text) ;
  -- code_retour := code_retour|| ' dist=' || ( COALESCE( (((v_param)->>'dist')::text)::numeric , 0)::text) ;

  -- Test de compétence équitation (difficulté 0) => gère le la consommation de PA
  temp_competence := monture_competence(v_perso, 3, v_monture, 0);
  code_retour := code_retour||split_part(temp_competence,';',3);

  -- Test sur le jet de compétence
  if split_part(temp_competence,';',1) = '1' then
      code_retour := code_retour || '<p>Vous avez donner un ordre avec succès à ' || v_monture_nom || ' !<br>';

      -- evenement déchevaucher (107)
      perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a donné un ordre à sa monture [cible].', 'O', NULL);

  else
      -- si echec du jet de compétence
      code_retour := code_retour||'<br><p>Vous n’avez pas réussi à donner un ordre à ' || v_monture_nom || '!<br>';

  end if;



  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_ordonner(integer, varchar(3), json) OWNER TO delain;