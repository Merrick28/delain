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
  v_param_ia json ;     --  json avec les ordres: distance, etc..
  v_param_monture json ;     --  json avec les compteurs
  v_nb_ordre integer ;     --  nombre d'ordre déjà donné
  v_num integer ;     --  N° d'ordre actuel pour calcul
  v_type_ordre text ;     --  Type d'ordre: diriger, sauter, tolonner
  v_num_ordre integer ;     --  N° d'ordre actuel
  v_num_prems integer ;     --  N° du 1er ordre
  v_difficulte integer ;     -- difficulté de l'ordre
  v_nb_action integer;   -- nombre d'echec d'ordre
  v_cout_ordre integer;   -- cout des ordres de monture
  v_count_talonner numeric ; -- pour mise à jour des compteur de talonnade
  v_count_sauter numeric ; -- pour mise à jour des compteur de saut
  v_ordre_old json;   -- 1 ordre pour comptage des actions en cas de supression ou midification

begin
	code_retour := '';

  select perso_monture, perso_pa into v_monture, v_perso_pa from perso where perso_cod=v_perso ;
  if not found then
    return '<p>Erreur ! Le perso n''a pas été trouvé !';
  end if;

  select COALESCE(f_to_numeric(etage_monture->>'pa_action'::text),4) into v_cout_ordre from perso
    join perso_position on ppos_perso_cod=perso_cod
    join positions on pos_cod=ppos_pos_cod
    join etage on etage_numero=pos_etage
    where perso_cod=v_perso ;


  if (v_perso_pa < 2 and v_ordre = 'DEL') or (v_perso_pa < v_cout_ordre and v_ordre != 'DEL') then
    return '<p>Erreur ! Vous n''avez pas suffisament de PA !';
  end if;

  select perso_nom, COALESCE((perso_misc_param->>'ia_monture_ordre')::json, '[]'::json), COALESCE((perso_misc_param->>'ia_monture')::json, '[]'::json) into v_monture_nom, v_param_ia, v_param_monture from perso where perso_cod=v_monture ;
  if not found then
    return '<p>Erreur ! la monture n''a pas été trouvée !';
  end if;

	select count(*), coalesce(max(f_to_numeric(value->>'ordre')) , 0), coalesce(min(f_to_numeric(value->>'ordre')) , 0) into v_nb_ordre, v_num_ordre, v_num_prems from json_array_elements (v_param_ia);

  -- On autorise un seul échec d'ajout d'ordre par DLT, après celui-ci la monture refuse tout ordre supplémentaire !!!!
  select pnbact_nombre into v_nb_action from perso_nb_action where pnbact_perso_cod=v_perso and pnbact_action = 'EQI-ordonner' ;
  if not found then
      insert into perso_nb_action(pnbact_perso_cod, pnbact_action, pnbact_nombre, pnbact_date_derniere_action) values(v_perso, 'EQI-ordonner', 0, now());
  elsif v_nb_action > 1 AND  ( f_to_numeric(v_param->>'num_ordre') > v_num_prems OR (f_to_numeric(v_param->>'num_ordre') = 0 AND v_nb_ordre > 0) ) then
      return '<p>Votre monture est <b>devenue incontrolable</b>, excepté le premier ordre, vous ne pouvez plus modifier ou donner de nouveaux ordres pendant cette DLT !';
  end if;


  -- traitement de la difficulté de l'ordre
  if v_ordre = 'ADD' or v_ordre = 'UPD' then
      v_difficulte := 5 * v_nb_ordre ;    -- 5% de difficulté par ordre au dessus du premier

      -- Test de compétence équitation => gère le la consommation de PA
      temp_competence := monture_competence(v_perso, 3, v_monture, v_difficulte);
      code_retour := code_retour||split_part(temp_competence,';',3);

  elsif v_ordre = 'DEL'  then
      v_difficulte := 0 ;                 -- pas de difficulté pour la suppression
      temp_competence := '1;2;';          -- pas de test de compétence, toujours une réussite standard!
      update perso set perso_pa = perso_pa  - 2 where perso_cod = v_perso;
  else
      return '<p>Erreur ! Type d''ordre inconnu !';
  end if;


  -- Test sur le jet de compétence
  if split_part(temp_competence,';',1) = '1' then

      v_count_talonner := coalesce(f_to_numeric( (v_param_monture::jsonb)->>'nb_talonner')  , 0) ;
      v_count_sauter := coalesce(f_to_numeric( (v_param_monture::jsonb)->>'nb_sauter') , 0) ;

      -- réaliser l'action !!!
      if v_ordre = 'ADD' then
          code_retour := code_retour || '<p>Vous avez donner un ordre avec succès à ' || v_monture_nom || ' !<br>';

          -- evenement déchevaucher (107)
          perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a donné un ordre à [cible].', 'O', NULL);

          -- ajouter un ordre à la fin de la liste des ordres
          v_num_ordre := v_num_ordre + 1 ;
          v_type_ordre := coalesce(v_param->>'type_ordre', 'DIRIGER') ;
          v_num := f_to_numeric(v_param->>'num_ordre') ;
          dist :=  f_to_numeric(v_param->>'dist') ;     --  ordre: distance
          dir_x :=  f_to_numeric(v_param->>'dir_x') ;     --  ordre: dir x
          dir_y :=  f_to_numeric(v_param->>'dir_y') ;     --  ordre: dir y

          if v_type_ordre = 'TALONNER' then
              v_count_talonner := v_count_talonner + 1 ;
          end if;
          if v_type_ordre = 'SAUTER' then
              v_count_sauter := v_count_sauter + 1 ;
          end if;

          if ( v_num != 0) then
              -- ajout de +1 sur chaque ordre supérieur à celui-ci programmé
              select jsonb_agg ( v::jsonb || jsonb_build_object('ordre', CASE WHEN (v->>'ordre')::integer<v_num THEN (v->>'ordre')::integer ELSE (v->>'ordre')::integer+1 END) )
                  into v_param_ia  from ( select  json_array_elements( v_param_ia ) as v ) s ;
              -- le nouveau numero d'ordre
              v_num_ordre := v_num ;
          end if;

          -- mise à jour des ordres de la monture
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb)
                              || (json_build_object( 'ia_monture' , (json_build_object( 'nb_talonner' , v_count_talonner, 'nb_sauter', v_count_sauter )::jsonb))::jsonb)
                              || (json_build_object( 'ia_monture_ordre' , ((v_param_ia::jsonb)  || (json_build_object( 'ordre' , v_num_ordre, 'type_ordre', v_type_ordre, 'dir_x' , dir_x, 'dir_y' , dir_y , 'dist' , dist )::jsonb)))::jsonb)
              where perso_cod=v_monture ;

      elseif v_ordre = 'UPD' then
          code_retour := code_retour || '<p>Vous avez modifié un ordre avec succès pour ' || v_monture_nom || ' !<br>';

          -- evenement déchevaucher (107)
          perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a modifié un ordre pour [cible].', 'O', NULL);

          -- ajouter un ordre à la fin de la liste des ordres
          v_num_ordre := f_to_numeric(v_param->>'num_ordre') ;
          v_type_ordre := coalesce(v_param->>'type_ordre', 'DIRIGER') ;
          dist :=  f_to_numeric(v_param->>'dist') ;     --  ordre: distance
          dir_x :=  f_to_numeric(v_param->>'dir_x') ;     --  ordre: dir x
          dir_y :=  f_to_numeric(v_param->>'dir_y') ;     --  ordre: dir y

          -- mise à jour des compteur d'action speciales
          select v into v_ordre_old from (   select json_array_elements( (perso_misc_param->>'ia_monture_ordre')::json ) as v from perso where perso_cod=v_monture   ) as s where (v->>'ordre')::integer=v_num_ordre limit 1 ;
          if coalesce(v_ordre_old->>'type_ordre','DIRIGER') = 'TALONNER' then
              v_count_talonner := GREATEST(0, v_count_talonner - 1 );
          end if;
          if coalesce(v_ordre_old->>'type_ordre','DIRIGER') = 'SAUTER' then
              v_count_sauter := GREATEST(0, v_count_sauter - 1 );
          end if;
          if v_type_ordre = 'TALONNER' then
              v_count_talonner := v_count_talonner + 1 ;
          end if;
          if v_type_ordre = 'SAUTER' then
              v_count_sauter := v_count_sauter + 1 ;
          end if;

          -- dabord supprimer l'ordre existant
          select coalesce(jsonb_agg(v), '[]'::jsonb) into v_param_ia from (  select  json_array_elements( v_param_ia ) as v ) s where v->>'ordre' <> v_num_ordre ;

          -- mise à jour des ordres de la monture
           update perso
               set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'ia_monture_ordre' , (v_param_ia::jsonb))::jsonb)
              where perso_cod=v_monture ;

          -- Ajout du nouvel ordres de la monture
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb)
                              || (json_build_object( 'ia_monture' , (json_build_object( 'nb_talonner' , v_count_talonner, 'nb_sauter', v_count_sauter )::jsonb))::jsonb)
                              || (json_build_object( 'ia_monture_ordre' , ((v_param_ia::jsonb) || (json_build_object( 'ordre' , v_num_ordre, 'type_ordre', v_type_ordre, 'dir_x' , dir_x, 'dir_y' , dir_y , 'dist' , dist )::jsonb)))::jsonb)
              where perso_cod=v_monture ;

      else
          code_retour := code_retour || '<p>Vous avez supprimé avec succès un ordre qui avait été donné à ' || v_monture_nom || ' !<br>';

          -- evenement déchevaucher (107)
          perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a supprimé un ordre qui avait été donné à [cible].', 'O', NULL);

          v_num_ordre := f_to_numeric(v_param->>'num_ordre') ;    -- ordre à supprimer
          select v into v_ordre_old from (   select json_array_elements( (perso_misc_param->>'ia_monture_ordre')::json ) as v from perso where perso_cod=v_monture   ) as s where (v->>'ordre')::integer=v_num_ordre limit 1 ;

          if coalesce(v_ordre_old->>'type_ordre','DIRIGER') = 'TALONNER' then
              v_count_talonner := GREATEST(0, v_count_talonner - 1 );
          end if;
          if coalesce(v_ordre_old->>'type_ordre','DIRIGER') = 'SAUTER' then
              v_count_sauter := GREATEST(0, v_count_sauter - 1 );
          end if;

          -- supprimer un ordre de la liste des ordres
          select jsonb_agg(v) into v_param_ia from (  select  json_array_elements( v_param_ia ) as v ) s where v->>'ordre' <> v_num_ordre ;


          -- mise à jour des ordres de la monture
          update perso
              set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb)
                              || (json_build_object( 'ia_monture' , (json_build_object( 'nb_talonner' , v_count_talonner, 'nb_sauter', v_count_sauter )::jsonb))::jsonb)
                              || (json_build_object( 'ia_monture_ordre' , (v_param_ia::jsonb))::jsonb)
              where perso_cod=v_monture ;

      end if;

      -- si on a ajouté, modifié ou supprimé un ordre on lance immédiatement l'IA pour traiter une talonnade s'il y en a maintenant une en tant que premier ordre
      perform ia_monture(v_monture, 2) ;

  else
      -- si echec du jet de compétence
      if v_ordre = 'ADD' or v_ordre = 'UPD'  then

          if v_num_ordre > 0 then
              update perso_nb_action set pnbact_nombre=pnbact_nombre+1, pnbact_date_derniere_action=now() where pnbact_perso_cod=v_perso and pnbact_action = 'EQI-ordonner' ;
          end if;

          -- en cas d'echc critique on donne un ordre aléatoire
          if split_part(temp_competence,';',2) = '0' then
                code_retour := code_retour||'<br><p>Vous avez donné un ordre à ' || v_monture_nom || ', mais <b>votre monture ne l''a pas compris!</b><br>';

                -- evenement déchevaucher (107)
                perform insere_evenement(v_perso, v_monture, 107, '[attaquant] a donné un ordre à sa monture [cible] qui ne l''a pas compris.', 'O', NULL);

                -- mise à jour des ordres de la monture
                v_num := f_to_numeric(v_param->>'num_ordre') ;
                v_num_ordre := v_num_ordre + 1 ;
                dist :=  f_to_numeric(v_param->>'dist') ;     --  ordre: distance

                if v_ordre = 'ADD' and v_num!=0 then
                        -- ajout de +1 sur chaque ordre supérieur à celui-ci programmé
                        select jsonb_agg ( v::jsonb || jsonb_build_object('ordre', CASE WHEN (v->>'ordre')::integer<v_num THEN (v->>'ordre')::integer ELSE (v->>'ordre')::integer+1 END) )
                            into v_param_ia  from ( select  json_array_elements( v_param_ia ) as v ) s ;
                        -- le nouveau numero d'ordre
                        v_num_ordre := v_num ;
                elseif v_ordre = 'UPD' then
                        -- d'abord supprimer l'ordre à modifier
                        select COALESCE(jsonb_agg(v),'[]'::jsonb) into v_param_ia from (  select  json_array_elements( v_param_ia ) as v ) s where v->>'ordre' <> v_num ;
                        v_num_ordre := v_num ;
                end if;

                update perso
                    set perso_misc_param = COALESCE(perso_misc_param::jsonb, '{}'::jsonb) || (json_build_object( 'ia_monture_ordre' , ((v_param_ia::jsonb) || (json_build_object( 'ordre' , v_num_ordre, 'type_ordre', 'DIRIGER', 'dir_x' , 0, 'dir_y' , 0 , 'dist' , dist )::jsonb)))::jsonb)
                    where perso_cod=v_monture ;

          else
                code_retour := code_retour||'<br><p>Vous n’avez pas réussi à donner un ordre à ' || v_monture_nom || '!<br>';
          end if;

      else
          code_retour := code_retour||'<br><p>Vous n’avez pas réussi à supprimer un ordre de ' || v_monture_nom || '!<br>';
      end if;
  end if;



  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_ordonner(integer, varchar(3), json) OWNER TO delain;
