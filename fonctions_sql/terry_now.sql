CREATE OR REPLACE FUNCTION public.terry_now()
 RETURNS text
 LANGUAGE plpgsql
AS $function$
declare
code_retour char;

begin
code_retour := 'a';

update perso set perso_dlt=now() where perso_actif='O';

return code_retour;
end;
$function$

