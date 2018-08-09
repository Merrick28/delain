CREATE OR REPLACE FUNCTION public.rattra_m()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	temp integer;
begin
for ligne in
	select obj_cod,gobj_nom from objets,objet_generique
where obj_gobj_cod = gobj_cod
and gobj_tobj_cod in (1,2)
and not exists
(select 1 from perso_objets where perobj_obj_cod = obj_cod)
and not exists
(select 1 from objet_position where pobj_obj_cod = obj_cod)
and not exists
(select 1 from stock_magasin where mstock_obj_cod = obj_cod)
and gobj_deposable = 'O' loop
	delete from perso_objets where perobj_obj_cod = ligne.obj_cod;
	temp := lancer_des(1,22);
	select into temp lieu_cod from lieu
		where lieu_tlieu_cod = 11
		offset temp limit 1;
	insert into stock_magasin (mstock_lieu_cod,mstock_obj_cod) values
		(temp,ligne.obj_cod);
	end loop;
	return 0;
end;
	
		$function$

