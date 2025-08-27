--
-- Name: ea_modification_qa(integer, integer, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_modification_qa(integer, integer, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_modification_qa                             */
/* lancé par les effet auto                       */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du perso)             */
/*   $2 = Perso ciblé                             */
/*   $3 = Probabilité de declencehement           */
/*   $4 = Message d’événement associé             */
/*   $5 = Paramètre additionnels                  */
/**************************************************/
declare
  -- Parameters
  v_perso_cod alias for $1;
  v_cible_cod alias for $2;
  v_proba alias for $3;
  v_texte_evt alias for $4;
  v_params alias for $5;

  code_retour text;
  v_perso_nom text;
  v_cible_nom text;
  v_qa_cod integer;
  v_nb_qa integer;
  sens integer;
  taux numeric;
  ligne record;                -- Une ligne d’enregistrements

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « mécanisme ».';
    return '';
  end if;
  -- Initialisation des conteneurs
  code_retour := '' ;

  -- nom source et cible
  select perso_nom into v_perso_nom from perso where perso_cod = v_perso_cod;
  select perso_nom into v_cible_nom from perso where perso_cod = COALESCE( v_cible_cod,v_perso_cod) ;

  -- boucle sur la liste des mécanismes
  v_nb_qa := 0 ;  -- comptage des qa modifiées!
  for ligne in (select value from json_array_elements((v_params->>'fonc_trig_qa')::json)  )
  loop

      taux := f_to_numeric(ligne.value->>'taux'::text) / 100 ;
      sens := f_to_numeric(ligne.value->>'sens'::text)::integer;

      if (taux=0) or (random() <= taux) then

          -- activation/desactivation de la QA !!! sens: 0: activer, -1: desactiver, 2: inverser
          v_nb_qa := v_nb_qa + 1;
          v_qa_cod := f_to_numeric(ligne.value->>'qa_cod'::text)::integer;

          if sens = 2 then
                -- inverser
                update quetes.aquete set aquete_actif = CASE WHEN aquete_actif='O' THEN 'N' ELSE 'O' END where aquete_cod = v_qa_cod ;
          elsif sens = -1 then
                -- desactiver
                update quetes.aquete set aquete_actif='N' where aquete_cod = v_qa_cod ;
          else
                -- activer
                update quetes.aquete set aquete_actif='O' where aquete_cod = v_qa_cod ;
          end if;

      end if;
  end loop;

  -- On rajoute la ligne d’événements
  if v_texte_evt != '' and v_nb_qa > 0 then
        code_retour := code_retour || replace( replace( v_texte_evt, '[attaquant]', v_perso_nom),  '[cible]', v_cible_nom) ;
        if strpos(v_texte_evt , '[cible]') != 0 then
            perform insere_evenement(v_perso_cod, v_cible_cod, 54, v_texte_evt, 'O', 'N', null);
        else
            perform insere_evenement(v_perso_cod, v_cible_cod, 54, v_texte_evt, 'O', 'O', null);
        end if;
  end if;

  -- if code_retour = '' then
  --   code_retour :=  'Pas d’effet de « quete ».';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_modification_qa(integer, integer, numeric, text, json) OWNER TO delain;

