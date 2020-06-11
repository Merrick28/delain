CREATE or REPLACE FUNCTION public.bonus_type_carac(character varying(4)) RETURNS character varying(4)
    LANGUAGE plpgsql
AS
$_$
declare
  v_bonus alias for $1;

begin

  return CASE
          WHEN v_bonus='FO2' THEN 'FOR'
          WHEN v_bonus='IN2' THEN 'INT'
          WHEN v_bonus='DE2' THEN 'DEX'
          WHEN v_bonus='CO2' THEN 'CON'
          ELSE v_bonus END ;

end;
$_$;
ALTER FUNCTION public.bonus_type_carac(character varying(4)) OWNER TO delain;