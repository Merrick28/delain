--
-- Name: ea_modification_ea(integer, numeric, text, json); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ea_modification_ea(integer, numeric, text, json) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************/
/* ea_modification_ea                             */
/* Applique les bonus et effectue les actions     */
/* spécifiées lors de l’activation d’une DLT.     */
/* On passe en paramètres:                        */
/*   $1 = source (perso_cod du perso)           */
/*   $2 = Probabilité d’atteindre chaque cible    */
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
  v_nb_ea integer;
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

  -- boucle sur la liste des EA d'étage
  v_nb_ea := 0 ;  -- comptage des ea  declenchés!
  for ligne in (select value from json_array_elements((v_params->>'fonc_trig_ea_etage')::json)  )
  loop
      taux := f_to_numeric(ligne.value->>'taux'::text) / 100 ;
      if (taux=0) or (random() <= taux) then
          -- declenchement de la modification 'ea !!!  "{"ea_cod":"10","rearme":"0","taux":"100"}"
          v_nb_ea := v_nb_ea + 1;
          update fonction_specifique
            set fonc_trigger_param=(COALESCE(fonc_trigger_param::jsonb, '{}'::jsonb)  || (json_build_object( 'fonc_trig_rearme', f_to_numeric(ligne.value->>'rearme'::text)::integer))::jsonb)
            where fonc_gmon_cod is null and fonc_perso_cod is null  and fonc_type='POS' and fonc_cod=f_to_numeric(ligne.value->>'ea_cod'::text)::integer ;

      end if;
  end loop;

  -- On rajoute la ligne d’événements
  if v_texte_evt != '' and v_nb_ea > 0 then
        code_retour := replace( v_texte_evt, '[attaquant]', v_perso_nom) ;
        perform insere_evenement(v_perso_cod, v_perso_cod, 54, v_texte_evt, 'O', 'O', null);
  end if;

  -- if code_retour = '' then
  --   code_retour :=  'Pas d’effet de « mécanisme ».';
  -- end if;

  return code_retour;
end;$_$;


ALTER FUNCTION public.ea_modification_ea(integer, numeric, text, json) OWNER TO delain;

