CREATE OR REPLACE FUNCTION public.f_to_numeric(text) RETURNS numeric AS $$
DECLARE x NUMERIC;
BEGIN
    x = $1::NUMERIC;
    RETURN x;
EXCEPTION WHEN others THEN
    RETURN 0;
END;
$$
STRICT
LANGUAGE plpgsql IMMUTABLE;
ALTER FUNCTION public.f_to_numeric(text) OWNER TO delain;