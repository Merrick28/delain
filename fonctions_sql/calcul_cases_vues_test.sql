--
-- Name: calcul_cases_vues_test(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

create or replace function calcul_cases_vues_test(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$-- Met à jour la liste des associations Case - Case visible pour l'étage passé en paramètre.
-- Version courante: Ne fait que compter.

declare
  etage alias for $1;
  case record;
  case_vue record;
  v_compteur integer;
  v_compteur_temp integer;
begin
  v_compteur := 0;
  for case in select pos_cod, pos_x, pos_y from positions where pos_etage = etage loop
  delete from pos_vue where vpos_pos_cod = case.pos_cod;
  for case_vue in select pos_cod from positions where
    pos_etage = etage and
    pos_x <= case.pos_x + 10 and pos_x >= case.pos_x - 10 and
    pos_y <= case.pos_y + 10 and pos_y >= case.pos_y - 10 and
    trajectoire_vue(case.pos_cod,pos_cod) = 1 loop
    insert into pos_vue (vpos_pos_cod, vpos_vue_cod) values (case.pos_cod, case_vue.pos_cod);
  end loop;
  v_compteur := v_compteur + 1;
end loop;
return v_compteur;
end;$_$;


ALTER FUNCTION public.calcul_cases_vues_test(integer) OWNER TO postgres;