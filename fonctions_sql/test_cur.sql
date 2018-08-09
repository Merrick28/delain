CREATE OR REPLACE FUNCTION public.test_cur()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	cur refcursor;
	req text;
	sortie text;
begin
	req := 'select vue_perso4(50)';
	open cur for execute req;
	fetch cur into sortie;
	close cur;
	return sortie;
end;$function$

