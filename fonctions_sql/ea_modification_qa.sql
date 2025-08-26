--
-- Name: ea_modification_qa(integer, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_modification_qa(integer, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_modification_qa                             */
/* lancé par les effet auto                       */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du perso)             */
/*   $2 = Probabilité de declencehement           */
/*   $3 = Message d’événement associé             */
/*   $4 = Paramètre additionnels                  */
/**************************************************/
declare
  -- Parameters
  v_perso_cod alias for $1;
  v_proba alias for $2;
  v_texte_evt alias for $3;
  v_params alias for $4;

  code_retour text;
  v_perso_nom text;
  v_ppos_pos_cod integer;
  v_ea_pos_cod integer;
  v_pos_cod integer;
  v_nb_meca integer;
  taux numeric;
  ligne record;                -- Une ligne d’enregistrements

begin

  -- Chances de déclencher l’effet
  if random() > v_proba then
    -- return 'Pas d’effet automatique de « mécanisme ».';
    return '';
  end if;
  -- Initialisation des conteneurs
  code_retour := '';

  -- Position et carac
  select perso_nom, ppos_pos_cod into v_perso_nom, v_ppos_pos_cod from perso join perso_position on ppos_perso_cod=perso_cod where perso_cod = v_perso_cod;
  v_ea_pos_cod := f_to_numeric(v_params->>'ea_pos_cod'::text);

  -- boucle sur la liste des mécanismes
  v_nb_meca := 0 ;  -- comptage des mecas declenchés!
  for ligne in (select value from json_array_elements((v_params->>'fonc_trig_meca')::json)  )
  loop
      taux := f_to_numeric(ligne.value->>'taux'::text) / 100 ;
      if (taux=0) or (random() <= taux) then
          -- declenchement du mecanisqme !!!  "{"meca_cod":"10","sens":"0","taux":"100","pos_cod":""}"
          v_nb_meca := v_nb_meca + 1;
          perform meca_declenchement( f_to_numeric(ligne.value->>'meca_cod'::text)::integer, f_to_numeric(ligne.value->>'sens'::text)::integer, f_to_numeric(ligne.value->>'pos_cod'::text)::integer, coalesce(nullif(v_ea_pos_cod,0), v_ppos_pos_cod) ) ;

      end if;
  end loop;

  -- On rajoute la ligne d’événements
  if v_texte_evt != '' and v_nb_meca > 0 then
        code_retour := replace( v_texte_evt, '[attaquant]', v_perso_nom) ;
        perform insere_evenement(v_perso_cod, v_perso_cod, 54, v_texte_evt, 'O', 'O', null);
  end if;

  -- if code_retour = '' then
  --   code_retour :=  'Pas d’effet de « mécanisme ».';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_modification_qa(integer, numeric, text, json) OWNER TO delain;

