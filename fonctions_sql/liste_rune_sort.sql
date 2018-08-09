CREATE OR REPLACE FUNCTION public.liste_rune_sort(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	v_sort alias for $1;
	ligne record;
begin
	code_retour := '';
	for ligne in select gobj_nom_generique,gobj_cod from objet_generique,sort_rune 
		where srune_sort_cod = v_sort
		and srune_gobj_cod = gobj_cod order by gobj_cod loop
		code_retour := code_retour||ligne.gobj_nom_generique||', ';
	end loop;
	
	return rtrim(code_retour,', ');
end;$function$

