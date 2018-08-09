CREATE OR REPLACE FUNCTION public.f_num_obj_echoppe(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare

code_retour integer;
num_obj alias for $1;
begin
select into code_retour sum(mgstock_nombre) from stock_magasin_generique where mgstock_gobj_cod = num_obj;
return code_retour;
end;

$function$

