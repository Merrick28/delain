--
-- Name: ajout_comp(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajout_comp(integer) RETURNS integer
LANGUAGE plpgsql
AS $$
declare
  ligne_perso record;
  test_perso integer;
  force integer;
  intellect integer;
  dex integer;
  temp integer;
  temp2 integer;
begin
  for ligne_perso in select * from perso loop
    select into force,dex perso_int,perso_dex
    from perso where perso_cod = ligne_perso.perso_cod;
    temp := 25 + 2 * (force - 10);
    select into temp2 pcomp_cod from perso_competences where pcomp_perso_cod = ligne_perso.perso_cod
                                                             and pcomp_pcomp_cod = 87;
    if not found then
      insert into perso_competences values (nextval('seq_pcomp'),ligne_perso.perso_cod,87,temp);
    end if;
  end loop;
  return 0;
end;





$$;


ALTER FUNCTION public.ajout_comp(integer) OWNER TO delain;