CREATE OR REPLACE FUNCTION public.cree_stat()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	nb_compte integer;
begin
	code_retour := '';
	select count(compt_cod) into nb_compte from compte where compt_actif != 'N';
	insert into stats_detail (dstat_valeur,dstat_stat_cod) values (nb_compte,1);
	return code_retour;
end;$function$

