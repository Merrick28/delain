CREATE OR REPLACE FUNCTION public.choix_lieu_vus(integer, integer)
 RETURNS SETOF integer
 LANGUAGE plpgsql
AS $function$declare
  code_retour text;
  personnage alias for $1;
  type_lieu alias for $2;
  v_pos integer;
  v_lieu integer;
  ligne record;
  v_req text;
  curs2 refcursor;
begin
  code_retour := '';
  for ligne in select tablename from pg_tables where tableowner = 'delain'
                                                     and tablename like 'perso_vue_pos%' loop
    v_req = 'select
			lpos_pos_cod,lpos_lieu_cod
			from lieu,lieu_position,'||ligne.tablename||'
			where pvue_perso_cod = '||trim(to_char(personnage,'99999999999'))||'
			and pvue_pos_cod =lpos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_tlieu_cod = '||trim(to_char(type_lieu,'99999999999'));
    open curs2 for execute v_req;
    loop
      fetch curs2 into v_pos;
      exit when NOT FOUND;
      if v_pos is not null then
        return next v_pos;
      end if;
    end loop;
    close curs2;
  end loop;

  return;
end;
$function$

