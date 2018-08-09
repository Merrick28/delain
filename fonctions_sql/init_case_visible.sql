CREATE OR REPLACE FUNCTION public.init_case_visible(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
  etage alias for $1;
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
begin
  v_compteur_vis := 0;
	v_compteur_inv := 0;
	v_compteur := 0;
  for case_r in select pos_cod, pos_x, pos_y from positions where pos_etage = etage order by pos_cod loop
	  for case_vue in select pos_cod from positions where 
	      pos_etage = etage and 
	      pos_x <= case_r.pos_x + 12 and pos_x >= case_r.pos_x - 12 and
	      pos_y <= case_r.pos_y + 12 and pos_y >= case_r.pos_y - 12 
	      and pos_cod >= case_r.pos_cod
	      loop
	      v_pos1 := case_r.pos_cod;
	      v_pos2 := case_vue.pos_cod;
			-- on vérifie que pos1 <= pos2
			--if(v_pos1 > v_pos2) then
			--	temp := v_pos1;
			--	v_pos1 := v_pos2;
			--	v_pos2 := temp;
			--end if;
			-- on efface l'existant
			delete from pos_visible where pvis_pos1 = v_pos1 and pvis_pos2 = v_pos2;
			-- on regarde si la position est visible
			is_visible := trajectoire_vue(v_pos1,v_pos2);
			if(is_visible = 1) then
				insert into pos_visible (pvis_pos1,pvis_pos2,pvis_visible) values (v_pos1,v_pos2,true);
				v_compteur_vis := v_compteur_vis + 1;
			else
				insert into pos_visible (pvis_pos1,pvis_pos2,pvis_visible) values (v_pos1,v_pos2,false);
				v_compteur_inv := v_compteur_inv + 1;
			end if;
	  end loop;
	  v_compteur := v_compteur + 1;
	  if ((v_compteur % 100) = 0) then
	  		raise notice 'nb traite %', v_compteur;
			raise notice 'Timestamp %',timeofday();
 end if;
	 end loop;
	
  code_retour := 'Etage '||trim(to_char(etage,'99999999999'))|| 'traité, '||trim(to_char(v_compteur_vis,'9999999999999'))|| 'visibles ajoutés, '||trim(to_char(v_compteur_inv,'9999999999999'))||' invisible ajoutés';
  return code_retour;
end;$function$

