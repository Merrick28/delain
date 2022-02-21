--
-- Name: action_generique(); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function action_generique() RETURNS text
LANGUAGE plpgsql
AS $$declare
  ligne record;
  code_retour text;
  compt integer;
  temp_tue text;
  l_objet record;
  v_defi record;
begin
  -----------------------------------------------------------------
  -- actions "génériques"
  -----------------------------------------------------------------
  -- effacement des portails
  for ligne in select lieu_cod,lpos_pos_cod from lieu,lieu_position
  where lieu_port_dfin < now() and lpos_lieu_cod = lieu_cod
  loop
    delete from lieu_position where lpos_lieu_cod = ligne.lieu_cod;
    delete from lieu where lieu_cod = ligne.lieu_cod;
    compt := init_automap_pos(ligne.lpos_pos_cod);
  end loop;

  -- hibernations
  for ligne in select compt_cod from compte where compt_ddeb_hiber < now() loop
    code_retour := hibernation(ligne.compt_cod);
  end loop;

  -- dissipations
  for ligne in select perso_cod from perso where perso_dfin < now() and perso_actif = 'O' loop
    temp_tue := dissipation(ligne.perso_cod);
  end loop;

  delete from lock_combat where lock_nb_tours <= 0;
  delete from lock_combat where lock_date + '36 hours'::interval < now();
  delete from auth.session where sess_date + '1 hours'::interval < now();
  delete from auth.demande_temp where (not dtemp_valide) and dtemp_date + '1 hours'::interval < now();
  delete from riposte where riposte_nb_tours <= 0;
  delete from perso_louche where plouche_nb_tours <= 0;
  delete from perso_identifie_objet where pio_nb_tours <= 0;

  -- on efface les objets qui doivent l’être
  for l_objet in
  select * from perso_objets
  where perobj_dfin < now()
  loop
    delete from perso_objets where perobj_cod = l_objet.perobj_cod;
    delete from perso_identifie_objet where pio_obj_cod = l_objet.perobj_obj_cod;
    delete from objets where obj_cod = l_objet.perobj_obj_cod;
  end loop;

  -- coteries
  delete from groupe where not exists
  (select 1 from groupe_perso where pgroupe_groupe_cod = groupe_cod);

  -- Les défis
  for v_defi in
  select defi_cod from defi where defi_statut = 0 and defi_date_debut < now() - '5 days'::interval
  loop
    perform defi_abandonner(v_defi.defi_cod, 'C');
  end loop;

  return 'ok';
end$$;


ALTER FUNCTION public.action_generique() OWNER TO delain;

--
-- Name: FUNCTION action_generique(); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION action_generique() IS 'Diverses actions récurrentes de nettoyage (cron)';
