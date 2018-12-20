--
-- Name: purge_evenements(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION purge_evenements(integer) RETURNS text
LANGUAGE plpgsql
AS $$/*************************************************/
/* fonction purge_evenements                     */
/*   efface les evenments antérieur a la date    */
/*   du jour - parametre 6                       */
/*************************************************/
/* retourne un entier 0                          */
/*************************************************/
declare
  code_retour text;
  temp interval;
  test integer;
  ligne record;
  test_t text;

begin
  temp := trim(to_char(getparm_n(6),'99999999999'))||' days';
  delete from ligne_evt where levt_date <= now() - temp;
  delete from flicage where flic_date <= now() - '90 days'::interval;
  delete from multi_trace where multi_date <= now() - temp;
  get diagnostics test = row_count;
  code_retour := trim(to_char(test,'9999999'))||' évènements supprimés, ';
  test := 0;
  for ligne in select * from perso where perso_actif = 'N' and not exists	(select 1 from ligne_evt where (levt_perso_cod1 = perso_cod or levt_attaquant = perso_cod or levt_cible = perso_cod)) loop
    test_t :=  efface_perso(ligne.perso_cod);
    test := test + 1;
  end loop;
  code_retour := code_retour||trim(to_char(test,'9999999'))||' persos supprimés';
  --code_retour := trim(to_char(test,'9999999'))||' persos supprimés';
  return code_retour;
end;



$$;


ALTER FUNCTION public.purge_evenements(integer) OWNER TO delain;