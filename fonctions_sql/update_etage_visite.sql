CREATE OR REPLACE FUNCTION public.update_etage_visite(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	v_vet_cod integer;
	v_perso alias for $1;
	v_etage alias for $2;
begin
	select into v_vet_cod vet_cod from etage_visite
		where vet_perso_cod = v_perso
		and vet_etage = v_etage;
	if not found then
		insert into etage_visite (vet_perso_cod,vet_etage)
			values (v_perso,v_etage);
	end if;
	return 0;
end;
$function$

