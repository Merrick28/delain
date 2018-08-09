CREATE OR REPLACE FUNCTION public.init_case_visible_pos(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
  v_pos alias for $1;
  etage integer;
  case_r record;
  case_vue record;
  v_compteur_vis integer;
  v_compteur_inv integer;
  v_pos1 integer;
  v_pos2 integer;
  temp integer;
  is_visible integer;
  code_retour text;
  v_compteur integer;
  v_x integer;
  v_y integer;
  v_mur integer;
  is_mur integer;
begin
  v_compteur_vis := 0;
	v_compteur_inv := 0;
	v_compteur := 0;
	select into etage,v_x,v_y pos_etage,pos_x,pos_y from positions
		where pos_cod = v_pos;
  for case_r in select pos_cod, pos_x, pos_y from positions 
  	where pos_etage = etage
  		and pos_x between (v_x - 12) and (v_x + 12)
  		and pos_y between (v_y - 12) and (v_y + 12) order by pos_cod loop
  	select into v_mur mur_pos_cod
  		from murs
  		where mur_pos_cod = case_r.pos_cod;
  	if found then
  		is_mur := 1;
  	else
  		is_mur := 0;
  	end if;
	  for case_vue in select pos_cod from positions where 
	      pos_etage = etage and 
	      pos_x <= case_r.pos_x + 12 and pos_x >= case_r.pos_x - 12 and
	      pos_y <= case_r.pos_y + 12 and pos_y >= case_r.pos_y - 12 
	      and pos_cod >= case_r.pos_cod
	      loop
	      v_pos1 := case_r.pos_cod;
	      v_pos2 := case_vue.pos_cod;
	      -- on efface l'existant
			
				delete from pos_visible where pvis_pos1 = v_pos1 and pvis_pos2 = v_pos2;

	      if(is_mur = 1) then
	      	insert into pos_visible (pvis_pos1,pvis_pos2,pvis_visible) values (v_pos1,v_pos2,false);
	      else
				-- on regarde si la position est visible
				is_visible := trajectoire_vue(v_pos1,v_pos2);
				if(is_visible = 1) then
					insert into pos_visible (pvis_pos1,pvis_pos2,pvis_visible) values (v_pos1,v_pos2,true);
					v_compteur_vis := v_compteur_vis + 1;
				else
					insert into pos_visible (pvis_pos1,pvis_pos2,pvis_visible) values (v_pos1,v_pos2,false);
					v_compteur_inv := v_compteur_inv + 1;
				end if;
			end if;
	  end loop;
	  v_compteur := v_compteur + 1;
	  if ((v_compteur % 20) = 0) then
	  		raise notice 'nb traite %', v_compteur;
raise notice 'Timestamp %',timeofday();
 		end if;
	 end loop;
	
  code_retour := 'Etage '||trim(to_char(etage,'99999999999'))|| 'traité, '||trim(to_char(v_compteur_vis,'9999999999999'))|| 'visibles ajoutés, '||trim(to_char(v_compteur_inv,'9999999999999'))||' invisible ajoutés';
  return code_retour;
end;$function$

