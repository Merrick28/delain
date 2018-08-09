CREATE OR REPLACE FUNCTION public.init_stock_mag()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction init_stock_mag : remplit les magasins            */
/*************************************************************/
declare
	l_mag record;
	l_objet record;
	qte integer;
	num_objet integer;
	num_gobj integer;
	temp integer;
	comptage integer;
	code_retour text;
	nb_rune integer;
	
begin
	comptage := 0;
	update lieu set lieu_date_refill = now() - '10 days'::interval where lieu_tlieu_cod = 14 and lieu_date_refill is NULL;
	for l_mag in select * from lieu
		where lieu_tlieu_cod = 14 
		and lieu_date_refill + '10 days'::interval < now() loop
		select into nb_rune count(mstock_obj_cod)
			from stock_magasin
			where mstock_lieu_cod = l_mag.lieu_cod;
		if nb_rune <= 500 then
		if lancer_des(1,100) < 10 then
			comptage := comptage + 1;
update lieu set lieu_date_refill = now() where lieu_cod = l_mag.lieu_cod;
			for l_objet in select * from stock_mag_magique_defaut loop
					temp := 0;
					while temp <= l_objet.dmstock_qte loop
						num_objet := nextval('seq_obj_cod');
						insert into objets (obj_cod,obj_gobj_cod) values (num_objet,l_objet.dmstock_obj_cod);		
						insert into stock_magasin (mstock_obj_cod,mstock_lieu_cod) values (num_objet,l_mag.lieu_cod);
						temp := temp + 1;
					end loop;
				end loop;
		end if;
		end if;
	end loop;
	code_retour := trim(to_char(comptage,'99999999999'))||' magasins magiques remplis.';
	return code_retour;
end;$function$

