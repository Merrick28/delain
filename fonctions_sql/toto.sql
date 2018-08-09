CREATE OR REPLACE FUNCTION public.toto()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	nom_fam text;
	code_rune integer;
begin
	for ligne in
		select * from objets,objet_generique
		where obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 5 loop
		update objets
			set obj_frune_cod = ligne.gobj_frune_cod where obj_cod = ligne.obj_cod;
	end loop;
	return 'ok';
end;$function$

