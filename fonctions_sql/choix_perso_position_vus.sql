CREATE OR REPLACE FUNCTION public.choix_perso_position_vus(integer)
 RETURNS SETOF integer
 LANGUAGE plpgsql
AS $function$declare
  code_retour text;
  v_pos alias for $1;
  personnage integer;
  ligne record;
  v_req text;
  curs2 refcursor;
  etage_map integer;
  table_map text;

begin
  code_retour := '';
  select into etage_map etage_cod from etage,positions where etage_numero = pos_etage and pos_cod = v_pos;
  table_map := 'perso_vue_pos_'||trim(to_char(etage_map,'99999999999999'));
  v_req = 'select
			pvue_perso_cod
			from '||table_map||'
			where pvue_pos_cod = '||trim(to_char(v_pos,'999999999'));
  open curs2 for execute v_req;
  loop
    fetch curs2 into personnage;
    exit when NOT FOUND;
    if personnage is not null then
      return next personnage;
    end if;
  end loop;
  close curs2;
  return;
end;$function$

