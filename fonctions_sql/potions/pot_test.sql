--
--
-- Name: pot_test(utilise,integer); Ty FUNCTION; Schema: potions; Owner: delain
--

CREATE or replace FUNCTION potions.pot_test(integer,integer) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  personnage alias for $1;
  cible alias for $2;
  v_nom text;
begin
  select into v_nom perso_nom from perso where perso_cod = personnage;
  return v_nom;
end;$_$;


ALTER FUNCTION potions.pot_test(integer,integer) OWNER TO delain;

