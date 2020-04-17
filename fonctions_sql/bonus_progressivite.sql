--
-- Name: bonus_progressivite(text, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE function bonus_progressivite(text, numeric) RETURNS text
LANGUAGE plpgsql
AS $_$-- calcul la progressivité d'un bonus si cumulatif
-- $1 = Le type de bonus
-- $2 = La valeur du bonus


declare
  v_bonus alias for $1;
  v_valeur alias for $2;

  v_progres int;
  v_retour text;
  v_counter int;
  v_degressivite integer;

begin

  v_retour:='O';

  select tbonus_degressivite into v_degressivite from bonus_type where tbonus_libc = v_bonus AND tbonus_cumulable='O' AND COALESCE(tbonus_degressivite,0) > 0 ;
  if found then
    v_retour := v_degressivite::text || '% (' || v_valeur::text;
    v_counter := 0 ;
    v_progres := v_valeur;

    loop

      v_valeur := v_valeur * v_degressivite / 100 ;

      EXIT WHEN (floor(abs(v_valeur)) = 0  or v_counter>9 );
      v_counter := v_counter + 1 ;

      v_progres := v_progres + (sign(v_valeur) * floor(abs(v_valeur)));
      v_retour := v_retour || ',' || v_progres::text;

    end loop ;
    v_retour := v_retour || ')' ;

  end if;


  return v_retour;
end;$_$;


ALTER FUNCTION public.bonus_progressivite(text, numeric) OWNER TO delain;