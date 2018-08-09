CREATE OR REPLACE FUNCTION public.init_automap()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	ligne record;
	temp integer;
	test_donnees integer;
	v_type_mur integer;
	type_lieu integer;
	test2 integer;
begin
delete from donnees_automap;	
for ligne in select pos_cod from positions loop
		test_donnees := 0;
		test2 := 0;
		select into type_lieu lieu_tlieu_cod from lieu,lieu_position
			where lpos_pos_cod = ligne.pos_cod
			and lpos_lieu_cod = lieu_cod;
		if found then
			temp := 2;
			if type_lieu = 2 then
				temp := 4;
			elsif type_lieu = 8 then
				temp := 6;
			elsif type_lieu = 12 then
				temp := 5;
			elsif type_lieu = 10 then
				temp := 7;
			elsif type_lieu = 3 then
				temp := 7;
			elsif type_lieu = 16 then
				temp := 7;
			elsif type_lieu = 22 then
				test2 := 1;
			end if;
			if test2 = 0 then
				insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue,dauto_type_bat)
					values (ligne.pos_cod,temp,8,type_lieu);
			else
				insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,0,8);
			end if;
				test_donnees := 1;
			if type_lieu = 19 then
				delete from donnees_automap where dauto_pos_cod = ligne.pos_cod;
				insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,1,999);	
			end if;
		end if;
		select into temp mur_pos_cod from murs
			where mur_pos_cod = ligne.pos_cod;
		if found then
			select into v_type_mur mur_type
				from murs
				where mur_pos_cod = ligne.pos_cod;
			delete from donnees_automap 
				where dauto_pos_cod = ligne.pos_cod;
			insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,1,v_type_mur);	
				test_donnees := 1;
		end if;
		if test_donnees = 0 then
			insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,0,8);
		end if;
	end loop;
	return 0;
end;$function$

CREATE OR REPLACE FUNCTION public.init_automap(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour integer;
	ligne record;
	temp integer;
	test_donnees integer;
	v_type_mur integer;
	type_lieu integer;
	test2 integer;
v_etage alias for $1;
begin
delete from donnees_automap where exists
(select 1 from positions where pos_etage = v_etage
and dauto_pos_cod = pos_cod);	
for ligne in select * from positions where pos_etage = v_etage loop
		test_donnees := 0;
		test2 := 0;
		select into type_lieu lieu_tlieu_cod from lieu,lieu_position
			where lpos_pos_cod = ligne.pos_cod
			and lpos_lieu_cod = lieu_cod;
		if found then
			temp := 2;
			if type_lieu = 2 then
				temp := 4;
			end if;
			if type_lieu = 8 then
				temp := 6;
			end if;
			if type_lieu = 12 then
				temp := 5;
			end if;
			if type_lieu = 10 then
				temp := 7;
			end if;
			if type_lieu = 3 then
				temp := 7;
			end if;
			if type_lieu = 16 then
				temp := 7;
			end if;
			if type_lieu = 22 then
				test2 := 1;
			end if;
			if test2 = 0 then
				insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue,dauto_type_bat)
					values (ligne.pos_cod,temp,8,type_lieu);
			else
				insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,0,8);
			end if;
			test_donnees := 1;
			if type_lieu = 19 then
				delete from donnees_automap where dauto_pos_cod = ligne.pos_cod;
				insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,1,999);	
			end if;
		end if;
		select into temp mur_pos_cod from murs
			where mur_pos_cod = ligne.pos_cod;
		if found then
delete from donnees_automap 
				where dauto_pos_cod = ligne.pos_cod;			
select into v_type_mur mur_type
				from murs
				where mur_pos_cod = ligne.pos_cod;
			insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,1,v_type_mur);	
				test_donnees := 1;
		end if;
		if test_donnees = 0 then
			insert into donnees_automap (dauto_pos_cod,dauto_valeur,dauto_vue)
				values (ligne.pos_cod,0,8);
		end if;
	end loop;
	return 0;
end;$function$

