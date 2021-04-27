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
  dir_x integer ;     -- ordre: direction en x
  dir_y integer ;     --  ordre: direction en y
  dist integer ;     --  ordre: distance
  v_param_ia json ;     --  ordre: distance
  v_nb_ordre integer ;     --  nombre d'ordre déjà donné
  v_num_ordre integer ;     --  N° d'ordre actuel
  v_difficulte integer ;     -- difficulté de l'ordre

begin
	code_retour := '';

  select perso_monture, perso_pa into v_monture, v_perso_pa from perso where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  if v_perso_pa < 2 then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select perso_nom, COALESCE((perso_misc_param->>'ia_monture_ordre')::json, '[]'::json) into v_monture_nom, v_param_ia from perso where perso_cod=v_monture ;
  if not found then
    return '<p>Erreur ! la monture n''a pas été trouvée !';
  end if;

	select count(*), coalesce(max(f_to_numeric(value->>'ordre')) , 0) into v_nb_ordre, v_num_ordre from json_array_elements (v_param_ia);

  -- traitement de la difficulté de l'ordre
  if v_ordre = 'ADD' then
      v_difficulte := v_nb_ordre ;
  elsif v_ordre = 'DEL'  then
      v_difficulte := 0 ;
  else
      return '<p>Erreur ! Type d''ordre inconnu !';
  end if;

  -- Test de compétence équitation => gère le la consommation de PA
  temp_competence := monture_competence(v_perso, 3, v_monture, v_difficulte);
  code_retour := code_retour||split_part(temp_competence,';',3);

  -- Test sur le jet de compétence
  if split_part(temp_competence,';',1) = '1' then

      -- réaliser l'action !!!
      if v_ordre = 'ADD' then
          code_retour := code_retour || '<p>Vous avez donner un ordre avec succès pour ' || v_monture_nom || ' !<br>';

          -- evenement déchevaucher (107)
          perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a donné un ordre à sa monture [cible].', 'O', NULL);

          -- ajouter un ordre à la fin de la liste des ordres
          v_num_ordre := v_num_ordre + 1 ;
          dist :=  f_to_numeric(v_param->>'dist') ;     --  ordre: distance
          dir_x :=  f_to_numeric(v_param->>'dir_x') ;     --  ordre: distance
          dir_y :=  f_to_numeric(v_param->>'dir_y') ;     --  ordre: distance

          -- mise à jour des ordres de la monture
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'ia_monture_ordre' , ((v_param_ia::jsonb) || (json_build_object( 'ordre' , v_num_ordre, 'dir_x' , dir_x, 'dir_y' , dir_y , 'dist' , dist )::jsonb)))::jsonb)
              where perso_cod=v_monture ;
      else
          code_retour := code_retour || '<p>Vous avez supprimer un ordre avec succès pour ' || v_monture_nom || ' !<br>';

          -- evenement déchevaucher (107)
          perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a supprimé un ordre à sa monture [cible].', 'O', NULL);

          -- supprimer un ordre de la liste des ordres
          v_num_ordre := f_to_numeric(v_param->>'num_ordre') ;    -- ordre à supprimer
          select jsonb_agg(v) into v_param_ia from (  select  json_array_elements( v_param_ia ) as v from perso where perso_cod=v_monture ) s where v->>'ordre' <> v_num_ordre ;


          -- mise à jour des ordres de la monture
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'ia_monture_ordre' , (v_param_ia::jsonb))::jsonb)
              where perso_cod=v_monture ;
      end if;

  else
      -- si echec du jet de compétence
      if v_ordre = 'ADD' then
          code_retour := code_retour||'<br><p>Vous n’avez pas réussi à donner un ordre à ' || v_monture_nom || '!<br>';
      else
          code_retour := code_retour||'<br><p>Vous n’avez pas réussi à supprimer un ordre de ' || v_monture_nom || '!<br>';
      end if;
  end if;



  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_ordonner(integer, varchar(3), json) OWNER TO delain;