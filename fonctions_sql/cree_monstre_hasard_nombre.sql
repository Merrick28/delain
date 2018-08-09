CREATE OR REPLACE FUNCTION public.cree_monstre_hasard_nombre(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/******************************/
/* cree_monstre_hasard_nombre */
/******************************/
declare
	v_gmon_cod alias for $1;
	v_etage alias for $2;
	v_nombre alias for $3;
	temp integer;
	temp2 integer;
begin
	for temp in 1..v_nombre loop
		temp2 := cree_monstre_hasard(v_gmon_cod,v_etage);
	end loop;
	return 'monstres créés';
end;$function$

