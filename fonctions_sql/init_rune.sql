CREATE OR REPLACE FUNCTION public.init_rune()
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************************/
/* fonction init_rune                                  */
/*******************************************************/
declare
	code_retour text;
	ligne record;
	v_pos integer;
	v_rune integer;
	v_objet integer;
begin
	code_retour := '';
	delete from sort_rune;
	for ligne in select * from sorts loop
		for v_pos in 1..6 loop
			v_rune := to_number(substr(ligne.sort_combinaison,v_pos,1),'9');
			if ((v_rune != 0) and (v_rune != 9) and (v_rune != 8)) then
				select into v_objet gobj_cod from objet_generique
					where gobj_rune_position = v_rune
					and gobj_frune_cod = v_pos;
				if not found then
					code_retour := 'Sort '||trim(to_char(ligne.sort_cod,'9999'))||' pos '||trim(to_char(v_pos,'999'))||' rune '||trim(to_char(v_rune,'9999'));
					return code_retour;
				end if;
				insert into sort_rune(srune_sort_cod,srune_gobj_cod) values (ligne.sort_cod,v_objet);
				code_retour := code_retour||trim(to_char(v_objet,'9999'))||';';
			end if;
		end loop;
	end loop;
	return code_retour;
end;$function$

