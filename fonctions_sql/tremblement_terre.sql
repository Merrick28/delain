CREATE OR REPLACE FUNCTION public.tremblement_terre()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	temp_pos integer;
begin
	for ligne in
		select perso_cod,pos_etage,ppos_cod,pos_x,pos_y,pos_cod
		from perso,perso_position,positions
		where ppos_perso_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso != 3
		and not exists
		(select 1 from lieu,lieu_position
		where ppos_pos_cod = lpos_pos_cod
		and lpos_lieu_cod = lieu_cod
		and lieu_refuge = 'O') loop
		delete from lock_combat
			where lock_attaquant = ligne.perso_cod;
		delete from lock_combat
			where lock_cible = ligne.perso_cod;
		if(ligne.pos_etage in (0,-1,-2,-3,-4,-5)) then
			update perso_position
				set ppos_pos_cod = pos_aleatoire(ligne.pos_etage)
				where ppos_cod = ligne.ppos_cod;
		else
			select into temp_pos
				pos_cod
				from positions
				where pos_etage = ligne.pos_etage
				and pos_x between (ligne.pos_x - 3) and (ligne.pos_x + 3)
				and pos_y between (ligne.pos_y - 3) and (ligne.pos_y + 3)
				and not exists
					(select 1 from murs
					where mur_pos_cod = pos_cod)
				and trajectoire_vue_murs(ligne.pos_cod,pos_cod) = 1
				order by random() limit 1;
			update perso_position
				set ppos_pos_cod = temp_pos
				where ppos_cod = ligne.ppos_cod;
		end if;
		end loop;
	return 'tremblement terminé';
end;$function$

CREATE OR REPLACE FUNCTION public.tremblement_terre(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	temp_pos integer;
	v_etage alias for $1;
	v_compteur integer;
begin
	v_compteur := 0;
	for ligne in
		select perso_cod,pos_etage,ppos_cod,pos_x,pos_y,pos_cod
		from perso,perso_position,positions
		where ppos_perso_cod = perso_cod
		and perso_actif = 'O'
		and ppos_pos_cod = pos_cod
		and perso_type_perso not in (2,3)
		and not exists
		(select 1 from lieu,lieu_position
		where ppos_pos_cod = lpos_pos_cod
		and lpos_lieu_cod = lieu_cod
		and lieu_refuge = 'O') 
		and pos_etage = v_etage
		loop
		delete from lock_combat
			where lock_attaquant = ligne.perso_cod;
		delete from lock_combat
			where lock_cible = ligne.perso_cod;
		if(ligne.pos_etage in (0,-1,-2,-3,-4,-5)) then
			update perso_position
				set ppos_pos_cod = pos_aleatoire(ligne.pos_etage)
				where ppos_cod = ligne.ppos_cod;
		else
			select into temp_pos
				pos_cod
				from positions
				where pos_etage = ligne.pos_etage
				and pos_x between (ligne.pos_x - 3) and (ligne.pos_x + 3)
				and pos_y between (ligne.pos_y - 3) and (ligne.pos_y + 3)
				and not exists
					(select 1 from murs
					where mur_pos_cod = pos_cod)
				and trajectoire_vue_murs(ligne.pos_cod,pos_cod) = 1
				order by random() limit 1;
			update perso_position
				set ppos_pos_cod = temp_pos
				where ppos_cod = ligne.ppos_cod;
		end if;
		v_compteur := v_compteur + 1;
		 if ((v_compteur % 100) = 0) then
	  		raise notice 'nb traite %', v_compteur;
			raise notice 'Timestamp %',timeofday();
 end if;
		end loop;
	return 'tremblement terminé';
end;$function$

