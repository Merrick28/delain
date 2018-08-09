CREATE OR REPLACE FUNCTION public.init_stock()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* fonction init_stock : remplit les magasins            */
/*********************************************************/
declare
	l_mag record;
	l_objet record;
	qte integer;
	num_objet integer;
	num_gobj integer;
	temp integer;
	
begin
	for l_mag in select * from lieu where lieu_tlieu_cod in (11,14,21) loop
		for l_objet in select * from stock_magasin_defaut where dmstock_lieu_cod = l_mag.lieu_cod loop
			temp := 0;
			while temp <= l_objet.dmstock_qte loop
				num_objet := nextval('seq_obj_cod');
				insert into objets (obj_cod,obj_gobj_cod) values (num_objet,l_objet.dmstock_obj_cod);		
				insert into stock_magasin (mstock_obj_cod,mstock_lieu_cod) values (num_objet,l_mag.lieu_cod);
				temp := temp + 1;
			end loop;
		end loop;
	end loop;
	return 0;
end;$function$

