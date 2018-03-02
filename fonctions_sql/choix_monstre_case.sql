--
-- Name: choix_monstre_etage(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION choix_monstre_etage(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/******************************************************/
/* choix_monstre_etage : choisit un monstre a créer   */
/*  en fonction de l'étage et du poids des mosntres   */
/* on passe l'étage en param 0                        */
/******************************************************/
declare
  code_retour integer;
  v_etage alias for $1;
  poids_total integer;
  res_des integer;
  l_monstre record;
  poids_actu integer;
  nb_monstre integer;
begin
  code_retour := 0;
  poids_actu := 0;
  select into poids_total sum(rmon_poids)
  from repart_monstre
  where rmon_etage_cod = v_etage;
  res_des := lancer_des(1,poids_total);
  for l_monstre in select * from repart_monstre
  where rmon_etage_cod = v_etage
  loop
    if l_monstre.rmon_max > 0 then
      select into nb_monstre count(*)
      from perso
        inner join perso_position on ppos_perso_cod = perso_cod
        inner join positions on pos_cod = ppos_pos_cod
      where perso_gmon_cod = l_monstre.rmon_gmon_cod and perso_actif = 'O' and pos_etage = v_etage;

      if nb_monstre < l_monstre.rmon_max then
        poids_actu := poids_actu + l_monstre.rmon_poids;
      end if;
    else
      poids_actu := poids_actu + l_monstre.rmon_poids;
    end if;

    if poids_actu >= res_des then
      code_retour := l_monstre.rmon_gmon_cod;
      exit;
    end if;
  end loop;
  return code_retour;
end;	$_$;


ALTER FUNCTION public.choix_monstre_etage(integer) OWNER TO delain;

--
-- Name: FUNCTION choix_monstre_etage(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION choix_monstre_etage(integer) IS 'Choisit le monstre à ajouter à l’étage.';
